(function($) {

	window.cLocator = window.cLocator || {};

	var map = map || {
		Map: {},
		markers: {},
		located: {},
		infoBubble: false,
		bounds: new google.maps.LatLngBounds(),
		markerClusterer: false,
		needsFit: true,
		isPanned: false,
		formIndex: 0
	};

	/**
	 * Marker
	 *
	 * An individual marker. Passed either an object of arbitrary
	 * data or an existing RichMarker instance. Create the marker
	 * if needed or simply show an existing.
	 */

	cLocator.Marker = function(id, marker) {
		if ( map.markers.hasOwnProperty(id) ) {
			this.data = map.markers[id].meta;
			this.marker = map.markers[id];

			this.marker.setVisible(true);
		} else {
			this.data = marker;
			this.create();
		}

		this.setBounds();

		return {
			marker: this.marker,
			infoBubble: this.getInfoBubbleContent()
		}
	}

	cLocator.Marker.prototype.create = function() {
		if ( ! this.data || ! this.data.lat ) {
			return;
		}

		this.marker = new google.maps.Marker({
			position: new google.maps.LatLng( this.data.lat, this.data.lng ),
			meta: this.data
		});

		this.setInfoBubble();
	}

	cLocator.Marker.prototype.setBounds = function() {
		map.bounds.extend(this.marker.getPosition());
	}

	cLocator.Marker.prototype.setInfoBubble = function() {
		var self = this;

		google.maps.event.addListener(this.marker, 'click', function() {
			window.location.href = this.meta.link;
		});

		google.maps.event.addListener(this.marker, 'mouseover', function() {
			if ( map.infoBubble.isOpen_ && map.infoBubble.getContent() == self.getInfoBubbleContent() ) {
				return;
			}

			map.infoBubble.setContent(self.getInfoBubbleContent());
			map.infoBubble.open(map.Map, self.marker);
		});
	}

	cLocator.Marker.prototype.getInfoBubbleContent = function() {
		var content = '<a href="' + this.data.link + '">' + this.data.title + '</a>';

		return content;
	}

	/**
	 * Markers
	 *
	 * Passed an object of jQuery objects (via cLocator.Results)
	 * Create a new marker if one does not exist for that result.
	 */

	cLocator.Markers = function() {
		if ( ! map.infoBubble ) {
			map.infoBubble = new InfoBubble({
				backgroundClassName: 'map-marker-info',
				borderRadius: 4,
				padding: 0,
				borderColor: '#ffffff',
				shadowStyle: 0,
				minWidth: 250,
				maxWidth: 250,
				minHeight: 67,
				hideCloseButton: true,
				flat: true
			});
		}
	}

	cLocator.Markers.prototype.hideAll = function() {
		if ( $.isEmptyObject( map.markers ) ) {
			return;
		}

		// reset bounds
		map.bounds = new google.maps.LatLngBounds();

		// any open InfoBubbles
		map.infoBubble.close();

		// any existing clusters
		if ( map.markerClusterer ) {
			map.markerClusterer.clearMarkers();
		}

		// any visible markers
		$.each( map.markers, function(i, marker) {
			map.markers[i].setVisible(false);
		});
	}

	cLocator.Markers.prototype.place = function(results) {
		this.hideAll();

		this.results = results;

		if ( $.isEmptyObject( this.results ) ) {
			return map.Stage.showDefault();
		}

		$.each(this.results, function(i, result) {
			var markerObj = new cLocator.Marker(i, result);

			map.markers[i] = markerObj.marker;

			if ( map.markers[i].getVisible() ) {
				map.markers[i].setMap(map.Map);
			} else {
				map.markers[i].setMap(null);
			}
		});

		if ( map.needsFit === true ) {
			this.fitBounds();
		}

		if ( map.Stage.settings.useClusters ) {
			this.createClusters();
		}
	}

	cLocator.Markers.prototype.fitBounds = function() {
		map.needsFit = false;

		map.Map.fitBounds( map.bounds );
		map.Map.setZoom( map.Map.getZoom() );
	}

	cLocator.Markers.prototype.createClusters = function() {
		var self = this;

		map.markerClusterer = new MarkerClusterer(
			map.Map,
			map.markers,
			{
				ignoreHidden: true,
				maxZoom: map.Stage.settings.mapOptions.maxZoom,
				gridSize: parseInt( map.Stage.settings.gridSize ),
				imagePath: ''
			}
		);

		google.maps.event.addListener(map.markerClusterer, 'click', function(c) {
			self.clusterOverlay(c);
		});
	}

	cLocator.Markers.prototype.clusterOverlay = function(c) {
		var markers = c.getMarkers();
		var zoom = map.Map.getZoom();

		if ( zoom < map.Stage.settings.mapOptions.maxZoom ) {
			return;
		}

		var p = [];

		for ( i = 0; i < markers.length; i++ ) {
			var marker = new cLocator.Marker(markers[i].meta.id, markers[i]);

			p.push(marker.infoBubble);
		};

		var title = map.Stage.settings.title.replace( '%d', p.length );

		$.magnificPopup.open({
			items: {
				src: '<div class="modal"><h2 class="modal-title">' + title + '</h2><ul class="cluster-items"><li class="map-marker-info">' +
						p.join( '</li><li class="map-marker-info">' ) +
					'</li></ul></div>',
				type: 'inline'
			}
		});
	}

	/**
	 * Results
	 *
	 * Parse a list of HTML elements and extra the necessary information
	 * we will use to help place the results.
	 *
	 * The indididual result objects are not markers at this point, just information.
	 */

	cLocator.Results = function() {

	}

	cLocator.Results.prototype.parse = function() {
		var self = this;

		var section = map.Stage.$target.eq( map.formIndex );

		this.results = {};
		this.items = section.find( '.type-' + map.Stage.type );

		$.each( this.items, function(i, el) {
			var $el = $(el);

			if ( ! ( $el.data( 'longitude' ) && $el.data( 'latitude' ) ) ) {
				return;
			}

			var data = {
				id:     $el.attr( 'id' ),
				lat:    $el.data( 'latitude' ),
				lng:    $el.data( 'longitude' ),
				link:   $el.data( 'href' ),
				title:  $el.data( 'title' )
			}

			self.results[data.id] = data;
		});

		map.Markers.place(this.results);
	}

	cLocator.Results.prototype.refresh = function() {
		map.Stage.$target.trigger( 'update_results', [ 1, false ] );
	}

	/**
	 * Stage
	 *
	 * Initialize the map and set up the rest of our functionality.
	 */

	cLocator.Stage = function(type, settings) {
		var defaults = {
			useClusters: true,
			mapOptions: {
				center: new google.maps.LatLng(41.850033, -87.6500523),
				zoom: 3,
				maxZoom: 17,
				scrollwheel: false,
				panControl: false,
				scaleControl: false,
				overviewMapControl: false
			}
		}

		this.settings = $.extend( true, {}, defaults, settings );
		this.type = type;
		this.$target = $( 'div.' + this.type + 's[data-show_filters="true"]' );
		this.$results = $( 'ul.' + this.type + 's' );
		this.canvas = this.type + '-map-canvas';

		// Validate some settings
		this.settings.mapOptions.zoom = parseInt( this.settings.mapOptions.zoom );
		this.settings.mapOptions.maxZoom = parseInt( this.settings.mapOptions.maxZoom );

		if ( $.isArray( this.settings.mapOptions.center ) ) {
			var center = this.settings.mapOptions.center;
			map.needsFit = false;

			this.settings.mapOptions.center = new google.maps.LatLng( center[0], center[1] );
		}

		if ( ! document.getElementById( this.canvas ) ) {
			return;
		}

		google.maps.event.addDomListener( window, 'load', this.create() );

		map.currentLocation = this.$target.find( $( '#search_location' ) ).val();

		map.Results = new cLocator.Results();
		map.Markers = new cLocator.Markers();
		map.Stage   = this;
	}

	cLocator.Stage.prototype.create = function() {
		map.Map = new google.maps.Map(
			document.getElementById( this.canvas ),
			this.settings.mapOptions
		);

		var self = this;

		this.adjustSize();

		$(window).on( 'resize', function() {
			self.adjustSize();
		});

		this.bindEvents();
	}

	cLocator.Stage.prototype.adjustSize = function() {
		var $map  = $( '#' + this.canvas );
		var mapH  = $map.outerHeight();
		var $win  = $(window);
		var winH  = $win.outerHeight();
		var extraH = 0;

		if ( $( 'body' ).hasClass( 'page-template-page-templatesmap-jobs-php' ) || $( 'body' ).hasClass( 'page-template-page-templatesmap-resumes-php' ) ) {
			extraH = $( '.site-header' ).outerHeight() + 80;
		} else {
			extraH = $( '.site-header' ).outerHeight() + $( '.job_filters' ).outerHeight() + 50;
		}

		if ( ( mapH + extraH ) > winH ) {
			var h = winH - extraH; 

			if ( h > 500 ) {
				return;
			}

			$( '.job_listing-map-wrapper, .job_listing-map, .resume-map-wrapper, .resume-map' ).css( 'height', h );
		}
	}

	cLocator.Stage.prototype.showDefault = function() {
		if ( '' == map.GeoCode.getCurrentLocation() ) {
			map.Map.setCenter(map.Stage.settings.mapOptions.center);
			map.Map.setZoom(map.Stage.settings.mapOptions.zoom);
		} else {
			map.Map.setCenter(map.GeoCode.getCurrentLocation());
		}
	}

	cLocator.Stage.prototype.bindEvents = function() {
		var self = this;

		/* When more jobs are loaded refit the bounds */
		$( '.load_more_jobs' ).click(function(e) {
			map.needsFit = true;
		});

		self.$target.on( 'update_results', function(event) {
			map.formIndex = self.$target.index(this);
		});

		/** When the results have been loaded parse/place the pins */
		self.$target.on( 'updated_results', function(event, result) {
			map.Results.parse();
		});

		/** When the map is clicked hide all info bubbles */
		google.maps.event.addListener(map.Map, 'click', function() {
			map.action = 'click';

			map.infoBubble.close();
		});

		/** When the map is zoomed close any infobubbles */
		google.maps.event.addListener(map.Map, 'zoom_changed', function() {
			map.infoBubble.close();
		});
	}

	/**
	 *  Get the height of the anchor
	 *
	 *  This function is a hack for now and doesn't really work that good, need to
	 *  wait for pixelBounds to be correctly exposed.
	 *  @private
	 *  @return {number} The height of the anchor.
	 */
	InfoBubble.prototype.getAnchorHeight_ = function() {
	 	return 45;
	};

})(jQuery);

<?php

  // Interview names


  $arr_interviewname[1]='CADD Interview';
  $arr_interviewname[2]='Project Manager Interview';
  $arr_interviewname[3]='Architect Interview';
  $arr_interviewname[4]='Behavioral Interview';
  $arr_interviewname[5]='Organizational Fit Interview';

  $arr_interviewintro[1]='The following is an on-line CADD Interview.<br />Please respond to each question/inquiry honestly and in ample detail.';
  $arr_interviewintro[2]='The following is an on-line Project Manager Interview.<br />Please respond to each question/inquiry honestly and in ample detail.';
  $arr_interviewintro[3]='The following is an on-line Architect Interview.<br />Please respond to each question/inquiry honestly and in ample detail.';
  $arr_interviewintro[4]='The following is an on-line Behavioral Interview.<br />Please respond to each question/inquiry honestly and in ample detail.';
  $arr_interviewintro[5]='The following survey is a measure of organizational culture and values.<br />This measure is a secondary selection measure that allows Archipro Staff Agency to maximize the fit between characteristics of the organization and desired working conditions of the applicants.  Please answer each question honestly and to the best of your ability as they apply to your organization.';


  // interview form fields, format:
  // [required]|[type]|[params]|[text]
  //
  // required: 0 - not, 1 - yes
  // type: 1 - text area big, params = [width]~[height]
  //       2 - drop down box, params = [option1]~[option2]...
  //       3 - text box, params = [width]~[maxlength]
  //       4 - drop downs group, params = [option1]~[option2]...[optionN]^[itemname1]~[itemname2]....[itemnameM]
  //       5 - radio group, params = [option1]~[option2]...
  //       6 - text label, not a field, params = empty

  $arr_interfields[1][1] ='1|2|No Experience~Novice~Intermediate~Advanced|Please rate your proficiency in AutoCADD:';
  $arr_interfields[1][2] ='1|1|520~8|Please describe an instance in which you were asked to use X-refs for a given task. How did you do so?';
  $arr_interfields[1][3] ='1|1|520~8|Suppose you are producing a sheet for a set of drawings and notice a significant design flaw. You have a deadline in three days but know that the discovered flaw could be of serious consequence. You know that your Project Manager will be very angry upon learning of the flaw and will likely require you and the rest of your team to work around the clock in order to fix it. How would you respond in this situation?';
  $arr_interfields[1][4] ='1|1|520~8|In any work, there are always instances when we are required to work with individuals we do not like.  Please describe an instance in which you were required to work with someone with whom you did not get along. How did you behave in this situation? How did this affect the project? What was the final outcome?';
  $arr_interfields[1][5] ='1|1|520~8|You are working on a set of construction documents when your Project Manager informs you that she needs the set complete in three days, instead of five days as you had originally thought.  You are slightly ahead of schedule, but to complete the drawings in three days will require picking up the pace significantly and working overtime.  However, even this will be difficult without an additional person being added to the project.  You know that there are several coworkers who know the job well enough to help, but to do so would require that they be taken off of whatever projects they are currently working on.  How would you handle this situation?';
  $arr_interfields[1][6] ='1|1|520~8|Describe a situation in which you were asked to do something in AutoCADD that you did not know how to do.  How did you handle this situation?  What was the outcome?';
  $arr_interfields[1][7] ='1|1|520~8|Please describe a time when you worked through a conflict of opinion with a coworker or manager by either reaching a compromise or persuading them.  What was the outcome?  How did the project benefit?';
  $arr_interfields[1][8] ='1|1|520~8|Please describe a time when miscommunication among yourself and your coworkers or managers negatively impacted a project on which you were working?  How did you remedy the situation?  What did you learn from the experience?';
  $arr_interfields[1][9] ='1|1|520~8|Please describe a time when you were faced with a very difficult deadline.  What did you do to try to meet this deadline?  Did the quality of the project suffer?';
  $arr_interfields[1][10]='1|1|520~8|Please describe a time when you were asked to use details for a given project.  Did you use existing details or were you required to develop them from scratch?  If you had to develop them, how did you do so?';
  $arr_interfields[1][11]='1|1|520~8|Please describe an instance in which you witnessed a co-worker doing something wrong on AutoCADD.  Did you bring it to his/her attention?  What was his/her reaction?  What was the outcome?';
  $arr_interfields[1][12]='1|1|520~8|You are assigned a project with a near-impossible deadline.  You are confident that you can complete the drawings on time, but worry that quality may suffer.  You are faced with a dilemma: either meet the deadline at the expense of quality, or deliver a quality project but miss the deadline.  How would you handle this situation?  With whom would you discuss these options and how would you make the decision?';
  $arr_interfields[1][13]='1|1|520~8|Please describe your greatest strengths:';
  $arr_interfields[1][14]='1|1|520~8|Please describe your greatest weaknesses:';

  $arr_interfields[2][1] ='1|2|No Experience~Novice~Intermediate~Advanced|Please rate your proficiency in AutoCADD:';
  $arr_interfields[2][2] ='1|1|520~8|A consultant calls you, angry that he/she has not been paid for an invoice submitted more than 60 days prior and threatens to stop work until the money has been received.  You know that the client has paid you, but the current cash flow has delayed your payment to the consultant.  When you ask your bookkeeper, she tells you that it will be at least four days until she can cut a check for the requested amount.  You have a deadline in 2 days that cannot be met without the consultant\'s work.  How would you handle this situation and ensure that the deadline is met?';
  $arr_interfields[2][3] ='1|1|520~8|Two employees under your supervision get into a shouting match over the messiness of one of the employee\'s workspaces; one of the employees has spread out a number of drawings over his desk, spilling over slightly onto the other employee\'s desk.  The two begin to argue loudly, eventually erupting into a shouting match of insults.  Although the two calm down, the event has left the entire workgroup on edge, a fact that is visible in the quality and quantity of the work produced in the days since.  You can sense that the rift is far from healed and will likely explode soon.  How do you handle this situation?';
  $arr_interfields[2][4] ='1|1|520~8|You are reviewing the site of a high-rise residential building when you notice that the contractor has installed fixtures different from those specified in the drawings; they are clearly a lower quality, less expensive brand.  This contractor has a notorious reputation for trying to use inferior materials than those specified in the drawings.  However, he is currently well ahead of schedule and the client is very happy with the progress. How would you handle this situation?';
  $arr_interfields[2][5] ='1|1|520~8|A consultant submits a "request for additional services" for work that was explicitly included in the contract.  The consultant claims that there was a mistake and that its inclusion was an oversight, thereby causing him to underbid.  He asserts that he cannot produce the work at the price agreed to in the contract and threatens to resign from the project.  If he resigns, the project will be delayed and the blame will fall upon your head.  How would you handle this situation?';
  $arr_interfields[2][6] ='1|1|520~8|You have been working on a large project for nearly three months.  You have completed the schematic design phase and documents, which were approved by the client, and are nearing 50% completion of the construction documents.  The client decides that they would like to alter the design, reducing the square footage by 4000 sq/ft, thereby cutting the budget by 10%.  The budget is already tight and the requested changes would exceed architectural fees by $10,000, thereby eating up any profit and causing the firm to a significant loss on the project.  How would you balance the needs of the client with the profitability of the project?';
  $arr_interfields[2][7] ='1|1|520~8|The firm is confronted with a temporary slowdown, requiring the company to reduce its staff by 20%.  Although this is a normal cyclical fluctuation in the workforce, the remaining employees are noticeably nervous and you fear that several may be searching for new jobs.  Please describe what you perceive to be your role and responsibilities in this process.  How would you handle this situation to help ease the fears and anxieties of the remaining employees?';
  $arr_interfields[2][8] ='1|1|520~8|You are reviewing the work of an architect under your supervision when you discover a costly mistake that threatens to set the project back nearly three weeks.  This is not the first time this architect has made such a mistake; in fact, in the previous year the architect has committed no fewer than two similar mistakes.  Another architect under your supervision has just delivered to you a package of drawings for a different project.  Upon review, the drawings are complete and nearly flawless more than two weeks before the deadline and well ahead of budget.  This architect consistently delivers her projects on-time, under budget, and with minimal problems.  How would you handle each of these architects?';
  $arr_interfields[2][9] ='1|1|520~8|Suppose an intern notices a significant design flaw in a set drawings for one of the company\'s largest clients.  You have  a deadline in three days but know that the discovered flaw could be of serious consequence. You know that the client will be very angry if the project is delayed and the flaw will likely require you and the rest of your team to work around the clock in order to fix it.   How would you respond in this situation?';
  $arr_interfields[2][10]='1|1|520~8|In any work, there are always instances when we are required to work with individuals we do not like.  Please describe an instance in which you were required to work with someone with whom you did not get along.  How did you behave in this situation?  How did this affect the project?  What was the final outcome?';
  $arr_interfields[2][11]='1|1|520~8|Please describe a time when you were faced with a very difficult deadline.  What did you do to try to meet this deadline?  Did the quality of the project suffer?';
  $arr_interfields[2][12]='1|1|520~8|You are assigned a project with a near-impossible deadline.  You are confident that you can complete the drawings on time, but worry that quality may suffer.  You are faced with a dilemma: either meet the deadline at the expense of quality, or deliver a quality project but miss the deadline.  How would you handle this situation?  With whom would you discuss these options and how would you make the decision?';
  $arr_interfields[2][13]='1|1|520~8|Please describe your greatest strengths:';
  $arr_interfields[2][14]='1|1|520~8|Please describe your greatest weaknesses:';

  $arr_interfields[3][1] ='1|2|No Experience~Novice~Intermediate~Advanced|Please rate your proficiency on AutoCADD R14/2000:';
  $arr_interfields[3][2] ='1|1|520~8|Two employees under your supervision get into a shouting match over the messiness of one of the employee\'s workspaces; one of the employees has spread out a number of drawings over his desk, spilling over slightly onto the other employee\'s desk.  The two begin to argue loudly, eventually erupting into a shouting match of insults.  Although the two calm down, the event has left the entire workgroup on edge, a fact that is visible in the quality and quantity of the work produced in the days since.  You can sense that the rift is far from healed and will likely explode soon.  How do you handle this situation?';
  $arr_interfields[3][3] ='1|1|520~8|The firm is confronted with a temporary slowdown, requiring the company to reduce its staff by 20%.  Although this is a normal cyclical fluctuation in the workforce, the remaining employees are noticeably nervous and you fear that several may be searching for new jobs.  Please describe what you perceive to be your role and responsibilities in this process.  How would you handle this situation to help ease the fears and anxieties of the remaining employees?';
  $arr_interfields[3][4] ='1|1|520~8|You are reviewing the work of an intern under your supervision when you discover a costly mistake that threatens to set the project back nearly three weeks.  This is not the first time this intern has made such a mistake; in fact, in the previous year the intern has committed no fewer than two similar mistakes.  Another intern under your supervision has just delivered to you a series of drawings for a different project.  Upon review, the drawings are complete and nearly flawless more than two weeks before the deadline and well ahead of budget.  This intern consistently delivers her projects on-time, under budget, and with minimal problems.  How would you handle each of these employees?';
  $arr_interfields[3][5] ='1|1|520~8|Suppose an intern notices a significant design flaw in a set of drawings for one of the company\'s largest client.  You have a deadline in three days but know that the discovered flaw could be of serious consequence. You know the client will be very angry if the project is delayed and the flaw will likely require you and the rest of your team to work around the clock in order to fix it.  How would you respond in this situation?';
  $arr_interfields[3][6] ='1|1|520~8|In any work, there are always instances when we are required to work with individuals we do not like.  Please describe an instance in which you were required to work with someone with whom you did not get along.  How did you behave in this situation?  How did this affect the project?  What was the final outcome?';
  $arr_interfields[3][7] ='1|1|520~8|Please describe a time when you were faced with a very difficult deadline.  What did you do to try to meet this deadline?  Did the quality of the project suffer?';
  $arr_interfields[3][8] ='1|1|520~8|You are reviewing the site of a high-rise residential building when you notice that the contractor has installed fixtures different from those specified in the drawings; they are clearly a lower quality, less expensive brand.  This contractor has a notorious reputation for trying to use inferior materials than those specified in the drawings.  However, he is currently well ahead of schedule and the client is very happy with the progress. How would you handle this situation?';
  $arr_interfields[3][2] ='1|1|520~8|A consultant submits a "request for additional services" for work that was explicitly included in the contract.  The consultant claims that there was a mistake and that its inclusion was an oversight, thereby causing him to underbid.  He asserts that he cannot produce the work at the price agreed to in the contract and threatens to resign from the project.  If he resigns, the project will be delayed and the blame will fall upon your head.  How would you handle this situation?';
  $arr_interfields[3][2] ='1|1|520~8|You are assigned a project with a near-impossible deadline.  You are confident that you can complete the drawings on time, but worry that quality may suffer.  You are faced with a dilemma: either meet the deadline at the expense of quality, or deliver a quality project but miss the deadline.  How would you handle this situation?  With whom would you discuss these options and how would you make the decision?';
  $arr_interfields[3][11]='1|1|520~8|Please describe your greatest strengths:';
  $arr_interfields[3][12]='1|1|520~8|Please describe your greatest weaknesses:';

  $arr_interfields[4][1] ='1|3|280~255|Current Employer:';
  $arr_interfields[4][2] ='1|3|280~255|Position:';
  $arr_interfields[4][3] ='1|3|280~255|CADr:';
  $arr_interfields[4][4] ='1|1|280~4|Projects:';
  $arr_interfields[4][5] ='1|1|280~4|Responsibilities:';
  $arr_interfields[4][6] ='1|1|280~4|What Technologies were used?';
  $arr_interfields[4][7] ='1|1|280~4|What are you strengths/weaknesses?';
  $arr_interfields[4][8] ='1|1|280~4|What do you see as your next career move?';
  $arr_interfields[4][9] ='1|1|280~4|What would you need to make a change?';
  $arr_interfields[4][10]='1|1|280~4|When can you start if you are offered a position that meets your requirements?';
  $arr_interfields[4][11]='1|2|Any~Full Time~Part Time~Contract~Permanent|What type of work are you interested in?';
  $arr_interfields[4][12]='1|2|1~2~3~4~5|How serious are you about leaving?';
  $arr_interfields[4][13]='1|1|280~4|What if they make you a counteroffer?';
  $arr_interfields[4][14]='1|1|280~4|Tell us about a time when you worked through a conflict of opinion with someone either reaching a compromise or persuading them:';
  $arr_interfields[4][15]='1|1|280~4|How do you feel about working overtime?';
  $arr_interfields[4][16]='1|1|280~4|Where would you like to see yourself five years from now?';
  $arr_interfields[4][17]='1|1|280~4|What do you consider most desirable in a work environment?';

  $arr_interfields[4][18]='0|6||Portfolio Review';
  $arr_interfields[4][19]='1|1|280~4|Which was your favorite project?';
  $arr_interfields[4][20]='1|1|280~4|What Project are you most proud of? Why?';
  $arr_interfields[4][21]='1|1|280~4|What was your role on this project?';
  $arr_interfields[4][22]='1|1|280~4|How often did you interact with the client?';
  $arr_interfields[4][23]='1|1|280~4|How many people were on this team?';
  $arr_interfields[4][24]='1|1|280~4|What phases of the project did you work on?';

  $arr_interfields[4][25]='0|6||Design Philosophy and Technical Ability';
  $arr_interfields[4][26]='1|1|280~4|What is your design Philosophy?';
  $arr_interfields[4][27]='1|1|280~4|How did your current employer contribute to your decision to leave?';
  $arr_interfields[4][28]='1|1|280~4|How do you like to be given direction?';
  $arr_interfields[4][29]='1|1|280~4|What do you find frustrating on the job?';

  $arr_interfields[5][1] ='1|4|1~2~3^Design and innovation.~Service provided to the client.~Delivery of the product on time and within budget.|Consider your ideal organization and the primary values placed upon different aspects of the work.  Please rank the following three dimensions from most valued (1) to least valued (3) by your ideal organization.';
  $arr_interfields[5][2] ='1|4|1~2~3^I enjoy an unstructured work environment where I get to shift between projects frequently.  It provides variety and allows me to learn different skills and work with different people all the time.~I enjoy working as part of a stable team that is responsible for all stages of a project from start to finish.~I enjoy working in stable, specialized departments that allow me to hone my skills and develop a specialized expertise in my field, such as design, schematics, CDs, details, etc.  It allows me to apply my specific skills to contribute to multiple projects within the firm.|Consider your ideal organization and the structure of its staff.  Please rank the following three dimensions from most preferred (1) to least preferred (3).';
  $arr_interfields[5][3] ='1|4|1~2~3^Jim is a third-year intern with extensive knowledge of AutoCADD and design principles.  He graduated in the top of his class from a premier university and has since worked with two of the best firms in the industry.  His references are glowing and you can tell that he is a self-directed individual with good initiative.  He is asking for a salary of $45,000 per year.~Joan is a second-year intern with good basic AutoCADD skills.  Although she lacks experience in many aspects of the job, she clearly has the aptitude to learn quickly and develop the skills necessary to fill the position; with a little bit of training and patience, she could be a strong contributor.  She is asking for a starting salary of $35,000.~Mike is a CADD Technician with a technical degree in computer-aided design from the local community college.  He has two years of experience and possesses good basic knowledge of AutoCADD.  Although lacking design experience, he appears to be a good technical person capable of completing deadlines and simple revisions quite quickly and with minimal error.  He is asking for a salary of $31,000.|Consider your ideal organization.  In hiring new staff, there is always a need to balance experience and cost.  If given a choice between the following three applicants, which should be hired to fill a production-level position (that is, intern or CADD technician).  Please rank the following from most preferred (1) to least preferred (3).';
  $arr_interfields[5][4] ='1|4|1~2~3^"Innovation! We believe in designing unique, design-focused buildings that will leave a mark on the community."~"Whatever you need, we can provide.  We have a long history of working on projects like yours, completing them on-time, on-budget, and to your specifications.  We\'re here to make you, the client, happy."~"We believe in efficiency.  Our expertise allows us to produce and deliver projects at a rate rarely rivaled by our competitors.  We can churn your project out in the shortest period of time at the absolute cheapest price."|Consider your ideal organization.  What would the marketing slogan most likely be?  Please rank the following from most preferred (1) to least preferred (3).';
  $arr_interfields[5][5] ='1|4|1~2~3^ABC International is looking to build a high profile world headquarters in Miami.  They are looking for a cutting-edge design that will capture the attention of any and all who come within view of it.  They want their building to stand out from the rest of the skyline, and to be of such quality that it will garner the attention of the major Architecture magazines.~Dade County School Board has budgeted $40 Million to build 6 new schools over the next year.  They are looking for functional, attractive buildings that meet the rigid design criteria put in place by the school board.  They want hands-on, attentive management who are committed to meeting the needs of the school board.~XYZ restaurants has scheduled to renovate their 40 restaurants in Florida over the next two years.  Although the renovations and design schemes will be standard, each project will require the architect to adjust the criteria to suit each location.  They are looking for quick turnaround and delivery.|Consider your ideal organization and the types of clients and projects they would pursue.  Of the following, which project most closely reflects the typical project pursued by your ideal organization?  Please rank the following from most preferred (1) to least preferred (3).';
  $arr_interfields[5][6] ='1|5|Firm X is run by 4 primary partners, all of whom are very much involved in the day to day management of the firm.  The partners are very close to the projects and frequently act as project manager on multiple projects.  The firm is loosely structured and informal, facilitating a family-like environment.  However, there is a tendency for basic administrative and financial duties to be neglected.~Firm Y is a corporation with 5 partners, but with most power residing within the CEO.  The CEO is removed from much of the projects, only occasionally involving him/herself in specific projects; his/her role is almost purely administrative.  The firm is very formal and hierarchically structured, and most functions are standard and proceduralized.|Consider your ideal organization.  Of the following, what best describes the power structure of your ideal organization?';
  $arr_interfields[5][7] ='1|5|We strive for consensus - we try to ensure that all relevant voices are heard and agree on the course of action to be taken.~Decisions are typically made by individuals according to the hierarchical authority structure.  Decisions are made at the top and passed down to lower levels.|Consider your ideal organization.  What process best describes the decision-making structure of your ideal organization?';
  $arr_interfields[5][8] ='1|5|We follow opportunities, but engage in little formal planning.  The market guides us and our goals change frequently.~Planning is a critical part of our operations.  We frequently engage in formal planning sessions in which specific goals and objects are set for the coming months.  Our plans guide our practices, including marketing, financial, expansion, etc.|Consider your ideal organization.  Which of the following best describes the planning activities of your ideal organization?';
  $arr_interfields[5][9] ='1|5|We hire individuals for the firm, not the project.  We look for long-term, flexible individuals who can work on almost any project in the office.  We focus of fit and longevity.~We hire to meet the needs of the project first.  We look for specific skills, experiences, and specialties that will contribute to the firm\'s ability to delivery the best, most efficient product.|Consider your ideal organization. Which of the following best describes the hiring strategy of your ideal organization?';
  $arr_interfields[5][10]='1|5|We focus on professional quality of projects first, profit second.~We focus on administration and profits; professional quality is essential, but a consideration of the project architect and designer.|Consider your ideal organization.  What best describes the management philosophy of your ideal organization?';
  $arr_interfields[5][11]='1|5|The project is a beautiful, well-designed structure of which we can be proud.~The project was very successful; it was delivered on time and well within budget, yielding a nice profit for the firm.|Consider your ideal organization.  When a project is complete, what of the following is valued more heavily?';


  // Reference fields
  // format - same as interviews
  // [required]|[type]|[params]|[text]

  $arr_reffields[1] ='0|3|280~255|Salary:';
  $arr_reffields[2] ='0|3|280~255|Position title:';
  $arr_reffields[3] ='0|1|280~4|What was the nature of the job?';
  $arr_reffields[4] ='0|1|280~4|What did you think of his/her work?';
  $arr_reffields[5] ='0|2|Good~Fair~Poor|Was the employee\'s level of equipment knowledge?';
  $arr_reffields[6] ='0|1|280~4|How would you describe his/her performance in comparison with other people?';
  $arr_reffields[7] ='0|1|280~4|How did he/she get along with other people?';
  $arr_reffields[8] ='0|1|280~4|What are his/her strong points?';
  $arr_reffields[9] ='0|1|280~4|What are his/her limitations?';
  $arr_reffields[10]='0|1|280~4|Why did he/she leave your company?';
  $arr_reffields[11]='0|2|Yes~No|Are they eligible for rehire?';
  $arr_reffields[12]='0|1|280~4|Is there anything else we should know?';
  $arr_reffields[13]='0|6||Could you comment on his/her:';
  $arr_reffields[14]='0|1|280~4|- professionalism:';
  $arr_reffields[15]='0|1|280~4|- attendance:';
  $arr_reffields[16]='0|1|280~4|- dependability:';
  $arr_reffields[17]='0|1|280~4|- ability to take on responsibility:';
  $arr_reffields[18]='0|1|280~4|- potential for advancement:';
  $arr_reffields[19]='0|1|280~4|- degree of supervision needed:';
  $arr_reffields[20]='0|1|280~4|- overall attitude:';


  // Employer's Organizational Fit Survey
  // format - same as interviews
  // [required]|[type]|[params]|[text]

  $arr_surveyfields[1] ='1|4|1~2~3^Design~Service~Delivery|Consider your organization and the primary values placed upon different aspects of the work.  Please rank the following three dimensions from most valued (1) to least valued (3) by your organization.';
  $arr_surveyfields[2] ='1|4|1~2~3^Projects are typically produced by flexible teams in which staff is frequently rotated in and out based on need, availability, and skill.  Staff is frequently shuffled around from one project to another.~Projects are typically produced by stable teams housed within studios or departments.  When a project is awarded to the firm, it is assigned to a studio or department whose staff is responsible for the complete production of the package.~Projects are broken down into phases, and each phase is assigned to a specialized studio or department.  Production very much follows an assembly-line model; when one department completes their contribution, it is sent on to the next until the project is complete.|Of the following, which best describes the production process of your firm?  Please rank the following from most similar (1) to least similar (3).';
  $arr_surveyfields[3] ='1|4|1~2~3^The Principal makes all decisions pertaining to the projects within the firm.~The Studio Director or Project Manager makes most decisions pertaining to projects under his/her charge.~Most decisions are made according to standardized procedures and responses to specific situations; the firm has developed proceduralized protocol for most major decisions to be made in the course of a project.|Of the following, which best describes how decisions are made for any given project.  Please rank the following from most similar (1) to least similar (3).';
  $arr_surveyfields[4] ='1|4|1~2~3^"Innovation! We believe in designing unique, design-focused buildings that will leave a mark on the community."~"Whatever you need, we can provide.  We have a long history of working on projects like yours, completing them on-time, on-budget, and to your specifications.  We\'re here to make you, the client, happy."~"We believe in efficiency.  Our expertise allows us to produce and deliver projects at a rate rarely rivaled by our competitors.  We can churn your project out in the shortest period of time at the absolute cheapest price."|Of the following, what sales pitch does your firm most frequently market?  Please rank the following from most similar (1) to least similar (3).';
  $arr_surveyfields[5] ='1|4|1~2~3^Jim is a third-year intern with extensive knowledge of AutoCADD and design principles.  He graduated in the top of his class from a premier university and has since worked with two of the best firms in the industry.  His references are glowing and you can tell that he is a self-directed individual with good initiative.  He is asking for a salary of $45,000 per year.~Joan is a second-year intern with good basic AutoCADD skills.  Although she lacks experience in many aspects of the job, she clearly has the aptitude to learn quickly and develop the skills necessary to fill the position; with a little bit of training and patience, she could be a strong contributor.  She is asking for a starting salary of $35,000.~Mike is a CADD Technician with a technical degree in computer-aided design from the local community college.  He has two years of experience and possesses good basic knowledge of AutoCADD.  Although lacking design experience, he appears to be a good technical person capable of completing deadlines and simple revisions quite quickly and with minimal error.  He is asking for a salary of $31,000.|Suppose you are hiring a new production-level staff member.  Below are three hypothetical job applicants.  Please rank the following from most likely (1) to be hired to least likely (3) to be hired.';
  $arr_surveyfields[6] ='1|4|1~2~3^ABC International is looking to build a high profile world headquarters in Miami.  They are looking for a cutting-edge design that will capture the attention of any and all who come within view of it.  They want their building to stand out from the rest of the skyline, and to be of such quality that it will garner the attention of the major Architecture magazines.~Dade County School Board has budgeted $40 Million to build 6 new schools over the next year.  They are looking for functional, attractive buildings that meet the rigid design criteria put in place by the school board.  They want hands-on, attentive management who are committed to meeting the needs of the school board.~XYZ restaurants has scheduled to renovate their 40 restaurants in Florida over the next two years.  Although the renovations and design schemes will be standard, each project will require the architect to adjust the criteria to suit each location.  They are looking for quick turnaround and delivery.|Of the following, which project most closely reflects the typical project pursued by your organization?  Please rank the following from most similar (1) to least similar (3).';
  $arr_surveyfields[7] ='1|5|Firm X is run by 4 primary partners, all of whom are very much involved in the day to day management of the firm.  The partners are very close to the projects and frequently act as project manager on multiple projects.  The firm is loosely structured and informal, facilitating a family-like environment.  However, there is a tendency for basic administrative and financial duties to be neglected.~Firm Y is a corporation with 5 partners, but with most power residing within the CEO.  The CEO is removed from much of the projects, only occasionally involving him/herself in specific projects; his/her role is almost purely administrative.  The firm is very formal and hierarchically structured, and most functions are standard and proceduralized.|Of the following, what best describes the power structure of your organization?<br><span style="font-weight: normal;">(Select one)</span>';
  $arr_surveyfields[8] ='1|5|We strive for consensus - we try to ensure that all relevant voices are heard and agree on the course of action to be taken.~Decisions are typically made by individuals according to the hierarchical authority structure.  Decisions are made at the top and passed down to lower levels.|What process best describes the decision-making structure of your organization?<br><span style="font-weight: normal;">(Select one)</span>';
  $arr_surveyfields[9] ='1|5|We follow opportunities, but engage in little formal planning.  The market guides us and our goals change frequently.~Planning is a critical part of our operations.  We frequently engage in formal planning sessions in which specific goals and objects are set for the coming months.  Our plans guide our practices, including marketing, financial, expansion, etc.|Which of the following best describes the planning activities of your organization?<br><span style="font-weight: normal;">(Select one)</span>';
  $arr_surveyfields[10]='1|5|We hire individuals for the firm, not the project.  We look for long-term, flexible individuals who can work on almost any project in the office.  We focus of fit and longevity.~We hire to meet the needs of the project first.  We look for specific skills, experiences, and specialties that will contribute to the firm\'s ability to delivery the best, most efficient product.|Which of the following best describes your hiring strategy?<br><span style="font-weight: normal;">(Select one)</span>';
  $arr_surveyfields[11]='1|5|We focus on professional quality of projects first, profit second.~We focus on administration and profits; professional quality is essential, but a consideration of the project architect and designer.|What best describes the management philosophy of your organization?<br><span style="font-weight: normal;">(Select one)</span>';
  $arr_surveyfields[12]='1|5|The project is a beautiful, well-designed structure of which we can be proud.~The project was very successful; it was delivered on time and well within budget, yielding a nice profit for the firm.|When a project is complete, what of the following is valued more heavily?<br><span style="font-weight: normal;">(Select one)</span>';


                         
?>
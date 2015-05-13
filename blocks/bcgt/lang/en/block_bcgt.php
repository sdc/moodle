<?php
//Settings
$string['descthemejquery'] = 'If JQuery is enabled in the theme then the block/mods will need
    to load that. Otherwise it will load its own.';
$string['labelthemejqueryloc'] = 'JQuery: Location of jQuery Theme File and full name';
$string['descthemejqueryloc'] = 'This is in relation to moodle. So it should be in the form of
    "/theme/yourtheme/js/jquery-1.9.1.js"';
$string['desclinkqualcourse'] = 'This allows qualifications to be attached to courses and thus allow enrolled students to 
    automatically be attached to them. It will also allow Moodle Teachers and Non Editing Teachers access and editing to  the Qualifications. 
    Please note that at least one of "Link Qualifications to Courses" or "Link Qualifications to Students" 
    must be set. Both can be set. This has not been fully implemented with handling of enrol/unenrol events yet.';
$string['desclinkqualstudent'] = 'This allows qualifications to be attached to students regardless of the course they are on 
    Please note that at least one of "Link Qualifications to Courses" or "Link Qualifications to Students" 
    must be set. Both can be set. If both are set, when a student unenrols from a course they wont lose links to their grade trackers 
    (All Grade Trackers are archived and can be relinked easily)';
$string['desclinkqualteacher'] = 'This allows qualifications to be attached to teachers regardless of the course they are on. 
    If this is not set then the teachers will be linked through courses that the qualifications are on. 
    Please note that if this is not set then "Link Qualifications to Courses" must be set. Both can be set. if both are set, when a 
    teacher is removed from a course they will not automatically lose links to their grade trackers. 
    (All Grade Trackers are archived and can be relinked easily)';
$string['descenrolstudentqual'] = 'If this is checked students enrolling on a course that has a qualificaion attached to it 
    will automatically be attached to all grade tracker associated with this course';
$string['descunenrolstudentqual'] = 'If this is checked students unenrolling from a course that has a qualificaion attached to it 
    will automatically be unlinked from all grade tracker associated with this course. This will be archived and can easily be relinked';
$string['descenroldeaultallunits'] = 'If this is checked when a student is added to a course/qualification they will be 
    added to all units on that qualification. If this is unchecked they will be defaulted to no units. 
    ';
$string['descbtecunitspredgrade'] = 'BTECs: The minimum number of units awarded before an averaged predicted grade is calculated.';
$string['descalevelusefa'] = 'ALevels: Use Formal Assessments to record distinct Grading/Assessments for students. These are used seperatly from general assignments/homeworks/activities. FA\'s are grades inputed by teachers';
$string['descalevelManageFACentrally'] = 'ALevels: If FA\'s are managed centrally then they can be pushed out to Alevels centrally. This stops FAs being able to be added onto individual qualifications. This allows for institution wide assessment points.';
$string['descalavelLinkAlevelGradeBook'] = 'ALevels: This will allow the Gradebook to be shown on the student\'s trackers.';
$string['descaleveluseceta'] = 'ALevels: CETA grades are inputted by the teacher to show what the teacher expects the student to achieve at the end of the year';
$string['descalevelusecalcpredicted'] = 'ALevels: Calculate a predicted grade based upon work submitted (homeworks, formal assessments etc)';
$string['descalevelpgfa'] = 'ALevels: Use FAs (Formal Assessments) in the predicted grade.';
$string['descalevelpggb'] = 'ALevels: Use gradebook activities in the predicted grade';
$string['descalevelallowalpsweighting'] = 'ALevels: ALPS have temperature coefficients that can be used to determine target grades that are specific to each Alevel subject';
$string['descaleveldefaultalpspercentage'] = 'ALevels: ALPS coefficients are broken down into percentiles of the national results. Weighted target grades can be calculated to push target grades to be in a certain percentile of the national average. For example, to be in the top quarter, put 75%';
$string['descbtecgridcolumns'] = 'Comma seperated list of columns to display when viewing students information. Please choose from: picture, email, name (firstname and lastname), firstname, lastname, userame. The order will be kept';
$string['descbteclockedcolumnswidth'] = 'This is the overall pixel width of the fixed columns in the Unit Grid';
$string['descalevelgradebookscaleonly'] = 'If this is checked then the GradeBook section of any grids will only show the grades that use the corresponding GradeTracker Scale';
$string['descautocalculateasptargetgrade'] = 'When a target grade is automatically calculated using ALPS. If this is set then another "aspirational" grade will be set. This can (using roles/permissions) be set to updateable';
$string['descautocalcaspvalue'] = 'This is the difference in grades between the calculated alps target grade and the aspiration grade. Values can be negative or positive and can be whole numbers or .3 or .6 For example, B -> A is 1 grade difference. C -> D/C is - 0.6 grade difference and A-> C/D is -2.3 grade difference';
$string['descshowtargetgrades'] = 'Show/Use Target Grades across the system (Either auto calculated using ALPS or teacher set). This allows Value Added calculations to be made. (Note: System is being updated to take this into account.)';
$string['descshowaspgrades'] = 'Show/Use aspiration/teacher set target grades across the system. (Note: System is being updated to take this into account.)';
$string['descweightedtargetmethod'] = '(0, 1 or 2): 0 = not at all, 1 = Multiplying the students average gcse score by the qualification coefficient from ALPS, 2 = Multiplying the users Target Grade Ucas Points (E.g. A -> 60) by the qualification coefficient from ALPS and find this new Target Grade';
$string['descpagingnumber'] = 'How many records to show in each page of records (grids). Use 0 to turn off paging.';
$string['labelpagingnumber'] = 'Size of Paging';
$string['labelshowtargetgrades'] = 'User Target Grades';
$string['labelshowaspgrades'] = 'Use Aspirational Grades';
$string['labelenroldeaultallunits'] = 'When enrolling a student onto a course/qual default to add to all units';
$string['labelunenrolstudentqual'] = 'Unlink students from Qual when unenrolling from Course';
$string['labelenrolstudentqual'] = 'Link students to Qual when enrolling on Course';
$string['labellinkqualteacher'] = 'Teachers: Link Qualifications to Teachers';
$string['labellinkqualstudent'] = 'Students: Link Qualifications to Students';
$string['labellinkqualcourse'] = 'Courses: Link Qualifications to Courses';
$string['labelthemejquery'] = 'JQuery: Is Jquery loaded in the theme?';
$string['labelbtecunitspredgrade'] = 'BTECs: Minimum number of unit awards for predicted grade';
$string['labelalevelusefa'] = 'ALevels: Use Formal Assessments?';
$string['labelalevelManageFACentrally'] = 'ALevels: Manage Formal Assessments Centrally?';
$string['labelalavelLinkAlevelGradeBook'] = 'ALevels: Link the tracking sheet to the Moodle Gradebook.';
$string['labelaleveluseceta'] = 'ALevels: Use CETA (Currently Expected to Achieve)?';
$string['labelalevelusecalcpredicted'] = 'ALevels: Calculate a Predicted Grade';
$string['labelalevelpgfa'] = 'ALevels: Use FAs (Formal Assessments) in the predicted grade';
$string['labelalevelpggb'] = 'ALevels: Use gradebook activities in the predicted grade';
$string['labelalevelallowalpsweighting'] = 'ALevels: Use ALPS Subject Weightings';
$string['labelaleveldefaultalpspercentage'] = 'ALevels: Default AlPS percentage';
$string['labelbtecgridcolumns'] = 'Columns: ';
$string['labelbteclockedcolumnswidth'] = 'BTEC Unit Grid Fixed Columns Width';
$string['labelalevelgradebookscaleonly'] = 'Show only GradeBook entries that use Grade Tracker Scale';
$string['labelautocalculateasptargetgrade'] = 'Auto Calculate Aspirational Grade';
$string['labelautocalcaspvalue'] = 'Target Grade -> Aspirational Difference';
$string['labelweightedtargetmethod'] = 'Weighted Target Grade Calculation Method';

//A
$string['areyousuredeletequals'] = 'Are you sure you want to delete these qualifications?';
$string['areyousuredeleteunits'] = 'Are you sure you want to delete these units?';
$string['activitiesfas'] = 'Activities and Formal Assessments';
$string['addcomment'] = 'Add Comment';
$string['addnewqualhelp'] = 'Add a new Qualification to the system. Pick from the Qualifications installed using the bcgt{qual} in the mod folder';
$string['addnewqual'] = 'New: Add a new Qualification to the system';
$string['addnewunithelp'] = 'Add a new Unit to the system. Pick from the Qualifications installed using the bcgt{qual} in the mod folder';
$string['addnewunit'] = 'Add a new Unit to the system';
$string['addnewgradingstructure'] = 'Add New Grading Structure (Bespoke Qualifications only)';
$string['addnewgradingstructurehelp'] = 'Create new grading structures to use to calculate final awards';
$string['addunitprequal'] = 'Add to pre-existing qualification';
$string['addeditunitsheading'] = 'Add/Edit A Unit';
$string['addeditqualsheading'] = 'Add/Edit a Qualification';
$string['addname'] = 'Additional Name';
$string['addtask'] = 'Add Task';
$string['admin'] = 'Admin';
$string['ahead'] = 'Ahead';
$string['addsignoffsheet'] = 'Add Sign-off Sheet';
$string['addnewqual'] = 'Add a New Qualification';
$string['asptargetgrades'] = 'Asp Target Grade';
$string['allavailablequals'] = 'All Available Qualifications';
$string['assessmentmarks'] = 'Assessment Marks';
$string['avgawardpoints'] = 'Avg Award Points';
$string['activity'] = 'Activity';
$string['activities'] = 'Activities';
$string['activitycheck'] = 'CS: Activity Check';
$string['activitycalendarview'] = 'CS: Calendar View';
$string['add'] = 'Add';
$string['addcriteria'] = 'Add New Criteria';
$string['addactivitylinks'] = 'Add an Activity Link';
$string['alevelums'] = 'UMS';
$string['alevelunitums'] = 'Unit UMS';
$string['lastyearsqual'] = 'Last Years AS Level';
$string['alevelunitex'] = 'Please Specify the Units';
$string['alevelformalassessments'] = 'Formal Assessments';
$string['alevelassname'] = 'Assessment Name';
$string['alevelassno'] = 'Assessment No';
$string['alevelassdate'] = 'Assessment Date';
$string['alevelassdetails'] = 'Assessment Details';
$string['alevelasslink'] = 'Assessment Link';
$string['percentage'] = 'Percentage';
$string['alevelcoefficient'] = 'Coeeficient/Score';
$string['alevelweightings'] = 'Alps Weightings';
$string['aleveltargetcoefficient'] = 'Target Weighting';
$string['aspirational'] = 'Aspirational';
$string['asptargetgrade'] = 'Aspirational Target Grade';
$string['aspbreakdown'] = 'Aspirational Target Breakdown';
$string['assessments'] = 'Assessments';
$string['addnewsubtype'] = 'Add New SubType';
$string['autoenrolusers'] = 'Automatically Enrol Students';
$string['autoenrolusersdesc'] = 'If a course is linked to a qualification, when a new user is enrolled on the course, they will be automatically linked to the qualification(s) as well, and all their units (if they are a student)';
$string['autounenrolusers'] = 'Automatically Unenrol Students';
$string['autounenrolusersdesc'] = 'If a course is linked to a qualification, when a user is unenrolled from the course, they are removed from the qualification and units as well (this data is archived).';
$string['award'] = 'Award';
$string['avggcsescore'] = 'Avg GCSE Score';
$string['awarddataimportedsuccess'] = 'Number of Award Data rows processed correctly';
$string['awarddataupdated'] = 'Number of Award Data rows led to an update';
$string['awarddatainserted'] = 'Number of Award Data rows led to an insert';
$string['addnewnonmetcritvalues'] = 'Add New Non-Met Criteria Values (Bespoke Qualifications Only)';
$string['addnewnonmetcritvalueshelp'] = 'Add new values that you can select for criteria that are not a passing grade, e.g. "Partially Achieved", "Absent", "Work Not Submitted", etc...';

//B
//Permissions
$string['bcgt:addinstance'] = 'Add a new Grade Tracker block';
$string['bcgt:addnewqual'] = 'Add new Qualifications to the system';
$string['bcgt:addnewunit'] = 'Add new units to the system';
$string['bcgt:addqualtocurentcourse'] = 'Ability to edit qualifications on the current Moodle course.';
$string['bcgt:addasstudentongrids'] = 'Can this role be added as a student on a tracking grid/qualification';
$string['bcgt:addasteacherongrids'] = 'Can this role be added as a teacher on a tracking grid/qualification';
$string['bcgt:addcriteriagradingstructure'] = 'Ability to add a criteria grading structure for the Bespoke Qualification family.';
$string['bcgt:addqualgradingstructure'] = 'Ability to add a qualification grading structure for the Bespoke Qualification family.';
$string['bcgt:addunitgradingstructure'] = 'Ability to add a unit grading structure for the Bespoke Qualification family.';
$string['bcgt:calculatetargetgrades'] = 'Ability to calculate target grades';
$string['bcgt:calculatepredictedgrades'] = 'Ability to calculate predicted grades en-mass';
$string['bcgt:calculateaveragegcsescore'] = 'Ability to calculate average gcse scores';
$string['bcgt:downloadstudentgrid'] = 'Download a student grid';
$string['bcgt:downloadunitgrid'] = 'Download a unit grid';
$string['bcgt:deleteunit'] = 'Ability to delete a unit';
$string['bcgt:deletequalification'] = 'Ability to delete a qualification';
$string['bcgt:editclassgrids'] = 'Edit users grids, a specific user or a unit of users';
$string['bcgt:editqual'] = 'Edit Qualifications in the system';
$string['bcgt:editunit'] = 'Edit Units in the system';
$string['bcgt:editqualunit'] = 'Add/Remove units on a qualification';
$string['bcgt:editqualscourse'] = 'Add/Remove qualifications from a Moodle Course';
$string['bcgt:editteacherqual'] = 'Link Teachers to Qualifications (separate from Moodle Courses)';
$string['bcgt:editstudentqual'] = 'Link Students to Qualifications (separate from Moodle Courses)';
$string['bcgt:editstudentunits'] = 'Select which Units a Student is doing on a Qualification';
$string['bcgt:editmentorsmentees'] = 'Link Personal Tutors to Students (Separate of Moodle Courses)';
$string['bcgt:editmanagersteam'] = 'Link Managers to Staff (Separate of Moodle Courses)';
$string['bcgt:editmyownquals'] = 'Ability to Link themselves to view/edit a Qualification (separate from Moodle Courses)';
$string['bcgt:editmyownmentees'] = 'Ability to Link themselves to students as mentees (separate from Moodle Courses)';
$string['bcgt:editmyownteam'] = 'Ability to Link themselves to Staff (separate from Moodle Courses)';
$string['bcgt:editstudentgrid'] = 'Edit(Mark) the Students Grid';
$string['bcgt:editunitgrid'] = 'Edit(Mark) the Unit Grid';
$string['bcgt:editqualfamilysettings'] = 'Ability to set and edit settings for the qualification families';
$string['bcgt:editpriorlearning'] = 'Ability to alter the points and grades for prior learning grades';
$string['bcgt:edittargetgradesettings'] = 'Ability to alter the points and grades for target grades';
$string['bcgt:edittargetgrade'] = 'Ability to edit the target grade for a student.';
$string['bcgt:editasptargetgrade'] = 'Ability to edit the aspirational or teacher set target grade for a student';
    
$string['bcgt:exportdata'] = 'Export Data to CSV';
$string['bcgt:editpriorqualsettings'] = 'Ability to edit the prior qualification settings (points, weightings and grades)';
$string['bcgt:importdata'] = 'Import Data from CSV';
$string['bcgt:importpriorlearning'] = 'Ability to import quals on entry into the system to calculate target grades';
$string['bcgt:importtargetgrades'] = 'Ability to import target grades';
$string['bcgt:importqualweightings'] = 'Ability to import qual weightings';
$string['bcgt:importassess'] = 'Ability to import assessment marks';
$string['bcgt:importquals'] = 'Ability to import quals';
$string['bcgt:myaddinstance'] = 'Add a new Grade Tracker block to the My Moodle page';
$string['bcgt:mergequalification'] = 'Ability to merge qualifications';
$string['bcgt:mergeunit'] = 'Ability to merge units';
$string['bcgt:manageactivitylinks'] = 'Manage Activity Links with the Grade Tracker';
$string['bcgt:printstudentgrid'] = 'Ability to print student grid';
$string['bcgt:printunitgrid'] = 'Ability to print unit grid';
$string['bcgt:searchquals'] = 'Search for a Qualification(s)';
$string['bcgt:searchunits'] = 'Search for a Unit(s)';
$string['bcgt:transferstudentsunits'] = 'Ability to transfer students units (plus marks) between qualification of the same type';
$string['bcgt:viewdashboard'] = 'View the main tracking area where users can view an overview and then links to other tabs';
$string['bcgt:viewclassgrids'] = 'View users grids, a specific user or a unit of users';
$string['bcgt:viewmytracking'] = 'View thier own grid(s)';
$string['bcgt:viewadmintab'] = 'View the Admin tab in the dashboard';
$string['bcgt:viewcoursestab'] = 'View the Courses tab in the dashboard';
$string['bcgt:viewassignmentstab'] = 'View the Assignment tab in the dashboard';
$string['bcgt:viewmessagestab'] = 'View the Messages tab in the dashboard';
$string['bcgt:viewteamtab'] = 'View the Team tab in the dashboard';
$string['bcgt:viewfeedbacktab'] = 'View the Feedback tab in the dashboard';
$string['bcgt:viewhelptab'] = 'View the Help tab in the dashboard';
$string['bcgt:viewstudentstab'] = 'View the Students tab in the dashboard';
$string['bcgt:viewreportsstab'] = 'View the Reports tab in the dashboard';
$string['bcgt:viewunitstab'] = 'View the Units tab in the dashboard';
$string['bcgt:viewallgrids'] = 'Ability to view all grids regardless of those that the user is linked to using the forms.';
$string['bcgt:viewowngrid'] = 'Ability to view a users own tracking sheet(s)';
$string['bcgt:viewbteclatetracking'] = 'View BTEC Late History';
$string['bcgt:viewbtecmaxgrade'] = 'View BTEC Max grade calculation';
$string['bcgt:viewbtecavggrade'] = 'View BTEC Average grade calculation';
$string['bcgt:viewbtecmingrade'] = 'View BTEC Min grade calculation';
$string['bcgt:viewbtectargetgrade'] = 'View BTEC Target grade';
$string['bcgt:viewweightedtargetgrade'] = 'Ability to view the weighted target grades (grids etc)';
$string['bcgt:viewtargetgrade'] = 'Ability to view the target grades (grids etc)';
$string['bcgt:viewvalueaddedgrids'] = 'Ability to view value added calculations on the grids';
$string['bcgt:viewactivitylinks'] = 'View Activity Links with the Grade Tracker';
$string['btecqualsettings'] = 'BTEC Qualification Family settings ';
$string['btecshowaspgrades'] = 'Coming Soon: Show aspiration grades';
$string['btecgridoptions'] = 'Configure Grid Options';
$string['btecspecialval'] = 'Special Value';
$string['btecenabled'] = 'Enabled';
$string['bteccurrenticon'] = 'Custom Icon';
$string['bteccurrenticonlate'] = 'Custom Late Icon';
$string['btecdefaulticon'] = 'Default Icon';
$string['btecdefaulticonlate'] = 'Default Late Icon';
$string['bteccredits'] = 'Credits';
$string['btectotalcredits'] = 'Total Credits';
$string['btecsubtypedes'] = 'For Level 3 Units there are two SubTypes. If the Qualification this unit will
    be going on is a Foundation Diploma then please select Foundation Diploma Unit. Else select "All Others"';
$string['bteccritdropdes'] = 'Please select the number of criteria wanted : ';
$string['btecpass'] = 'Pass';
$string['btecmerit'] = 'Merit';
$string['btecdiss'] = 'Distinction';
$string['btecblankcriteria'] = 'Any sub criteria that are left blank will not be saved onto the unit.';
$string['bteceditcritins'] = 'To remove a criteria either reduce the number in 
    the drop down boxes above. To remove subCriteria click on the delete icon';
$string['bteccredrequired'] = 'Credits Required';
$string['btectotalcrednote'] = 'Note: Total Credits can be more than qualifications credits required';
$string['byStudent'] = 'By Student';
$string['byClass'] = 'By Class';
$string['byassessment'] = 'By Assessment';
$string['byUnitSubject'] = 'By Unit/Subject';
$string['byUnit'] = 'By Unit';
$string['bySubject'] = 'By Subject';
$string['bcgtstudentassgrid'] = 'Student Assessments';
$string['bcgtassessment'] = 'Assessment';
$string['bcgtmydashboard'] = 'My Dashboard';
$string['back'] = 'Back';
$string['backmenu'] = 'Back To Menu';
$string['behind'] = 'Behind';
$string['btecUnitSubtype'] = 'Unit Type';
$string['btecUnitSubtypeExp'] = 'APL Units are those brought in from other Institutes. For APL Units you can select 0 criteria';
$string['breakdownsnotfound'] = 'Breakdowns Not Found';
$string['breakdown'] = 'Target Breakdown';


//C
$string['calculate'] = 'Calculate';
$string['calculateall'] = 'Calculate All';
$string['calculatesel'] = 'Calculate Selected';
$string['calcavgscore'] = 'Calculate Average GCSE Score';
$string['refreshpredgrade'] = 'Refresh Predicted Grade';
$string['calctargetgrade'] = 'Calculate Target Grade';
$string['calculateaveragegcsescoreshelp'] = 'Calculate Average GCSE Scores using Prior Learning that has already been inputted into the system';
$string['calculateaveragegcsescores'] = 'Calculate Average GCSE Scores';
$string['calculatetargetgradeshelp'] = 'Calculate Target Grades using Average GCSE Scores that has already been inputted into the system';
$string['calculatetargetgrade'] = 'Calculate Target Grades';
$string['calcusertgdesc'] = 'Please select users from the options below. For Qualification and Courses: When selected, the system will find all of the users attached to that course/qual and then for each user calculate all of their target grades. (For each user it will calculate all target grades for all of their courses/quals not just the one selected)';
$string['calcuserpgdesc'] = 'Please select users from the options below. For Qualification and Courses: When selected, the system will find all of the users attached to that course/qual and then for each user calculate all of their predicted grades. (For each user it will calculate all predicted grades for all of their courses/quals not just the one selected)';
$string['calculatepredictedgrade'] = 'Calculate Predicted Grades';
$string['calculatepredictedgradeshelp'] = 'Will refresh the calculation for the predicted grades for either a student, qualification or course.';
$string['ceta'] = 'CETA';
$string['cgqualsettings'] = 'City & Guilds Qualification Settings';
$string['choosefile'] = 'File';
$string['classoverview'] = 'Class Overview';
$string['compatabilityerrors'] = 'Compatability Errors';
$string['coefficient'] = 'Coefficient';
$string['course'] = 'Course';
$string['coursesearch'] = 'Course Search';
$string['courseualusersselectall'] = 'Select all Students on this course for this Qual';
$string['count'] = 'Count';
$string['countheadersimport'] = 'The count of the uploaded headers is not the same as the count of the expected headers';
$string['coursesearchpar'] = 'Course Search Parameters';
$string['credits'] = 'Credits';
$string['criteria'] = 'Criteria';
$string['criteriaName'] = 'Criteria Name';
$string['criteriaDetails'] = 'Criteria Details';
$string['criterias'] = 'Criterias';
$string['criteriasnotfound'] = 'Criterias Not Found';
$string['criterianame'] = 'Name';
$string['criteriadetails'] = 'Details';
$string['criterianotavailableuntilformcomplete'] = 'You will be able to add criteria to this unit once you have selected all the required options to build the unit, such as Type, Pathway, etc...';
$string['critgradingstructurehelp'] = 'Create a new grading structure to use to calculate criteria awards.';
$string['critgradingpointshelp'] = 'The points you assigned to grades here are used in the calculation for the overall unit award, to create the avg score of criteria which will correspond with the lower & upper ranges of the UNIT grading structure which you created earlier';
$string['critgradingrangehelp'] = 'These ranges are only used if the criteria itself has sub-criteria. The ranges you assign here will be used to choose a criteria award, based on an average calculation of the points the student has gained on that criteria\'s sub-criteria. E.g. If they had an avg sub-critera score of 1.8, and you had a Criteria grading of Merit with a range of 1.5-2.6 it would fall between there and their criteria award would be calculated as a merit.';
$string['criteriadataimportedsuccess'] = 'Number of Criteria Data Import rows process successfully';
$string['criteriadataupdated'] = 'Number of Criteria Data Import rows led to an update';
$string['criteriadatainserted'] = 'Number of Criteria Data Import rows led to an insert';
$string['csvheadersdontmatch'] = 'The headers in the csv dont match the expected headers';
$string['copyqualhelp'] = 'Pick the qualification from those in the system and copy it';
$string['copyqual'] = 'Copy: Copy a Qualification in the system';
$string['couldnotsave'] = 'Could not save... Please fill out all required elements of the form...';
$string['coursesearch'] = 'Course Search';
$string['currentquals'] = 'Current Qualifications';
$string['customfullname'] = 'Custom Value';
$string['customshortname'] = 'Custom Short Value';
$string['currentproject'] = 'Current Assessment';
$string['current'] = 'Current';

//D
$string['dashtabdash'] = 'My Dashboard';
$string['dashtabtrack'] = 'Trackers';
$string['dashtabcourse'] = 'CS: Courses';
$string['dashtabstu'] = 'CS: Students';
$string['dashtabteam'] = 'CS: Team';
$string['dashtabunit'] = 'CS: Units';
$string['dashtabrep'] = 'Reports';
$string['dashtabass'] = 'CS: Assignments';
$string['dashtabadm'] = 'Admin';
$string['dashtabhel'] = 'CS: Help';
$string['dashtabfeed'] = 'CS: Feedback';
$string['dashtabmess'] = 'CS: Messages';
$string['dataimportedsuccess'] = 'Number of Data Import rows process successfully';
$string['displaytype'] = 'Display Type';
$string['displaytype:desc'] = 'The awarding body or general title of the type of qualification so that "Bespoke" does not appear in the name';
$string['delete'] = 'Delete';
$string['date'] = 'Date';
$string['desc'] = 'Description';
$string['deleteexisting'] = 'Delete Existing';
$string['deletequals'] = 'Delete Qualifications';
$string['deleteunits'] = 'Delete Units';
$string['deletequalshelp'] = 'Delete a Qualification from the system';
$string['deleteunitshelp'] = 'Delete a Unit from the system';
$string['details'] = 'Details';
$string['direct'] = 'Direct';

//E
$string['editcomments'] = 'Edit Comments';
$string['editexisting'] = 'Edit Existing';
$string['editqualteacherheading'] = 'Add/Remove Teachers from Qualifications';
$string['editqualstudentheading'] = 'Add/Remove Students from Qualifications';
$string['editunithelp'] = 'Pick the Unit from those in the System and alter things like its name, criteria etc';
$string['editunit'] = 'Edit a Unit in the system';
$string['editqualunitshelp'] = 'Pick a Unit and add/remove it from many Qualifications';
$string['editqualunits'] = 'Add/Remove Unit from Qualifications';
$string['editstudentunitshelp'] = 'Select a Unit and for all of those students doing a Qualification
    that this Unit is on, select if the students are doing this Unit';
$string['editstudentunits'] = 'Edit Students Unit';
$string['editmanagerteamhelp'] = 'Link Staff to other Staff so they can see an overiew and reports based on thise saff';
$string['editmanagerteam'] = 'Link Staff to other Staff';
$string['editmentorsstudentshelp'] = 'Pick a staff member and link the staff member to the student. This will allow
    this member of staff to view the tracking sheets and get reports on them';
$string['editmentorsstudents'] = 'Link Mentors with Students';
$string['editqualsteacherhelp'] = 'Select a staff member and link them to Qualifications so they can view and edit the tracking grids.';
$string['editqualsteacher'] = 'Link Staff with Qualifications';
$string['editqualsstudenthelp'] = 'Select a student and link them to Qualifications. It will
    then ask you to pick the units that this student is doing';
$string['editqualsstudent'] = 'Link a Student with Qualifications';
$string['editunitsstudenthelp'] = 'Select a User and edit which units this user is doing';
$string['editunitsstudent'] = 'Edit Student Units';
$string['edittargetgradesettingshelp'] = 'Alter points and grades for qualifications in relation to target grades';
$string['edittargetgradesettings'] = 'Target Grade Settings';
$string['editpriorlearningsettingshelp'] = 'Alter points and grades for qualifications in relation to target grades';
$string['editpriorlearningsettings'] = 'Prior Learning Settings';
$string['editcoursequal'] = 'Edit Course Qualifications';
$string['editqualunits'] = 'Add & Remove Qualification Units';
$string['editqualunithead'] = 'Units on this Qualification';
$string['editsimple'] = 'Edit Simple';
$string['editadvanced'] = 'Edit Advanced';
$string['edit'] = 'Edit';
$string['editcoursequals'] = 'Qualifications on Course';
$string['editunitcond'] = '(Edit Unit: When one unit selected)';
$string['editunitqualheading'] = 'Edit the Qualifications a Unit is on';
$string['editusersusersmanheading'] = 'Link Staff to Staff: Edit a Managers Team';
$string['editusersuserstutheading'] = 'Link Mentors with Students: Edit Mentees';
$string['editcoursequalheading'] = 'Stage 1: Edit Course Qualifications';
$string['editcoursequalusers'] = 'Edit Users Qualification';
$string['editqualhelp'] = 'Pick the qualification from those in the system and change its name etc';
$string['editqual'] = 'Edit: Edit a Qualification in the system';
$string['editqualunithelp'] = 'Add/remove Units from a Qualification by first selecting the qualification. You will then
    be asked to select which students that are on this Qualification are doing these Units';
$string['editqualunit'] = 'Units: Add/Remove Units from a Qualification';
$string['editqualscoursehelp'] = 'Pick a course and add a qualification to it, 
    or pick a qualification and add it to a course. Note that all students and teachers
    who are on this course will immediately do this Qualification and its Units';
$string['editqualscourse'] = 'Courses: Add/Remove Quals onto Moodle Courses';
$string['editteacherqualhelp'] = 'Link a staff member to a Qualification so they can see
    it and edit it. This can be combined, or separate from, the Quals on Course link. 
    Here you will pick the Qualification and then pick the teachers.';
$string['editteacherqual'] = 'Teachers: Link Qualifications with Staff';
$string['editstudentqualhelp'] = 'Link a student to a Qualification so they can see
    it and be tracked. This can be combined, or separate from, the Quals on Course link. 
    Here you will pick the Qualification and then pick the students';
$string['editstudentqual'] = 'Students: Link Qualifications with Students';
$string['editstudentunitsqualhelp'] = 'Pick a Qualification and select, for all students 
    on that Qualification all of thei units.';
$string['edituserscoursequal'] = 'Edit Course & Users Qualifications';
$string['editstudentunitsqual'] = 'Students Units: Edit Students Units';
$string['error:displaytype'] = 'Display Type must be set (this would most commonly be the awarding body)';
$string['error:subtype'] = 'Sub Type must be set (e.g. Diploma, Certificate, Award, etc...)';
$string['error:name'] = 'Name must be set';
$string['error:gradingstructure'] = 'Grading structure must be set (e.g. PMD). These can be created through the dashboard.';
$string['error:cannotdelqualstructure'] = 'Grading Structure cannot be deleted. The following qualifications are using it:';
$string['error:uniquecode'] = 'Unique (external) code must be set (this would be the code assigned by the awarding body, e.g. H/123/456)';
$string['error:cannotdeunitstructure'] = 'Unit Grading Structure cannot be deleted. The following units are using it:';
$string['error:pathway'] = 'Unit Pathway must be set';
$string['error:pathwaytype'] = 'Unit Pathway type must be set';
$string['email'] = 'Email';
$string['enrolment']= 'Enrolment';
$string['edit'] = 'Edit';
$string['edittargetgrades'] = 'Edit Target Grades';
$string['examples'] = 'Examples';
$string['enggcse'] = 'Eng GCSE';

//F
$string['family'] = 'Family';
$string['famqual'] = 'Qualification to add marks to';
$string['famassessment'] = 'The Assessment';
$string['famimportsum1'] = 'Number of Users Not Found';
$string['famimportsum2'] = 'Number of Grades Not Found';
$string['famimportsum3'] = 'Number of CETA Grades Not Found';
$string['famimportsum4'] = 'Number of Assessment marks submitted';
$string['firstname'] = 'Firstname';
$string['fullname'] = 'Fullname';
$string['formalassessment'] = 'FA';
$string['formalassessments'] = 'Formal Assessments';
$string['fas'] = 'Assessments';
$string['feedback'] = 'Feedback';
$string['finalProject'] = 'Final Project';
$string['findstudent'] = 'Find Student';
$string['famdesc'] = 'This will import the Formal Assessment Grades, Target Grads and comments for a set of students for a chosen formal assessment and qualification';

//G
$string['grade'] = 'Grade';
$string['gradetracker'] = 'Grade Tracker V5';
$string['gridselectstudent'] = 'Select a Student Grid';
$string['gridselectunit'] = 'Select a Unit Grid';
$string['gridselectclass'] = 'Select a Class Grid';
$string['gridselectassessment'] = 'Select a Assessment Grid';
$string['gradesettings'] = 'Grade Settings';
$string['gradebook'] = 'GradeBook';
$string['gradebookExp'] = 'This will show the Moodle core grade book for a course. This will allow grades and comments to be added to 
    activities that are in the gradebook. This will be managed by an "Activity Management Console from the Grade Tracker Block"';
$string['grids'] = 'Trackers';
$string['grid'] = 'Grid';
$string['gradingstructure'] = 'Grading Structure';
$string['grading'] = 'Grading';
$string['gridselect'] = 'gridselect';
$string['gradevalues'] = 'Grade Values';
$string['gradetype'] = 'Grade Type';
$string['gradetypealps'] = 'ALPS Calculated Target Grade';
$string['gradetypeweighted'] = 'Weighted Target Grade';
$string['gradetypeteacher'] = 'Teacher Set/Aspirational Target Grade';
$string['gridimg'] = 'Grid Image';
$string['gridkey'] = 'Grid Key';
$string['gcselower'] = 'GCSE Lower';
$string['gcseupper'] = 'GCSE Upper';

//H
$string['header'] = 'Header';
$string['help'] = 'Help';

//I
$string['import'] = 'Import';
$string['importcalc'] = 'Import & Process';
$string['importpriorlearninghelp'] = 'Import Quals on Entry using a csv file';
$string['importpriorlearning'] = 'Import Quals on Entry';
$string['importargetgradeshelp'] = 'Import Target Grades using a csv file';
$string['importtargetgrades'] = 'Import Target Grades';
$string['importweightingshelp'] = 'Import Qualification Weightings using a csv file';
$string['importweightings'] = 'Import Qualification Weightings';
$string['importqualshelp'] = 'Import Qualifications using a csv file';
$string['importquals'] = 'Import Qualifications';
$string['importassessmarkshelp'] = 'Import Assessment Marks using a csv file';
$string['importassessmarks'] = 'Import Assessment Marks';
$string['importoptions'] = 'Current Import Values Available';
$string['importnotice'] = 'Note: Target Grades currently work for BTEC and Alevels. Weightings currently only work for Alevels';
$string['ifyoudeletequals'] = 'If you delete these qualifications then they will be removed from any courses and users they are linked to.';
$string['ifyoudeleteunits'] = 'If you delete these units then they will be removed from any qualifications and students they are linked to.';
$string['invalidqual'] = 'Invalid Qualification';
$string['importcalcserver'] = 'Import From Server';
$string['importserverdesc'] = 'Import From Server will load the files from the folder moodledata/bcgt/import/ (You may need to create this). The files will need to be: ';


//J

//K
$string['ksearch'] = 'Keyword Search';
$string['kqualsearch'] = 'Qual Search';

//L
$string['lastname'] = 'Lastname';
$string['levels'] = 'Levels';
$string['level'] = 'Level';
$string['lowerrangescore'] = 'Lower Range Score';

//M
$string['mathsgcse'] = 'Mth GCSE';
$string['myDashboard'] = 'My Dashboard';
$string['mypriorquals'] = 'My Prior Qualifications';
$string['mytrackers'] = 'My Tracking Sheets';
$string['mystudents'] = 'My Students';
$string['mycourses'] = 'My Courses';
$string['myassignments'] = 'My Assignments';
$string['myreports'] = 'My Reports';
$string['myteam'] = 'My Team';
$string['myunits'] = 'My Units';
$string['mytargetpredictedgrades'] = 'My Target & Predicted Grades';
$string['markcurrent'] = 'Mark as Current';
$string['mytrackingsheet'] = 'My Tracking Sheet';
$string['messages'] = 'Messages';
$string['managefas'] = 'View and Manage Assessments';
$string['managefahelp'] = 'View/Edit/Add Centrally Managed Formal Assessments that can be pushed out to multiple qualifications';
$string['managersteam'] = 'Managers Team';
$string['manageactivitylinks'] = 'Manage Activity Links';
$string['missingiconimg'] = '[Missing Image]';
//N
$string['na'] = 'N/A';
$string['name'] = 'Name';
$string['notypesforthispathway'] = 'There are no types for this pathway';
$string['nosubtypesforthispathway'] = 'There are no subtypes for this pathway';
$string['nolevelsforthispathway'] = 'There are no levels for this pathway';
$string['noqualsuser'] = 'This user has no qualifications in the system';
$string['noimportfile'] = 'No import file was detected';
$string['notcsvfile'] = 'The file was not a CSV file (did not end in .csv)';
$string['nolevels'] = 'There are no Levels for this Type';
$string['nostudentsfound'] = 'No Students could be found';
$string['nosubtypes'] = 'There are no Subtypes for this Combination';
$string['noyears'] = 'No Of Years';
$string['nounits'] = 'No Units';
$string['number'] = 'Number';
$string['numobservations'] = 'No. Observations';
$string['numstudents'] = 'No. Students';
$string['nounitsawarded'] = 'No Units Awarded';
$string['nounitsfound'] = 'No Units Could Be Found';
$string['nodiss'] = 'No Diss';
$string['nodistinction'] = 'No Diss';
$string['nomerit'] = 'No Merit';
$string['nopass'] = 'No Pass';
$string['nonmetcriteriavalues'] = 'Non-Met Criteria Values';
$string['nonmetcriteriavalueshelp'] = 'These will be available as choices on all Bespoke qualifications/Units, they are not specific to a qualification.';
$string['newavgscore'] = 'New avg gcse score';
$string['newweighteducas'] = 'New Ucas Target Points';

//O
$string['order'] = 'Order';
$string['on'] = 'on';

//P
$string['pagenumber'] = 'Page';
$string['parent'] = 'Parent';
$string['pathway'] = 'Pathway';
$string['percentcomplete'] = '% Complete';
$string['pluginname'] = 'Grade Tracker V5';
$string['plimportsum1'] = 'Number of Prior Learning Records inserted';
$string['plimportsum2'] = 'Number of Users not found';
$string['plimportsum3'] = 'Number of Quals not found';
$string['plimportsum4'] = 'Number of Grades not found';
$string['plimportsum5'] = 'Number of Subjects not found';
$string['pleasechoosetracker'] = 'Please choose a tracker to view.<br><br>If you do not see any trackers listed above, then you have not been set up on any yet, please contact your course tutor.';
$string['plcreatemissingqual'] = 'Insert Quals';
$string['plcreatemissingqualdesc'] = 'If selected this will create any quals on entry that are not in the system but are in the csv';
$string['plcreatemissingsubject'] = 'Insert Subjects';
$string['plcreatemissingsubjectdesc'] = 'If selected this will create any subjects that are not in the system but are in the csv';
$string['plcreatemissinggrade'] = 'Insert Grade';
$string['plcreatemissinggradedesc'] = 'If selected this will create any grades that are not in the system but are in the csv';
$string['plcreatemissinguser'] = 'Insert User';
$string['plcreatemissinguserdesc'] = 'If selected this will create any users that are not in the system but are in the csv';
$string['pleaseselect'] = 'Please select an option';
$string['pleaseselectblank'] = '';
$string['picture'] = '';
$string['pldesc'] = 'This will import the students prior learning from a CSV. The columns are below. For GCSE the qual type can be (Normal,Short,Double). If you chose to "process" the file then the average GCSE score will be calculated and the Target Grades calculated. <br /><br />"GCSE" prior qualifications are the only qualifications that will be used to calculate an average score and then target grade.<br /><br />For any subject and qual type combination that a student already has prior learning for, it will update';
$string['predicted'] = 'Predicted';
$string['predictedgrades'] = 'Predicted Grades';
$string['predictedminaward'] = 'Predicted Min Award';
$string['predictedmaxaward'] = 'Predicted Max Award';
$string['predictedavgaward'] = 'Predicted Avg Award';
$string['predictedavgawardhelp'] = 'This is the prediction of your final award once you have completed all your units, based on the units you have awards for at this point.';
$string['predictedfinalaward'] = 'Predicted Final Award';
$string['predictedfinalawardhelp'] = 'This is your final award. Most of the time this will be accurate, however due to external assessors sometimes differing from teaching staff marking, we cannot guarantee it is 100% accurate.';
$string['predicted'] = 'Predicted';
$string['priorlearning'] = 'Prior Learning';
$string['printgrid'] = 'Print Grid';
$string['points'] = 'Points';

//Q
$string['qualfamily'] = 'Qualification Family';
$string['qualfamilys'] = 'Qualification Families';
$string['qualcount'] = 'Qualifications';
$string['qualifications'] = 'Qualifications';
$string['qualification'] = 'Qualification';
$string['quals'] = 'Quals';
$string['qual'] = 'Qual';
$string['qualoverview'] = 'Qual Overview';
$string['qualtype'] = 'Qualification Type';
$string['qwimportsum2'] = 'Number of Qualifications not found and not inserted (probably because the Level, SubType and Family dont macth a combination that the system can support)';
$string['qwimportsum3'] = 'Number of Qualifications not found and then successfully inserted';
$string['qwimportsum1'] = 'Number of Qualification Weightings Inserted';
$string['qwcreatemissingqual'] = 'Insert Qualification';
$string['qwcreatemissingqualdesc'] = 'If selected this will create any qualification that is not in the system but is in the csv';
$string['qualgradingstructurehelp'] = 'Qualification Grading Structures define the final awards an overall qualification can have.</p> <p>The Lower &amp; Upper range scores are used to work out which grade to award the student, based on their average points across all their units. <br /><em>(Please see example at the bottom of page)</em></p>';
$string['qualgradingstructurehelpex'] = '<h3>Example</h3> <p>If your Unit Grading Structure (the awards the units can be given) was: <br /><br />Pass (1 point),<br />Merit (2 points),<br />Distinction (3 points),<br />and the student ended up with <br />3 Passes, <br />2 Merits and <br />5 Distinctions, <br />the avg of those units would be 2.2 (22 [ttl] / 10 [num of units]).</p> <p>So if our Qualification Grading Structure was set up so that <br />Pass was between 1.0 and 1.5, <br />Merit was between 1.6 and 2.5, and <br />Distinction was between 2.6 and 3.0, <br />then that student (2.2) would fall into the Merit value and be given that as their final award.';
$string['qualuniqueid'] = 'Code/UniqueID';
$string['qualsearchpar'] = 'Qualification Search Parameters';
$string['qualsuniton'] = 'Qualifications This Unit Is On';
$string['qualpicker'] = 'Pick Another Qualification';
$string['qualselectheading'] = 'Select A Qualification';
$string['qualstudentheading'] = 'Step 1 : Add/Remove Students from Qualification';
$string['qualstudents'] = 'Students on this Qualification';
$string['qualaward'] = 'Qual Award';
$string['qualweightings'] = 'Qual Weightings';
$string['qualsnotfound'] = 'Quals Not Found';
$string['qualsettingsandtests'] = 'Qualification Settings and System Tests';

//R
$string['rank'] = 'Rank/Order';
$string['reportnogcse'] = 'No GCSE';
$string['reportnopl'] = 'No QOE';
$string['remove'] = 'Remove';
$string['ranges'] = 'Ranges';
$string['report'] = 'Report';
$string['run'] = 'Run';
$string['ranking'] = 'Ranking';

$string['reports:pl:numrecords'] = 'No. PL Records';
$string['reports:pl:percentwith'] = '% With PL';
$string['reports:pl:percentwithout'] = '% Without PL';
$string['reports:pl:avggcsescore'] = 'Avg GCSE Score';
$string['reports:pl:percentwithavggcse'] = '% With Avg GCSE';
$string['reports:pl:percentwithoutavggcse'] = '% Without Avg GCSE';
$string['reports:pl:avgnumrecords'] = 'Avg No. PL Records';
$string['reports:pl:numwith'] = 'No. With PL';
$string['reports:pl:numwithout'] = 'No. Without PL';
$string['reports:pl:numwithavggcse'] = 'No. With Avg GCSE';
$string['reports:pl:numwithoutavggcse'] = 'No. Without Avg GCSE';
$string['reports:pl:enggcse'] = 'English GCSE';
$string['reports:pl:mathsgcse'] = 'Maths GCSE';
$string['reports:bcgt:numwithqual'] = 'No. With Qual';
$string['reports:bcgt:numwithoutqual'] = 'No. Without Qual';
$string['reports:bcgt:numwithtargetgrade'] = 'No. With Target Grade';
$string['reports:bcgt:numwithouttargetgrade'] = 'No. Without Target Grade';
$string['reports:bcgt:quals'] = 'Qualifications';
$string['reports:bcgt:targetgrade'] = 'Target Grade';
$string['reports:bcgt:weightedtargetgrade'] = 'Weighted Target Grade';
$string['reports:bcgt_target_grades:aspgrades'] = 'Aspirational Grade';

//S
$string['save'] = 'Save';
$string['search'] = 'Search';
$string['searchstudent'] = 'Student Search';
$string['searchStudent'] = 'Student Search';
$string['searchunit'] = 'Unit Search';
$string['selectallstudentsqual'] = 'Select all Students for this Qual';
$string['selectallusersquals'] = 'Select all Qualifications for this Student';
$string['searchass'] = 'Search Assessment';
$string['searchclass'] = 'Search Qual';
$string['selectcourse'] = 'Course Select';
$string['selectqual'] = 'Select a Qualification';
$string['selectunit'] = 'Select a Unit';
$string['shortname'] = 'Shortname';
$string['shortgrade'] = 'Short Grade';
$string['signoffsheets'] = 'Sign-off Sheets';
$string['simplereportsortinst'] = 'Sort : To sort, click a column name. To reset, click the tab name again.';
$string['students'] = 'Students';
$string['student'] = 'Student';
$string['studentsquals'] = 'Students Qualifications';
$string['studentsonheading'] = 'Students On This Qualification';
$string['studentschooseheading'] = 'Students to Select From';
$string['specifictargetgrade'] = 'Specific Target';
$string['st'] = 'ST';
$string['status'] = 'Status';
$string['studentchoose'] = 'Students to Choose From';
$string['step2editstuunits'] = 'Step 2 : Edit Students Units';
$string['studentsunitheading'] = 'Select Students Units';
$string['studentsunitheadingstage'] = 'Select Students Units';
$string['studentsearch'] = 'Search Courses By Student';
$string['subject'] = 'Subject';
$string['subtypes'] = 'Subtypes';
$string['subtype'] = 'Subtype';
$string['success'] = 'Success';

//T
$string['t'] = 'T';
$string['targets'] = 'Targets';
$string['target'] = 'Target';
$string['targetdate'] = 'Target Date';
$string['targetgrade'] = 'Target Grade';
$string['targetgrades'] = 'Target Grades';
$string['targetgradestestdesc'] = 'This will allow you to input an average gcse score and show the target grades the system calculates';
$string['targetgrades:coursetypes:desc'] = 'Can grades be set against just child courses? Meta courses? Or both?';
$string['tasks'] = 'Tasks';
$string['tddesc'] = 'This will import the students Target Grades and/or average gcse scores. It will attempt to find the Student, Qualification and checks if the student is on the Qualification. If the target grade is specified then it will attempt to find this grade in the database. If the average score is specified it will insert this into the database. If "process" is specified it will attempt to calculate the target grade based on the average score.<br /><br />If the student already has an average score or target grade for a qualification, it will update.';
$string['teachersonheading'] = 'Teachers On This Qualification';
$string['teacherschooseheading'] = 'Teachers to Select From';
$string['teachersquals'] = 'Teachers Qualifications';
$string['teachersearch'] = 'Search Courses By Teacher';
$string['tgcreatemissingfulltarget'] = 'Insert Full Target Grade';
$string['tgcreatemissingfulltargetdesc'] = 'If selected this will create any full target grade that is not in the system but is in the csv';
$string['tgcreatemissingtargetgrade'] = 'Insert Target Grade';
$string['tgcreatemissingtargetgradedesc'] = 'If selected this will create any target grade that is not in the system but is in the csv';
$string['tgimportsum1'] = 'Number of Target Grades inserted';
$string['tgimportsum2'] = 'Number of Overal; Target Grades not found';
$string['tgimportsum3'] = 'Number of Grades not found';
$string['tgimportsum4'] = 'Number of Qualifications Not Found';
$string['theyarelinkedqualsstuds'] = 'They are linked to the following qualifications and students';
$string['theyarelinkedcoursesusers'] = 'They are linked to the following courses and users';
$string['toomanyusers'] = 'There are too many users to display';
$string['trackinggrid'] = 'Tracking Grid';
$string['transferstudentsunitshelp'] = 'Transfer a students units from one qualification to another';
$string['transferstudentsunits'] = 'Transfer Students Units';
$string['transferunits'] = 'Transfer Units';
$string['tutorsmentees'] = 'Tutors Mentees';
$string['type'] = 'Type';

//U
$string['udoverwrightdata'] = 'Overright New Data';
$string['udoverwrightdatadesc'] = 'This will ignore data in the database even if it has been updated by moodle. It will simply take the csv data and alter the moodle data. If this is left unchecked it will check he date update date in moodle. If this is newer than the csv one then it will not overright';
$string['uddesc'] = 'This will attempt to import the user data. There are three files required. The User Criteria Data, the User Unit Data and the User Award Data';
$string['unitdetails'] = 'Unit Details';
$string['units'] = 'Units';
$string['unit'] = 'Unit';
$string['unitname'] = 'Unit Name';
$string['unittype'] = 'Unit Type';
$string['uniqueid'] = 'Unique ID';
$string['unitdetails'] = 'Details/Description';
$string['unitlistdetails'] = 'Level : UniqueID : Name';
$string['unitchooseheading'] = 'Units to choose from';
$string['unitsquals'] = 'The Units Qualifications';
$string['unitsearch'] = 'Unit Search';
$string['unitaward'] = 'Unit Award';
$string['unitgradingstructurehelp'] = 'Create a new grading structure to use to calculate unit awards.';
$string['unitgradingpointshelp'] = 'The points you assigned to grades here are used in the calculation for the overall qualification final award, to create the avg score of units which will correspond with the lower & upper ranges of the QUALIFICATION grading structure which you created earlier';
$string['unitgradingrangehelp'] = 'The ranges you assign here will be used to choose a unit award, based on an average calculation of the points the student has gained on the unit\'s criteria. E.g. If they had an avg critera score of 1.8, and you had a unit gradnig of Merit (1.6-2.5) it would fall between there and their unit award would be calculated as a merit.';
$string['unitdataimportedsuccess'] = 'Number of Unit rows processed succefully';
$string['unitdataupdated'] = 'Number of Unit rows led to an update';
$string['unitdatainserted'] = 'Number of Unit rows led to an insert';
$string['unitsnotfound'] = 'Units not found';
$string['unitsaved'] = 'Unit Saved!';
$string['unitsearchhelp'] = 'Find units that are on a Qualification that has these parameters';
$string['unitsearchpar'] = 'Unit Search Parameters';
$string['unitfamily'] = 'Unit Family';
$string['unitgrid'] = 'Unit Grid';
$string['upperrangescore'] = 'Upper Range Score';
$string['usercriteriacsv'] = 'User Criteria CSV';
$string['userunitcsv'] = 'User Unit CSV';
$string['userawardcsv'] = 'User Award CSV';
$string['users'] = 'Users';
$string['user'] = 'User';
$string['userdata'] = 'User Data';
$string['userschoose'] = 'Users to Choose From';
$string['username'] = 'Username';
$string['usepercentcompleteonunits'] = 'Use % Completion on Units';
$string['unittests'] = 'System Tests';
$string['ucaspoints'] = 'Ucas';

//V


$string['va'] = 'VA';
$string['view'] = 'View';
$string['viewactivitylinks'] = 'Assignment & Activity Links';
$string['viewadvanced'] = 'View Advanced';
$string['viewallassessments'] = '(Back To All Assessments)';
$string['viewEditBy'] = 'View/Edit Grid By';
$string['viewnonedit'] = 'Return to View';
$string['viewsimple'] = 'View Simple';

//W
$string['weighting'] = 'Weighting';
$string['withdistinctionaward'] = 'Diss';
$string['withmeritaward'] = 'Merit';
$string['withpassaward'] = 'Pass';
$string['withunitdoingunit'] = 'With Unit Award/Doing Unit';
$string['wqdesc'] = 'This will import the Qualification Weightings (Alps weightings and coefficients) into the database. It will attempt to find the qualification in the database. If it cant find it it will attempt to create it. If you choose to "process" the file then the users on this Qualification will have their weighted target grades calculated.<br /><br />If the qualification already has weightings, it will update them.';
$string['weightedbreakdown'] = 'Weighted Target Breakdown';
$string['weightedtargetgrade'] = 'Weighted Target Grade';
//X


// Y
$string['year'] = 'Year';

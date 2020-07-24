# Automated Template Import Plugin
### Repository: https://github.com/stemiwe/moodle-local_template_import
### Author: Stefan Weber <stewe1@gmx.de>


## Summary

This plugin makes it easy to create pre-defined courses which can be imported into any new course with a single mouseclick.

If a suitable template exists for a given course, teachers will be presented with a simple popup upon opening their course, which enables them to directly import a pre-defined backup file that is selected using their course's shortname using predefined criteria.

Alternatively, template files can also be imported via a link in the course settings dropdown menu.

Administrators must define the naming conventions for template files, and add course backup files to the list of available templates.

As of now the restoration process cannot be customized. Backup files will always be restored with these settings:
- Replace and delete original course
- Do not include users/enrolments from backup file
- Keep users/enrolments/groups in target course
- Overwrite course configuration & fields
- Do not overwrite course names & start date


## Installation and Configuration

Place this plugin in your /local subfolder.

Once installed, administrators have to set the naming convention for the template files and how to extract the template identifier for the course from the course shortname. These settings can be found and changed in the main site administration menu in tab "site administration" after installation.

The plugin creates four new capabilities upon installation:
- local/template_import:seepopup - users with this capability will receive the popup notification about available templates to import. By default, teachers have this capability.

- local/template_import:dismisspopup - users with this capability are able to dismiss the popup notification about available templates to import. By default, teachers have this capability. You can force teachers to import a template by taking away this permission.

- local/template_import:importviamenu - shows the "automated template import" link in the course menu settings, to enable users to import templates without the popup. By default, teachers have this capability.

- local/template_import:createtemplatefile - shows the "create template from backup file" link in the course menu settings, to enable users to set this course's backup files as templates. By default, only admins can do this.


## How to use

1. Go to the admin settings for this plugin (can be found and changed in the main site administration menu in tab "site administration" after installation), activate the plugin and select at which course id it should start presenting the popup (this is to make sure that already existing courses will not ever get a popup notification). Older courses can still import templates via the link in the course settings menu.

2. Define the naming convention for course shortnames. The plugin will search for a suitable template using the course shortname. If your course shortnames include other elements, you can cut them out.

Example:
Your course shortnames are of the format "ABC-1-WS2020-XXXXX/123", where "XXXXX" is the shortcode for the type of this course.

- Set "Cut shortname before" to "ABC-1-WS2020-"
- Set "Cut shortname after" to "/".
"XXXXX" will be used as this course's identifier.

3. Define the naming convention for course backup files to be used as templates.

Example:
- Set "Template prefix" to "TEMPLATE-".

4. Create a backup file in any course's backup area and use the template prefix set in step 3.

Example:
- Go to any course and create a backup file named "TEMPLATE-XXXXX.mbz"

5. Add the backup files to the list of templates.

Example:
- In the course which holds the backup file, select "Create templates from backup files" from the course settings menu. The backup files will be added to the list of available backup files.

6. Create a course that can use the template

Example:
- Create a course with a shortname of "ABC-1-WS2020-XXXXX/123"
- Open the course - you should see the popup asking you to import the template.
- Click OK to import the template.


## Setting new template files

By default, only admins can create new template files.

Please note that no matter which course you are adding new template files from, every single import from any course will first check all available template files and delete any files that no longer exist from the list of templates.

Also note that you cannot add files with duplicate file names to the list of templates. Use the list of current template files to locate and delete any unwanted files and repeat the process.

To add new template files:

1. Create a standard backup file using default settings in any course, and set its filename to something that fits your naming convention for backup files.

(Note that changing the settings might put the backup file into the user's personal backup area, instead of the course backup area, and will thus make it impossible to add these files to the list of templates)

2. Select "Create templates from backup files" from the course settings dropdown in the same course.


## Reports

The list of currently active template files can be viewed from any course by picking "Create templates from backup files" from the course settings dropdown.

To see a list of courses which used the template import function, go to Admin Settings -> Automated Template Import -> Report of completed restores.

# uhleloX

uhleloX is a modern PHP Content Management System.

Build on PHP 8, jQuery 3 and Bootstrap, it provides a lightweight and powerful CMS experience to powerusers.
A number of core plugins available add features such as Media Editor, Text Editor and Media Upload.

# Install
- Prepare an Apache server with PHP 8 and a MySQL Database
- Download and unzip the latest Release
- Place the unzipped folder's *contents* in your webservers *root*
- Visit your website (i.g. https://domain.tld)
- Complete the setup steps. You will need to enter your Database host, name, port, charset and user/password.
- Create a User in the next prompt
- Login to the CMS in the last prompt

# Setup
Add new settings in `/admin.php?x_action=add&x_type=settings`
- Slug: `x_site_url`, Value: `https://domain.tld` (your site URL)
- Slug: `x_upload_max_size`, Value: `999999999` (value in bites defining max upload file size)
- Slug: `x_active_template`, Value: `uhlelox-template` (unless you have a custom template, use that temlate's slug)
- Slug: `x_field_type_mugshot`, Value: `img` (this is a dynamic setting defining the input type of a field in edit scree. Setting slug can be `x_field_type_{field_slug}`, value can be `img` [image input], `owner` [select2 with users])

Activate extensions in `/admin.php?x_action=add&x_type=extensions`
- Slug: `x-ck-editor`, Status: `active` (activates the CK Editor on edit screens)
- Slug: `x-media-browser`, Status: `active` (activates a media browser in the Editors)
- Slug: `x-file-robot`, Status: `active` (activates media editor)

# Relationships
Add new relationships in `/admin.php?x_action=add&x_type=relationships`
- Slug: `user_page` (SHOULD be singular database table names separated by underscores)
- Type: `m2m` (none else supported yet)
- Entity_a: `database table name` (the "left" partner in a relationship, MUST be database table name)
- Entity_b: `database table name` (the "right" partner in a relationship, MUST be database table name)
You can now connect any item of "left" database table to any item of "right" database table name.

# Changelog

### 25-06-2022
[Added] First Beta Release

### 29-04-2022 
[Added] Initial Commit

# Contributing 

Everyone is welcome to contribute by opening a PR or an Issue in this project.
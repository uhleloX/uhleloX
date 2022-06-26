# uhleloX

uhleloX is a modern PHP Content Management System.

Build on PHP 8, jQuery 3 and Bootstrap, it provides a lightweight and powerful CMS experience to powerusers.
A number of core plugins available add features such as Media Editor, Text Editor and Media Upload.

A number of big CMS have "ruled" the web for the past years, and they are excellent. However, many of them are built on very outdated code, and have a number of security issues and user shortcomings that uhleloX tries to fix.

One of the biggest differences to any common CMS (inclusive WordPress) is that uhleloX can *not* be taken over during install, because it uses a very simple but effective verification of the owner during install.
Other differences are: 
- uhleloX is very modular. In its most basic version, all you get is a *Content Management System*. Using *Core Extensions*, developed directly by uhleloX, and shipped in the install (but not activated) you can extend uheloX with very modern and powerful text editors, image editors and media management features.
- uhleloX uses a normalized, compressed database structure. No JSON or seralised arrays are stored. It has a native relational Database that can be infinitely extended, inclusive translations. This grants major performance advantages compared to traditional PHP CMSs.
- Strict SSL and CSPs are applied natively in uhleloX, making it literally *impossible* to run an unsafe HTTP website or loading any scripts from external sources. Everything has to be loaded with SSL and from your own server.
- uhleloX is not owned by anyone. While the GitHub and Website Servers are sponsored by TukuToi, and currently development is as well made by TukuToi, the project as such is intended to be open to anyone _contributing_. Leadesrhip is approached with the [Liberal contribution model](https://opensource.guide/leadership-and-governance/#what-are-some-of-the-common-governance-structures-for-open-source-projects). uhleloX is updated _directly_ from GitHub, removing the necessity of Update Server alltogether.

uhleloX also uses several features of other CMSs that make them so powerful:
- Just like WordPress, uhleloX also offers a set of PHP Hooks that can be used to extend core functionality.
- Just like WordPress, uhleloX also offers the so powerful (and so much neglected) ShortCodes

*Note: some of the above features or approaches are still under development*

# Install
- Prepare an Apache or nginX server with PHP 8 and a MySQL Database. 
Note, in nginX you have to ensure pretty url rewrites on your own. Try something like below in your `server` block. On Apache, uhleloX already includes an .htaccess rewrite rule.
```
location / {
	try_files $uri $uri/ /index.php?$args; 
}
```
- Download and unzip the latest Release
- Unzip the downloaded package, open the folder and find the `hash.txt` file in it.
- Create an SHA256 hash of a passphrase of your choice that you will be using to:
-- Setup the install
-- Create new users
-- maybe in future log in to the system
==> A possible online service to create an SHA256 hash is https://emn178.github.io/online-tools/sha512.html
- Paste the SHA 256 hash of your passphrase in the `hash.txt` file and save it
- Upload the entire folder *contents* to your webserver's *root*
- Visit your website (i.g. https://domain.tld)
- Complete the setup steps. You will need to enter your Database host, name, port, charset, passphrase and user/password, thus have them ready.
- Create a User in the next prompt
- Login to the CMS in the last prompt

# Setup
**Settings**
All necessary settings for the default to work are added during setup
Add new settings in `/admin.php?x_action=add&x_type=settings`

**Extensions**
All currently existing core extensions are active by default.
Activate extensions in `/admin.php?x_action=add&x_type=extensions`
Deactivate existing extenstions in `/admin.php?x_action=list&x_type=extensions`

# Relationships
Add new relationships in `/admin.php?x_action=add&x_type=relationships`
- Slug: `{entity-a}_{entity-b}` (SHOULD be singular database table names separated by underscores)
- Type: `m2m` (none else supported yet)
- Entity_a: `database table name` (the "left" partner in a relationship, MUST be database table name)
- Entity_b: `database table name` (the "right" partner in a relationship, MUST be database table name)
You can now connect any item of "left" database table to any item of "right" database table name.
NOTE: currently only inbuilt tables are accepted for new relationships. 

# Todo and Future Plans
*A lot, and many*
- finalise front end display, routing, templating, paginating
- finalise user management and role/capabilities
- finalise API (`event()`, currently `add_action` and `add_filter`)
- ... + ...

# Contributing 

Everyone is welcome to contribute by opening a PR or an Issue in this project.
=== Post Summarizer ===
Contributors:      Sanjucta Ghose
Tags:              block, summary
Tested up to:      6.7
Stable tag:        0.1.0
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Plugin that adds a block to generate a summary for a post

== Description ==

Plugin that adds a block to generate a summary for a post. This is essentially a fun project to help me explore writing custom Gutenberg blocks
while playing around with the OpenAPI Chat Completion API

== Usage ==

You need to add your OpenAPI API key to your wp-config.php file as the value of the constant - VIRIDIANSG_API_KEY

I've used @wordpress/env to set up a local dev environment. If you want to spin up a local env quickly to see how the plugin works this is what you need to do :

- The project contains a .wp-env.json file.
- Create a copy of the file and rename it to .wp-env.override.json.
- Update VIRIDIANSG_API_KEY to your OpenAPI API key.
- Run npm install
- Run composer install
- To start a local WordPress install run npm run env:start


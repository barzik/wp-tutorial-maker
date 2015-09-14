# WP Tutorial Maker

WP tutorial maker will make your selected categories into tutorial style categories.
The posts inside the categories will include previous and next links to other tutorials articles.
WP Tutorial Maker allow you to select some categories in your site and makes them into tutorial categories.

1. The category will be ordered by the creation date in ascending order.
2. You can choose if each post in the category will have navigational to the next post and to the last post.
You can also choose the position of those links - before or after the content.
3. You can choose to add a link back to the category and choose the text of the link and a text that will appear next to
the link.

After activating WP Tutorial Maker, you can go to any category in your WordPress and designate it as 'Tutorial Category'.

# Installation and other information

For Installations, screenshots and logs, please refer to [WP tutorial maker WordPress.org page](https://wordpress.org/plugins/wp-tutorial-maker/). 

# Automated testing

WP Tutorial maker can be tested by using PHPUnit with the official WordPress testing environment.

1. Install WordPress develop and PHPUnit. You can follow [those instructions](https://make.wordpress.org/core/handbook/testing/automated-testing/)
2. Define local variable WP_TESTS_DIR with the location of WordPress develop phpunit folder. for example, put
put `export WP_TESTS_DIR="/var/www/html/wordpress-develop/tests/phpunit"` in .bashrc (Linux)
3. Go to the plugin main folder and run `phpunit`.
4. Tests coverage report is being printed in HTML page to ./log/CodeCoverage.


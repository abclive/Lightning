Lightning
=========

A lite yet simple PHP framework. Using an intelligent package based dependencies system, Lightning load only the files you really need.

**Write code as faster as your website run**

Getting Started
------------------
**Hello World Tutorial**

First of all to understand the basis of Lightning logic you have to understand the MVC design pattern. Lightning kind of fork this pattern to make his very own. The architecture is as it follow:
- Modules
- Bundles
- Views

So what are the differences between this and a classic MVC pattern. The Modules are optional, there are kind of plugins that add functions and tools to your website to make the framework yours. The Bundles are a mix of what we call Controllers and Models. So basically all the query to the database and the modification of the results occurs in them.

So now that we know that let's dive into it.

---

**Configure the framework**

First of all you have to configure the Framework. You will find in the Core folder a file named Configuration.php. This file contains all the essentials informations the framework needs to run correctly.

**$base_dir** is the root adress of your website, it will be used to handle routes correctly

**$routes_params** is an array setting how your routes are going to be organized. It's divided in three parts that must be included: "locale", "bundle", and "action". If you leave it by default the root will look like this : yoursite.com/(locale/)bundle/action. But it can be customized as well and the locale is optional.

**$default_method_call** is the name of your default action. If none specified in the address bar the framework will launch this one by default.

**$index_bundle** is the name of your site default bundle. The root of your website. For the tutorial we gonna set it to Home.

Now that the framework is correctly configured you're ready to go!

------------
**Create your first bundle**

Now you have to create your homepage bundle. So go to the Bundles folder and create a new file called Home.php in it. Bundles follow strict naming rules, you have to name it with the first letter in capital and the other ones in lower case. And it have of course, to be named as the class name.

So now create your Home class and make it extends Bundle so you can get something like this:

	<?php
	
	class Home extends Bundle
	{
	
	}
In order to load it correctly you need to implements the parent constructor like as follow:			

	<?php
	
	class Home extends Bundle
	{
		public function __construct()
		{
			parent::__construct();
		}
	}
And now we gonna add the default method name you configured, by default it's named index. And in this method we gonna say Hello to the World. Let's see what it's doing.

	<?php
	
	class Home extends Bundle
	{
		public function __construct()
		{
			parent::__construct();
		}
	
		public function index()
		{
			echo "Hello World!";
		}
	}
And that's all you need! Now go to your website you should see your printed Hello World!

**More tutorials to come!**

More infos
-------------
http://www.bukkit.fr/index.php/topic/14366-lightning-un-framework-l%C3%A9ger-et-facile/

---

Download the latest release
---------------------------------
https://github.com/abclive/Lightning/releases



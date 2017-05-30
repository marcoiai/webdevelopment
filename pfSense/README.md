# Captive Portal with Facebook authentication for pfSense.

Ok so I needed to implement FB login and couldn't find a simple and transparent solution. It can be much better and elegant, but for your better understanding, one file is better.

1. Install Facebook SDK: composer require facebook/graph-sdk

2. Configure your Facebook Application

3. Allow facebook host in pfSense: Services -> Captive Portal -> Allowed Hostnames

4. Modify index.php to configure your app_id, sdk location and other settings (file is self explanatory)

5. Upload through pfSense file manager: facebook-login.png

6. Upload through "Portal page contents" section of Captive Portal Service: index.php

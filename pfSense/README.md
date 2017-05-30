# Captive Portal with Facebook authentication for pfSense.

1. Install Facebook SDK: composer require facebook/graph-sdk

2. Configure your Facebook Application

3. Allow facebook host in pfSense: Services -> Captive Portal -> Allowed Hostnames

4. Modify index.php to configure your app_id, sdk location and other settings (file is self explanatory)

5. Download and upload through pfSense: index.php, facebook-login.png


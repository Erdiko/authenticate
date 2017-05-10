# Authenticate

[![Package version](https://img.shields.io/packagist/v/erdiko/authenticate.svg?style=flat-square)](https://packagist.org/packages/erdiko/authenticate)

**User Authentication**


Compatibility
-------------
This is compatible with PHP 5.4 or above and the latest version of Erdiko.

Requirements
------------
This package requires Pimple version 3.0 or above, and Symfony-security package version 3.2 or above.
If this dependency is not installed automatically when you run `composer update` in your project folder
please add to the project by

`composer require pimple/pimple`

`composer require symfony/security`

Installation
------------
Add package using composer 

`composer require erdiko/authenticate`

How to Use
----------
Before you start using this package, it needs some initial setup/config.

##### Add `authenticate.json` config.

In this file will be defined two major components, the first one related with storage and the other related with 
authentication.

For the storage we provided a SessionStorage service, but you can add your custom storage service just implementing 
`erdiko\authenticate\Services\Storage` interface and adding it to the config file.

Same way with the authentication, but in this case you will have to implement `erdiko\authenticate\Services\iAuth` 
interface. An example of this is the `Mock` class provided.
  
Here's an example of config file 
(you can copy from `<project>/vendor/erdiko/authenticate/app/config/default/authenticate.json`)
  
``` 
{
  "authentication": {
    "available_types": [{
      "name": "jwt_auth",
      "namespace": "erdiko_authenticate_services",
      "classname": "JWTAuthentication",
      "enabled": true
    }]
  },
  "storage": {
    "selected": "session",
    "storage_types": [{
      "name": "session",
      "namespace": "erdiko_authenticate_Services",
      "classname": "SessionStorage",
      "enabled": true
    }]
  }
}
```  

As we mention above, the _authentication_ will define the available classes that 
implements the user's validation logic. You can choose between a list of them defined in this config. For example, you 
can have one class that allows you to authenticate using oAuth methods, other that use LDAP, other that use database, 
and so on. 

Same for the storage section, except that you should use only one type at time, that's why this section has a `selected`
field.
 
Let's breakdown the config fields. 
In both cases:
* _**name**_: is the key will be used to references an individual class.
* _**namespace**_: represents a translated class namespace, e.g.: for `app\lib\service` should be `app_lib_services`, 
the rule is: replace back slash with underscore.
* _**classname**_: is the exact name of the class and it is case-sensitive.
* _**enabled**_: True, if it's available to use, false, if you want to disable temporarily.

##### Custom Authenticate class example
```
   class AuthTest implements iAuth
   {
        public function login( $credentials ) {
            $user = new User();
            $username = (array_key_exists('username', $credentials)) ? $credentials['username'] : '';
            $password = (array_key_exists('password',$credentials)) ? $credentials['password'] : '';
            $result = $user->authenticate($username, $password);
            return $result;
        }
   }
```

Special Thanks
--------------

Arroyo Labs - For sponsoring development, [http://arroyolabs.com](http://arroyolabs.com)

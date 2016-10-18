# Homegear-Framework
a small PHP framework to use with Homegear PHP scripting

for PHP scripts called from external PHP engine Homegear connection is made with Homegear XMLRPC-client (see https://github.com/Homegear/Homegear_PHP_XMLRPC_Client). When called with Homegear's builtin PHP engine, the internal Homegear PHP class is used.
All PHP scripts should be located in Homegear script directory (usually /var/lib/homegear/scripts). 
The XMLRPC-client should be located in /var/lib/homegear/scripts/HM-XMLRPC-Client/Client.php.

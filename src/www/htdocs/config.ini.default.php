;<?php
;/**
; * conjoon
; * (c) 2002-2009 siteartwork.de/conjoon.org
; * licensing@conjoon.org
; *
; * $Author$
; * $Id$
; * $Date$
; * $Revision$
; * $LastChangedDate$
; * $LastChangedBy$
; * $URL$
; */
; ?>
;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;                             README                                        ;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;
; This is the template for the application configuration file for the conjoon
; software.
; You should set the variables to the values that suits your installation and
; then rename this file to "config.ini.php".
;
;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;                         SECURITY WARNING                                  ;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;
; Do not remove the line below, or otherwise you risc exposing your application
; settings (i.e. passwords, db settings etc.). You can, however, remove everything
; between line 1 and 35.
;
; ------- DO NOT REMOVE THE FOLLOWING LINE ------
;<?php die('forbidden');?>
; --------- DO NOT REMOVE THE LINE ABOVE --------



;;;;;;;;;;;;;;;;;;;;;;;;
;;;   ENVIRONMENT    ;;;
;;;;;;;;;;;;;;;;;;;;;;;;
[environment]

; if your hoster does not allow you to configure php's include path, set this to
; the path where the "lib" folder resides.
; Notice: since the bootstrapper has to
; re-set the include path on every request, it is better to hardcode this value
; in your webserver's config.
; Example:
; UNIX: "/path1:/path2"
; Windows: "\path1;\path2"
; Notice:
; If you use this setting, make sure you quote the value if it contains a semicolon
; when specifying more than one directory, otherwise conjoon will interpret
; anything that follows the semicolon as a comment.
include_path =

; set this to the path where the "application" folder can be found.
; make sure the containing folders are readable and writable by the webserver.
; the path may be relative to the folder where the index.pho resides in.
application_path = ../

; set this to the path under which conjoon is available on your host.
; Example: If you configured your webserver so that conjoon is available
; under http://my.domain.com/, the value must be set to "/". If you configured
; your webserver so that the application is available under http://my.domain.com/app/,
; the value must be set to "app".
base_url = /

; The value should be set to anything that provides more infomration about the
; context conjoon is installed in. The value is not needed for any evaluation tasks,
; but will appear at some places in the software, such as the login screen.
; If you are running the software from an USB stick, the value for this property could
; be somewhat like "USB installation"
edition = web edition


;;;;;;;;;;;;;;;;;;;;;;;;
;;;   DATABASE       ;;;
;;;;;;;;;;;;;;;;;;;;;;;;
[database]
; set this value to the adapter conjoon should use for db connections.
; There is currently only support for pdo_mysql, so you should not change
; this value.
adapter = pdo_mysql

; database host
params.host = 127.0.0.1

; port your database server listens to. For mysql, this should default to
; "3306"
params.port = 3306

; username for db connections
params.username = user

; password for db connections
params.password = password

; the name of the database used
params.dbname = conjoon_db

; max_allowed_packet denotes the maximum site of a packet that can be stored
; in a database using the configured connection. If you have worked with
; MySQL before, this value should be familiar to you. However, it's used for
; all db adapters: Provide a value that is less than or equal to the
; "max_allowed_packet" variable of your MySQL server configuration (or any configuration
; setting of the database server you are using that corresponds to this variable's
; functionality). The value has to be provided in bytes.
; If left empty, conjoon will look up this value from the database configuration by itself.
; Setting this value is mainly important for the email module of conjoon, as emails vary in
; size and any email that is larger than max_allowed_packet cannot and wil not be stored
; into the database
variables.max_allowed_packet =
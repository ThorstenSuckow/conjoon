;<?php
;/**
; * conjoon
; * (c) 2002-2012 siteartwork.de/conjoon.org
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
; This is the file where you can specify a key which will be used to
; authenticate you to the conjoon Setup Assistant.
;
;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;;;                         SECURITY WARNING                                  ;;;
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
;
; Do not remove the line below, or otherwise you risc exposing your secret key.
;
; ------- DO NOT REMOVE THE FOLLOWING LINE ------
;<?php die('forbidden');?>
; --------- DO NOT REMOVE THE LINE ABOVE --------



; To authenticate yourself in order to use the conjoon Setup Assistant,
; you need to change the value of he following variable.
; You can then use this key to use the Setup Assistant.
;
; You must not leave this empty, otherwise you will not be able to access
; the Setup Assistant.
;
; Here's an example of how a secure authorization key would look like:
;
; CONJOON_AUTHORIZATION_KEY = jk1lWf89
CONJOON_AUTHORIZATION_KEY =
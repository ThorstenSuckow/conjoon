;<?php
;/**
; * conjoon
; * (c) 2007-2014 conjoon.org
; * licensing@conjoon.org
; *
; * conjoon
; * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
; *
; * This program is free software: you can redistribute it and/or modify
; * it under the terms of the GNU Affero General Public License as
; * published by the Free Software Foundation, either version 3 of the
; * License, or (at your option) any later version.
; *
; * This program is distributed in the hope that it will be useful,
; * but WITHOUT ANY WARRANTY; without even the implied warranty of
; * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
; * GNU Affero General Public License for more details.
; *
; * You should have received a copy of the GNU Affero General Public License
; * along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
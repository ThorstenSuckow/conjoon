/**
 * conjoon
 * (c) 2007-2015 conjoon.org
 * licensing@conjoon.org
 *
 * conjoon
 * Copyright (C) 2014 Thorsten Suckow-Homberg/conjoon.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
 * $Author$
 * $Id$
 * $Date$
 * $Revision$
 * $LastChangedDate$
 * $LastChangedBy$
 * $URL$
 */

Ext.namespace('com.conjoon.cudgets.util');

com.conjoon.cudgets.util.MimeIconFactory = function() {

    return {

        getIconCls : function(mime)
        {
            switch (mime) {

                // images
                case 'image/bmp':
                case 'image/gif':
                case 'image/jpeg':
                case 'image/jpg':
                case 'image/pjpeg':
                case 'image/png':
                case 'image/x-citrix-pjpeg':
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-image';

                case 'application/msword':
                case 'application/vnd.oasis.opendocument.text':
                case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-wordprocessing';

                case 'application/pdf':
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-pdf';

                case 'application/ogg':
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-sound';

                case 'application/x-gzip':
                case 'application/x-zip-compressed':
                case 'application/zip':
                case 'application/rar':
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-compressed';

                case 'application/vnd.ms-excel':
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-spreadsheet';

                case 'application/vnd.ms-powerpoint':
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-presentation';

                case 'application/x-httpd-php':
                case 'application/x-php':
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-php';

                case 'video/x-ms-wmv':
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-video';

                case 'text/x-vcard':
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-vcard';

                case 'application/pgp-signature':
                case 'application/pkcs7-signature':
                case 'application/x-pkcs7-signature':
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-signed';

                case 'text/x-patch':
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-patch';

                case 'message/delivery-status':
                case 'message/rfc822':
                case 'multipart/mixed':
                case 'multipart/signed':
                case 'text/rfc822-headers':
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-message';

                case 'text/css':
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-stylesheet';

                case 'text/html':
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-html';

                case 'text/directory':
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-directory';

                case 'text/plain':
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-txt';

                //case 'application/force-download':
                //case 'application/ms-tnef':
                //case 'application/octet-stream':

                default:
                    return 'com-conjoon-cudgets-util-MimeIconFactory-mimeType-unknown';
            }
        }


    };


}();
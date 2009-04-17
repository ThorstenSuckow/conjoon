/**
 * List compiled by mystix on the extjs.com forums.
 * Thank you Mystix!
 *
 * Hungarian Translations (utf-8 encoded)
 * by Amon <amon@theba.hu> (27 Apr 2008)
 * encoding fixed by Vili (17 Feb 2009)
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Bet�lt�s...</div>';

if(Ext.View){
  Ext.View.prototype.emptyText = "";
}

if(Ext.grid.GridPanel){
  Ext.grid.GridPanel.prototype.ddText = "{0} kiv�lasztott sor";
}

if(Ext.TabPanelItem){
  Ext.TabPanelItem.prototype.closeText = "F�l bez�r�sa";
}

if(Ext.form.Field){
  Ext.form.Field.prototype.invalidText = "Hib�s �rt�k!";
}

if(Ext.LoadMask){
  Ext.LoadMask.prototype.msg = "Bet�lt�s...";
}

Date.monthNames = [
  "Janu�r",
  "Febru�r",
  "M�rcius",
  "�prilis",
  "M�jus",
  "J�nius",
  "J�lius",
  "Augusztus",
  "Szeptember",
  "Okt�ber",
  "November",
  "December"
];

Date.getShortMonthName = function(month) {
  return Date.monthNames[month].substring(0, 3);
};

Date.monthNumbers = {
  'Jan' : 0,
  'Feb' : 1,
  'M�r' : 2,
  '�pr' : 3,
  'M�j' : 4,
  'J�n' : 5,
  'J�l' : 6,
  'Aug' : 7,
  'Sze' : 8,
  'Okt' : 9,
  'Nov' : 10,
  'Dec' : 11
};

Date.getMonthNumber = function(name) {
  return Date.monthNumbers[name.substring(0, 1).toUpperCase() + name.substring(1, 3).toLowerCase()];
};

Date.dayNames = [
  "Vas�rnap",
  "H�tfo",
  "Kedd",
  "Szerda",
  "Cs�t�rt�k",
  "P�ntek",
  "Szombat"
];

Date.getShortDayName = function(day) {
  return Date.dayNames[day].substring(0, 3);
};

if(Ext.MessageBox){
  Ext.MessageBox.buttonText = {
    ok     : "OK",
    cancel : "M�gsem",
    yes    : "Igen",
    no     : "Nem"
  };
}

if(Ext.util.Format){
  Ext.util.Format.date = function(v, format){
    if(!v) return "";
    if(!(v instanceof Date)) v = new Date(Date.parse(v));
    return v.dateFormat(format || "Y m d");
  };
}

if(Ext.DatePicker){
  Ext.apply(Ext.DatePicker.prototype, {
    todayText         : "Mai nap",
    minText           : "A d�tum kor�bbi a megengedettn�l",
    maxText           : "A d�tum k�sobbi a megengedettn�l",
    disabledDaysText  : "",
    disabledDatesText : "",
    monthNames        : Date.monthNames,
    dayNames          : Date.dayNames,
    nextText          : 'K�v. h�nap (CTRL+Jobbra)',
    prevText          : 'Elozo h�nap (CTRL+Balra)',
    monthYearText     : 'V�lassz h�napot (�vv�laszt�s: CTRL+Fel/Le)',
    todayTip          : "{0} (Sz�k�z)",
    format            : "y-m-d",
    okText            : "&#160;OK&#160;",
    cancelText        : "M�gsem",
    startDay          : 0
  });
}

if(Ext.PagingToolbar){
  Ext.apply(Ext.PagingToolbar.prototype, {
    beforePageText : "Oldal",
    afterPageText  : "a {0}-b�l/bol",
    firstText      : "Elso oldal",
    prevText       : "Elozo oldal",
    nextText       : "K�vetkezo oldal",
    lastText       : "Utols� oldal",
    refreshText    : "Friss�t�s",
    displayMsg     : "{0} - {1} sorok l�that�k a {2}-b�l/bol",
    emptyMsg       : 'Nincs megjelen�theto adat'
  });
}

if(Ext.form.TextField){
  Ext.apply(Ext.form.TextField.prototype, {
    minLengthText : "A mezo tartalma legal�bb {0} hossz� kell legyen",
    maxLengthText : "A mezo tartalma legfeljebb {0} hossz� lehet",
    blankText     : "K�telezoen kit�ltendo mezo",
    regexText     : "",
    emptyText     : null
  });
}

if(Ext.form.NumberField){
  Ext.apply(Ext.form.NumberField.prototype, {
    minText : "A mezo tartalma nem lehet kissebb, mint {0}",
    maxText : "A mezo tartalma nem lehet nagyobb, mint {0}",
    nanText : "{0} nem sz�m"
  });
}

if(Ext.form.DateField){
  Ext.apply(Ext.form.DateField.prototype, {
    disabledDaysText  : "Nem v�laszthat�",
    disabledDatesText : "Nem v�laszthat�",
    minText           : "A d�tum nem lehet kor�bbi, mint {0}",
    maxText           : "A d�tum nem lehet k�sobbi, mint {0}",
    invalidText       : "{0} nem megfelelo d�tum - a helyes form�tum: {1}",
    format            : "Y m d",
    altFormats        : "Y-m-d|y-m-d|y/m/d|m/d|m-d|md|ymd|Ymd|d"
  });
}

if(Ext.form.ComboBox){
  Ext.apply(Ext.form.ComboBox.prototype, {
    loadingText       : "Bet�lt�s...",
    valueNotFoundText : undefined
  });
}

if(Ext.form.VTypes){
  Ext.apply(Ext.form.VTypes, {
    emailText    : 'A mezo email c�met tartalmazhat, melynek form�tuma "felhaszn�l�@szolg�ltat�.hu"',
    urlText      : 'A mezo webc�met tartalmazhat, melynek form�tuma "http:/'+'/www.weboldal.hu"',
    alphaText    : 'A mezo csak betuket �s al�h�z�st (_) tartalmazhat',
    alphanumText : 'A mezo csak betuket, sz�mokat �s al�h�z�st (_) tartalmazhat'
  });
}

if(Ext.form.HtmlEditor){
  Ext.apply(Ext.form.HtmlEditor.prototype, {
    createLinkText : 'Add meg a webc�met:',
    buttonTips : {
      bold : {
        title: 'F�lk�v�r (Ctrl+B)',
        text: 'F�lk�v�rr� teszi a kijel�lt sz�veget.',
        cls: 'x-html-editor-tip'
      },
      italic : {
        title: 'Dolt (Ctrl+I)',
        text: 'Dolt� teszi a kijel�lt sz�veget.',
        cls: 'x-html-editor-tip'
      },
      underline : {
        title: 'Al�h�z�s (Ctrl+U)',
        text: 'Al�h�zza a kijel�lt sz�veget.',
        cls: 'x-html-editor-tip'
      },
      increasefontsize : {
        title: 'Sz�veg nagy�t�s',
        text: 'N�veli a sz�vegm�retet.',
        cls: 'x-html-editor-tip'
      },
      decreasefontsize : {
        title: 'Sz�veg kicsiny�t�s',
        text: 'Cs�kkenti a sz�vegm�retet.',
        cls: 'x-html-editor-tip'
      },
      backcolor : {
        title: 'H�tt�rsz�n',
        text: 'A kijel�lt sz�veg h�tt�rsz�n�t m�dos�tja.',
        cls: 'x-html-editor-tip'
      },
      forecolor : {
        title: 'Sz�vegsz�n',
        text: 'A kijel�lt sz�veg sz�n�t m�dos�tja.',
        cls: 'x-html-editor-tip'
      },
      justifyleft : {
        title: 'Balra z�rt',
        text: 'Balra z�rja a sz�veget.',
        cls: 'x-html-editor-tip'
      },
      justifycenter : {
        title: 'K�z�pre z�rt',
        text: 'K�z�pre z�rja a sz�veget.',
        cls: 'x-html-editor-tip'
      },
      justifyright : {
        title: 'Jobbra z�rt',
        text: 'Jobbra z�rja a sz�veget.',
        cls: 'x-html-editor-tip'
      },
      insertunorderedlist : {
        title: 'Felsorol�s',
        text: 'Felsorol�st kezd.',
        cls: 'x-html-editor-tip'
      },
      insertorderedlist : {
        title: 'Sz�moz�s',
        text: 'Sz�mozott list�t kezd.',
        cls: 'x-html-editor-tip'
      },
      createlink : {
        title: 'Hiperlink',
        text: 'A kijel�lt sz�veget linkk� teszi.',
        cls: 'x-html-editor-tip'
      },
      sourceedit : {
        title: 'Forr�s n�zet',
        text: 'Forr�s n�zetbe kapcsol.',
        cls: 'x-html-editor-tip'
      }
    }
  });
}

if(Ext.grid.GridView){
  Ext.apply(Ext.grid.GridView.prototype, {
    sortAscText  : "N�vekvo rendez�s",
    sortDescText : "Cs�kkeno rendez�s",
    lockText     : "Oszlop z�rol�s",
    unlockText   : "Oszlop felold�s",
    columnsText  : "Oszlopok"
  });
}

if(Ext.grid.GroupingView){
  Ext.apply(Ext.grid.GroupingView.prototype, {
    emptyGroupText : '(Nincs)',
    groupByText    : 'Oszlop szerint csoportos�t�s',
    showGroupsText : 'Csoportos n�zet'
  });
}

if(Ext.grid.PropertyColumnModel){
  Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
    nameText   : "N�v",
    valueText  : "�rt�k",
    dateFormat : "Y m j"
  });
}

if(Ext.layout.BorderLayout && Ext.layout.BorderLayout.SplitRegion){
  Ext.apply(Ext.layout.BorderLayout.SplitRegion.prototype, {
    splitTip            : "�tm�retez�s h�z�sra.",
    collapsibleSplitTip : "�tm�retez�s h�z�sra. Elt�ntet�s duplaklikk."
  });
}

/**
 * Romanian translations for ExtJS 2.1
 * First released by Lucian Lature on 2007-04-24
 * Changed locale for Romania (date formats) as suggested by keypoint
 * on ExtJS forums: http://www.extjs.com/forum/showthread.php?p=129524#post129524
 * Removed some useless parts
 * Changed by: Emil Cazamir, 2008-04-24
 * Fixed some errors left behind
 * Changed by: Emil Cazamir, 2008-09-01
 */

Ext.UpdateManager.defaults.indicatorText = '<div class="loading-indicator">Încarcare...</div>';

if(Ext.grid.GridPanel){
   Ext.grid.GridPanel.prototype.ddText = "{0} rând(uri) selectate";
}

if(Ext.TabPanelItem){
   Ext.TabPanelItem.prototype.closeText = "Închide acest tab";
}

if(Ext.form.Field){
   Ext.form.Field.prototype.invalidText = "Valoarea acestui câmp este invalida";
}

if(Ext.LoadMask){
    Ext.LoadMask.prototype.msg = "Încarcare...";
}

Date.monthNames = [
   "Ianuarie",
   "Februarie",
   "Martie",
   "Aprilie",
   "Mai",
   "Iunie",
   "Iulie",
   "August",
   "Septembrie",
   "Octombrie",
   "Noiembrie",
   "Decembrie"
];

Date.getShortMonthName = function(month) {
  return Date.monthNames[month].substring(0, 3);
};

Date.monthNumbers = {
  Ian : 0,
  Feb : 1,
  Mar : 2,
  Apr : 3,
  Mai : 4,
  Iun : 5,
  Iul : 6,
  Aug : 7,
  Sep : 8,
  Oct : 9,
  Noi : 10,
  Dec : 11
};

Date.getMonthNumber = function(name) {
  return Date.monthNumbers[name.substring(0, 1).toUpperCase() + name.substring(1, 3).toLowerCase()];
};

Date.dayNames = [
   "Duminica",
   "Luni",
   "Marti",
   "Miercuri",
   "Joi",
   "Vineri",
   "Sâmbata"
];

Date.getShortDayName = function(day) {
  return Date.dayNames[day].substring(0, 3);
};

if(Ext.MessageBox){
   Ext.MessageBox.buttonText = {
      ok     : "OK",
      cancel : "Renunta",
      yes    : "Da",
      no     : "Nu"
   };
}

if(Ext.util.Format){
   Ext.util.Format.date = function(v, format){
      if(!v) return "";
      if(!(v instanceof Date)) v = new Date(Date.parse(v));
      return v.dateFormat(format || "d.m.Y");
   };
}

if(Ext.DatePicker){
  Ext.apply(Ext.DatePicker.prototype, {
    todayText         : "Astazi",
    minText           : "Aceasta data este anterioara datei minime",
    maxText           : "Aceasta data este ulterioara datei maxime",
    disabledDaysText  : "",
    disabledDatesText : "",
    monthNames        : Date.monthNames,
    dayNames          : Date.dayNames,
    nextText          : 'Luna urmatoare (Control+Dreapta)',
    prevText          : 'Luna precedenta (Control+Stânga)',
    monthYearText     : 'Alege o luna (Control+Sus/Jos pentru a parcurge anii)',
    todayTip          : "{0} (Bara spa?iu)",
    format            : "d.m.Y",
    okText            : "&#160;OK&#160;",
    cancelText        : "Renun?a",
    startDay          : 0
  });
}

if(Ext.PagingToolbar){
  Ext.apply(Ext.PagingToolbar.prototype, {
    beforePageText : "Pagina",
    afterPageText  : "din {0}",
    firstText      : "Prima pagina",
    prevText       : "Pagina anterioara",
    nextText       : "Pagina urmatoare",
    lastText       : "Ultima pagina",
    refreshText    : "Împrospateaza",
    displayMsg     : "Afi?are înregistrarile {0} - {1} din {2}",
    emptyMsg       : 'Nu sunt date de afi?at'
  });
}

if(Ext.form.TextField){
   Ext.apply(Ext.form.TextField.prototype, {
      minLengthText : "Lungimea minima pentru acest câmp este de {0}",
      maxLengthText : "Lungimea maxima pentru acest câmp este {0}",
      blankText     : "Acest câmp este obligatoriu",
      regexText     : "",
      emptyText     : null
   });
}

if(Ext.form.NumberField){
   Ext.apply(Ext.form.NumberField.prototype, {
      minText : "Valoarea minima permisa a acestui câmp este {0}",
      maxText : "Valaorea maxima permisa a acestui câmp este {0}",
      nanText : "{0} nu este un numar valid"
   });
}

if(Ext.form.DateField){
  Ext.apply(Ext.form.DateField.prototype, {
    disabledDaysText  : "Indisponibil",
    disabledDatesText : "Indisponibil",
    minText           : "Data din aceasta caseta trebuie sa fie dupa {0}",
    maxText           : "Data din aceasta caseta trebuie sa fie inainte de {0}",
    invalidText       : "{0} nu este o data valida, trebuie sa fie în formatul {1}",
    format            : "d.m.Y",
    altFormats        : "d-m-Y|d.m.y|d-m-y|d.m|d-m|dm|d|Y-m-d"
  });
}

if(Ext.form.ComboBox){
  Ext.apply(Ext.form.ComboBox.prototype, {
    loadingText       : "Încarcare...",
    valueNotFoundText : undefined
  });
}

if(Ext.form.VTypes){
   Ext.apply(Ext.form.VTypes, {
      emailText    : 'Acest câmp trebuie sa contina o adresa de e-mail în formatul "user@domeniu.com"',
      urlText      : 'Acest câmp trebuie sa contina o adresa URL în formatul "http:/'+'/www.domeniu.com"',
      alphaText    : 'Acest câmp trebuie sa contina doar litere si _',
      alphanumText : 'Acest câmp trebuie sa contina doar litere, cifre si _'
   });
}

if(Ext.form.HtmlEditor){
  Ext.apply(Ext.form.HtmlEditor.prototype, {
    createLinkText : 'Va rugam introduceti un URL pentru aceasta legatura web:',
    buttonTips : {
      bold : {
        title: 'Îngrosat (Ctrl+B)',
        text: 'Îngrosati caracterele textului selectat.',
        cls: 'x-html-editor-tip'
      },
      italic : {
        title: 'Înclinat (Ctrl+I)',
        text: 'Înclinati caracterele textului selectat.',
        cls: 'x-html-editor-tip'
      },
      underline : {
        title: 'Subliniat (Ctrl+U)',
        text: 'Subliniati caracterele textului selectat.',
        cls: 'x-html-editor-tip'
      },
      increasefontsize : {
        title: 'Marit',
        text: 'Mareste dimensiunea fontului.',
        cls: 'x-html-editor-tip'
      },
      decreasefontsize : {
        title: 'Micsorat',
        text: 'Micsoreaza dimensiunea textului.',
        cls: 'x-html-editor-tip'
      },
      backcolor : {
        title: 'Culoarea fundalului',
        text: 'Schimba culoarea fundalului pentru textul selectat.',
        cls: 'x-html-editor-tip'
      },
      forecolor : {
        title: 'Culoarea textului',
        text: 'Schimba culoarea textului selectat.',
        cls: 'x-html-editor-tip'
      },
      justifyleft : {
        title: 'Aliniat la stânga',
        text: 'Aliniaza textul la stânga.',
        cls: 'x-html-editor-tip'
      },
      justifycenter : {
        title: 'Centrat',
        text: 'Centreaza textul în editor.',
        cls: 'x-html-editor-tip'
      },
      justifyright : {
        title: 'Aliniat la dreapta',
        text: 'Aliniaza textul la dreapta.',
        cls: 'x-html-editor-tip'
      },
      insertunorderedlist : {
        title: 'Lista cu puncte',
        text: 'Insereaza lista cu puncte.',
        cls: 'x-html-editor-tip'
      },
      insertorderedlist : {
        title: 'Lista numerotata',
        text: 'Insereaza o lista numerotata.',
        cls: 'x-html-editor-tip'
      },
      createlink : {
        title: 'Legatura web',
        text: 'Transforma textul selectat în legatura web.',
        cls: 'x-html-editor-tip'
      },
      sourceedit : {
        title: 'Editare sursa',
        text: 'Schimba pe modul de editare al codului HTML.',
        cls: 'x-html-editor-tip'
      }
    }
  });
}


if(Ext.grid.GridView){
   Ext.apply(Ext.grid.GridView.prototype, {
      sortAscText  : "Sortare ascendenta",
      sortDescText : "Sortare descendenta",
      lockText     : "Blocheaza coloana",
      unlockText   : "Deblocheaza coloana",
      columnsText  : "Coloane"
   });
}

if(Ext.grid.GroupingView){
  Ext.apply(Ext.grid.GroupingView.prototype, {
    emptyGroupText : '(Fara)',
    groupByText    : 'Grupeaza dupa aceasta coloana',
    showGroupsText : 'Afi?eaza grupat'
  });
}

if(Ext.grid.PropertyColumnModel){
  Ext.apply(Ext.grid.PropertyColumnModel.prototype, {
    nameText   : "Nume",
    valueText  : "Valoare",
    dateFormat : "d.m.Y"
  });
}

if(Ext.layout.BorderLayout && Ext.layout.BorderLayout.SplitRegion){
   Ext.apply(Ext.layout.BorderLayout.SplitRegion.prototype, {
      splitTip            : "Trage pentru redimensionare.",
      collapsibleSplitTip : "Trage pentru redimensionare. Dublu-click pentru ascundere."
   });
}

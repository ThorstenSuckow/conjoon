Ext.namespace('Ext.ux.layout');

/**
 * Overrides {Ext.layout.FitLayout}. Items will be shown/hidden with an
 * animation effect.
 *
 * NOTE:
 * Make sure each component which is managed by this layout has its
 * hideMode-property set to "visibility", otherwise you may experience
 * rendering problems.
 *
 * @class Ext.ux.layout.SlideLayout
 * @extends Ext.layout.FitLayout
 *
 * @author NeonMonk
 * @author Thorsten Suckow-Homberg <ts@siteartwork.de>
 *
 * @link http://www.extjs.com/forum/showthread.php?t=43120
 * Licensed under the WTFPL <http://sam.zoy.org/wtfpl/>
 */
Ext.ux.layout.SlideLayout = Ext.extend(Ext.layout.FitLayout, {

    deferredRender : false,

    renderHidden : false,

    easing : 'easeBoth',

    duration : .3,

    opacity : 1,


    setActiveItem : function(itemInt)
    {
        var container  = this.container;
        var cBody      = container.body;
        var cItems     = container.items;
        var cBodyX     = cBody.getX();
        var cBodyWidth = cBody.getWidth();

        if (typeof(itemInt) == 'string') {
            itemInt = cItems.keys.indexOf(itemInt);
        } else if (typeof(itemInt) == 'object') {
            itemInt = cItems.items.indexOf(itemInt);
        }

        var item = container.getComponent(itemInt);

        if (this.activeItem != item) {
            if (this.activeItem) {
                if (item && (!item.rendered || !this.isValidParent(item, container))) {
                    this.renderItem(
                        item,
                        itemInt,
                        container.getLayoutTarget()
                    );
                    item.show();
                }

                var s = [
                    cBodyX - cBodyWidth,
                    cBodyX + cBodyWidth
                ];

                w = this.activeItem;

                // this will hide the component once it got shifted out of the viewport
                w.hide.defer(this.duration, w);

                this.activeItem.el.shift({
                    duration : this.duration,
                    easing   : this.easing,
                    opacity  : this.opacity,
                    x        :(this.activeItemNo < itemInt ? s[0] : s[1] )
                });

                // whows the item if it was hidden before.
                item.show();
                item.el.setY(cBody.getY());
                item.el.setX((this.activeItemNo < itemInt ? s[1] : s[0] ));

                item.el.shift({
                    duration : this.duration,
                    easing   : this.easing,
                    opacity  : 1,
                    x        : cBodyX
                });
            }

            this.activeItemNo = itemInt;
            this.activeItem   = item;
            this.layout();
        }
    },

    renderAll : function(ct, target)
    {
        if(this.deferredRender){
            this.renderItem(this.activeItem, undefined, target);
        }else{
            Ext.layout.CardLayout.superclass.renderAll.call(this, ct, target);
        }
    }
});
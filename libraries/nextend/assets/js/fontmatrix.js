(function(dojo) {
    dojo.declare("NextendFontmatrix", null, {
        constructor: function(args) {
            dojo.mixin(this, args);
            this.table = dojo.byId(this.table);
            this.tbody = dojo.query('> tbody', this.table)[0];
            this.duplicates = dojo.query('.nextend-duplicate', this.table);
            for (var i = 0; i < this.duplicates.length; i++) {
                this.initRow(i);

            }
        },
        initRow: function(i) {
            this.duplicates[i].fonts = dojo.query('.nextend-fontmanager input', this.duplicates[i].parentNode)[0];
            dojo.connect(this.duplicates[i], 'click', dojo.hitch(this, 'duplicate', i));
            dojo.connect(dojo.query('.nextend-trash', this.duplicates[i].parentNode)[0], 'click', dojo.hitch(this, 'delete', i));
        },
        duplicate: function(i, e) {
            this.biggestindex++;
            var rowclass = dojo.hasClass(this.duplicates[this.duplicates.length - 1].parentNode.parentNode, 'odd') ? 'even' : 'odd';
            var tr = dojo.create('tr', {'class': rowclass});

            var labeltext = dojo.query('label input', this.duplicates[i].parentNode.parentNode);

            if (labeltext.length > 0) {
                labeltext = labeltext[0].value;
            } else {
                labeltext = dojo.query('label', this.duplicates[i].parentNode.parentNode)[0].innerHTML;
            }

            var html = this.make(this.name + 'custom' + this.biggestindex, labeltext + ' COPY ', this.duplicates[i].fonts.value);
            var label = dojo.create('td', {'class': 'nextend-label', innerHTML: html[0]}, tr);
            var element = dojo.create('td', {'class': 'nextend-element', innerHTML: html[1]}, tr);
            dojo.place(tr, this.tbody);

            this.duplicates[this.duplicates.length] = dojo.query('.nextend-duplicate', tr)[0];
            this.initRow(this.duplicates.length - 1);
            eval(html[2]);
        },
        delete: function(i, e) {
            dojo.destroy(this.duplicates[i].parentNode.parentNode);
            this.duplicates[i] = this.duplicates[i - 1];
            for (i++; i < this.duplicates.length; i++) {
                var tr = this.duplicates[i].parentNode.parentNode;
                var trminus1 = this.duplicates[i - 1].parentNode.parentNode;
                if(tr.parentNode == null){
                    this.duplicates[i] = this.duplicates[i-1];
                }else if (tr != trminus1) {
                    if (dojo.hasClass(trminus1, 'odd')) {
                        dojo.removeClass(tr, 'odd');
                        dojo.addClass(tr, 'even');
                    } else {
                        dojo.removeClass(tr, 'even');
                        dojo.addClass(tr, 'odd');
                    }
                }
            }
        },
        make: function(name, label, value) {

            return [
                this.html[0].replace(/\*\*name\*\*/g, name).replace(/\*\*label\*\*/g, label).replace(/\*\*value\*\*/g, value),
                this.html[1].replace(/\*\*name\*\*/g, name).replace(/\*\*label\*\*/g, label).replace(/\*\*value\*\*/g, value) + "<div class='nextend-duplicate'></div><div class='nextend-trash'></div>",
                this.js.replace(/\*\*name\*\*/g, name).replace(/\*\*label\*\*/g, label).replace(/\*\*value\*\*/g, value)
            ];
        }
    });
})(ndojo);
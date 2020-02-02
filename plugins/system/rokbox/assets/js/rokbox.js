/*
---
provides: moofx
version: 3.1.0
description: A CSS3-enabled javascript animation library
homepage: http://moofx.it
author: Valerio Proietti <@kamicane> (http://mad4milk.net)
license: MIT (http://mootools.net/license.txt)
includes: cubic-bezier by Arian Stolwijk (https://github.com/arian/cubic-bezier)
...
*/

(function(modules) {
    var cache = {}, require = function(id) {
        var module = cache[id];
        if (!module) {
            module = cache[id] = {};
            var exports = module.exports = {};
            modules[id].call(exports, require, module, exports, window);
        }
        return module.exports;
    };
    window["moofx"] = require("0");
})({
    "0": function(require, module, exports, global) {
        /*          .-   3
.-.-..-..-.-|-._.
' ' '`-'`-' ' ' '
*/
                "use strict";

        // color and timer
        var color = require("1"), frame = require("2");

        // if we're in a browser we need ./browser, otherwise ./fx
        var moofx = typeof document !== "undefined" ? require("7") : require("b");

        moofx.requestFrame = function(callback) {
            frame.request(callback);
            return this;
        };

        moofx.cancelFrame = function(callback) {
            frame.cancel(callback);
            return this;
        };

        moofx.color = color;

        // and export moofx
        module.exports = moofx;
    },
    "1": function(require, module, exports, global) {
        /*
color
*/
                "use strict";

        var colors = {
            maroon: "#800000",
            red: "#ff0000",
            orange: "#ffA500",
            yellow: "#ffff00",
            olive: "#808000",
            purple: "#800080",
            fuchsia: "#ff00ff",
            white: "#ffffff",
            lime: "#00ff00",
            green: "#008000",
            navy: "#000080",
            blue: "#0000ff",
            aqua: "#00ffff",
            teal: "#008080",
            black: "#000000",
            silver: "#c0c0c0",
            gray: "#808080",
            transparent: "#0000"
        };

        var RGBtoRGB = function(r, g, b, a) {
            if (a == null || a === "") a = 1;
            r = parseFloat(r);
            g = parseFloat(g);
            b = parseFloat(b);
            a = parseFloat(a);
            if (!(r <= 255 && r >= 0 && g <= 255 && g >= 0 && b <= 255 && b >= 0 && a <= 1 && a >= 0)) return null;
            return [ Math.round(r), Math.round(g), Math.round(b), a ];
        };

        var HEXtoRGB = function(hex) {
            if (hex.length === 3) hex += "f";
            if (hex.length === 4) {
                var h0 = hex.charAt(0), h1 = hex.charAt(1), h2 = hex.charAt(2), h3 = hex.charAt(3);
                hex = h0 + h0 + h1 + h1 + h2 + h2 + h3 + h3;
            }
            if (hex.length === 6) hex += "ff";
            var rgb = [];
            for (var i = 0, l = hex.length; i < l; i += 2) rgb.push(parseInt(hex.substr(i, 2), 16) / (i === 6 ? 255 : 1));
            return rgb;
        };

        // HSL to RGB conversion from:
        // http://mjijackson.com/2008/02/rgb-to-hsl-and-rgb-to-hsv-color-model-conversion-algorithms-in-javascript
        // thank you!
        var HUEtoRGB = function(p, q, t) {
            if (t < 0) t += 1;
            if (t > 1) t -= 1;
            if (t < 1 / 6) return p + (q - p) * 6 * t;
            if (t < 1 / 2) return q;
            if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
            return p;
        };

        var HSLtoRGB = function(h, s, l, a) {
            var r, b, g;
            if (a == null || a === "") a = 1;
            h = parseFloat(h) / 360;
            s = parseFloat(s) / 100;
            l = parseFloat(l) / 100;
            a = parseFloat(a) / 1;
            if (h > 1 || h < 0 || s > 1 || s < 0 || l > 1 || l < 0 || a > 1 || a < 0) return null;
            if (s === 0) {
                r = b = g = l;
            } else {
                var q = l < .5 ? l * (1 + s) : l + s - l * s;
                var p = 2 * l - q;
                r = HUEtoRGB(p, q, h + 1 / 3);
                g = HUEtoRGB(p, q, h);
                b = HUEtoRGB(p, q, h - 1 / 3);
            }
            return [ r * 255, g * 255, b * 255, a ];
        };

        var keys = [];

        for (var c in colors) keys.push(c);

        var shex = "(?:#([a-f0-9]{3,8}))", sval = "\\s*([.\\d%]+)\\s*", sop = "(?:,\\s*([.\\d]+)\\s*)?", slist = "\\(" + [ sval, sval, sval ] + sop + "\\)", srgb = "(?:rgb)a?", shsl = "(?:hsl)a?", skeys = "(" + keys.join("|") + ")";

        var xhex = RegExp(shex, "i"), xrgb = RegExp(srgb + slist, "i"), xhsl = RegExp(shsl + slist, "i");

        var color = function(input, array) {
            if (input == null) return null;
            input = (input + "").replace(/\s+/, "");
            var match = colors[input];
            if (match) {
                return color(match, array);
            } else if (match = input.match(xhex)) {
                input = HEXtoRGB(match[1]);
            } else if (match = input.match(xrgb)) {
                input = match.slice(1);
            } else if (match = input.match(xhsl)) {
                input = HSLtoRGB.apply(null, match.slice(1));
            } else return null;
            if (!(input && (input = RGBtoRGB.apply(null, input)))) return null;
            if (array) return input;
            if (input[3] === 1) input.splice(3, 1);
            return "rgb" + (input.length === 4 ? "a" : "") + "(" + input + ")";
        };

        color.x = RegExp([ skeys, shex, srgb + slist, shsl + slist ].join("|"), "gi");

        module.exports = color;
    },
    "2": function(require, module, exports, global) {
        /*
requestFrame / cancelFrame
*/
                "use strict";

        var array = require("3");

        var requestFrame = global.requestAnimationFrame || global.webkitRequestAnimationFrame || global.mozRequestAnimationFrame || global.oRequestAnimationFrame || global.msRequestAnimationFrame || function(callback) {
            return setTimeout(callback, 1e3 / 60);
        };

        var callbacks = [];

        var iterator = function(time) {
            var split = callbacks.splice(0, callbacks.length);
            for (var i = 0, l = split.length; i < l; i++) split[i](time || (time = +new Date()));
        };

        var cancel = function(callback) {
            var io = array.indexOf(callbacks, callback);
            if (io > -1) callbacks.splice(io, 1);
        };

        var request = function(callback) {
            var i = callbacks.push(callback);
            if (i === 1) requestFrame(iterator);
            return function() {
                cancel(callback);
            };
        };

        exports.request = request;

        exports.cancel = cancel;
    },
    "3": function(require, module, exports, global) {
        /*
array
 - array es5 shell
*/
                "use strict";

        var array = require("4")["array"];

        var names = ("pop,push,reverse,shift,sort,splice,unshift,concat,join,slice,toString,indexOf,lastIndexOf,forEach,every,some" + ",filter,map,reduce,reduceRight").split(",");

        for (var methods = {}, i = 0, name, method; name = names[i++]; ) if (method = Array.prototype[name]) methods[name] = method;

        if (!methods.filter) methods.filter = function(fn, context) {
            var results = [];
            for (var i = 0, l = this.length >>> 0; i < l; i++) if (i in this) {
                var value = this[i];
                if (fn.call(context, value, i, this)) results.push(value);
            }
            return results;
        };

        if (!methods.indexOf) methods.indexOf = function(item, from) {
            for (var l = this.length >>> 0, i = from < 0 ? Math.max(0, l + from) : from || 0; i < l; i++) {
                if (i in this && this[i] === item) return i;
            }
            return -1;
        };

        if (!methods.map) methods.map = function(fn, context) {
            var length = this.length >>> 0, results = Array(length);
            for (var i = 0, l = length; i < l; i++) {
                if (i in this) results[i] = fn.call(context, this[i], i, this);
            }
            return results;
        };

        if (!methods.every) methods.every = function(fn, context) {
            for (var i = 0, l = this.length >>> 0; i < l; i++) {
                if (i in this && !fn.call(context, this[i], i, this)) return false;
            }
            return true;
        };

        if (!methods.some) methods.some = function(fn, context) {
            for (var i = 0, l = this.length >>> 0; i < l; i++) {
                if (i in this && fn.call(context, this[i], i, this)) return true;
            }
            return false;
        };

        if (!methods.forEach) methods.forEach = function(fn, context) {
            for (var i = 0, l = this.length >>> 0; i < l; i++) {
                if (i in this) fn.call(context, this[i], i, this);
            }
        };

        var toString = Object.prototype.toString;

        array.isArray = Array.isArray || function(self) {
            return toString.call(self) === "[object Array]";
        };

        module.exports = array.implement(methods);
    },
    "4": function(require, module, exports, global) {
        /*
shell
*/
                "use strict";

        var prime = require("5"), type = require("6");

        var slice = Array.prototype.slice;

        var ghost = prime({
            constructor: function ghost(self) {
                this.valueOf = function() {
                    return self;
                };
                this.toString = function() {
                    return self + "";
                };
                this.is = function(object) {
                    return self === object;
                };
            }
        });

        var shell = function(self) {
            if (self == null || self instanceof ghost) return self;
            var g = shell[type(self)];
            return g ? new g(self) : self;
        };

        var register = function() {
            var g = prime({
                inherits: ghost
            });
            return prime({
                constructor: function(self) {
                    return new g(self);
                },
                define: function(key, descriptor) {
                    var method = descriptor.value;
                    this[key] = function(self) {
                        return arguments.length > 1 ? method.apply(self, slice.call(arguments, 1)) : method.call(self);
                    };
                    g.prototype[key] = function() {
                        return shell(method.apply(this.valueOf(), arguments));
                    };
                    prime.define(this.prototype, key, descriptor);
                    return this;
                }
            });
        };

        for (var types = "string,number,array,object,date,function,regexp".split(","), i = types.length; i--; ) shell[types[i]] = register();

        module.exports = shell;
    },
    "5": function(require, module, exports, global) {
        /*
prime
 - prototypal inheritance
*/
                "use strict";

        var has = function(self, key) {
            return Object.hasOwnProperty.call(self, key);
        };

        var each = function(object, method, context) {
            for (var key in object) if (method.call(context, object[key], key, object) === false) break;
            return object;
        };

        if (!{
            valueOf: 0
        }.propertyIsEnumerable("valueOf")) {
            // fix for stupid IE enumeration bug
            var buggy = "constructor,toString,valueOf,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString".split(",");
            var proto = Object.prototype;
            each = function(object, method, context) {
                for (var key in object) if (method.call(context, object[key], key, object) === false) return object;
                for (var i = 0; key = buggy[i]; i++) {
                    var value = object[key];
                    if ((value !== proto[key] || has(object, key)) && method.call(context, value, key, object) === false) break;
                }
                return object;
            };
        }

        var create = Object.create || function(self) {
            var constructor = function() {};
            constructor.prototype = self;
            return new constructor();
        };

        var getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

        var define = Object.defineProperty;

        try {
            var obj = {
                a: 1
            };
            getOwnPropertyDescriptor(obj, "a");
            define(obj, "a", {
                value: 2
            });
        } catch (e) {
            getOwnPropertyDescriptor = function(object, key) {
                return {
                    value: object[key]
                };
            };
            define = function(object, key, descriptor) {
                object[key] = descriptor.value;
                return object;
            };
        }

        var implement = function(proto) {
            each(proto, function(value, key) {
                if (key !== "constructor" && key !== "define" && key !== "inherits") this.define(key, getOwnPropertyDescriptor(proto, key) || {
                    writable: true,
                    enumerable: true,
                    configurable: true,
                    value: value
                });
            }, this);
            return this;
        };

        var prime = function(proto) {
            var superprime = proto.inherits;
            // if our nice proto object has no own constructor property
            // then we proceed using a ghosting constructor that all it does is
            // call the parent's constructor if it has a superprime, else an empty constructor
            // proto.constructor becomes the effective constructor
            var constructor = has(proto, "constructor") ? proto.constructor : superprime ? function() {
                return superprime.apply(this, arguments);
            } : function() {};
            if (superprime) {
                var superproto = superprime.prototype;
                // inherit from superprime
                var cproto = constructor.prototype = create(superproto);
                // setting constructor.parent to superprime.prototype
                // because it's the shortest possible absolute reference
                constructor.parent = superproto;
                cproto.constructor = constructor;
            }
            // inherit (kindof inherit) define
            constructor.define = proto.define || superprime && superprime.define || function(key, descriptor) {
                define(this.prototype, key, descriptor);
                return this;
            };
            // copy implement (this should never change)
            constructor.implement = implement;
            // finally implement proto and return constructor
            return constructor.implement(proto);
        };

        prime.has = has;

        prime.each = each;

        prime.create = create;

        prime.define = define;

        module.exports = prime;
    },
    "6": function(require, module, exports, global) {
        /*
type
*/
                "use strict";

        var toString = Object.prototype.toString, types = /number|object|array|string|function|date|regexp|boolean/;

        var type = function(object) {
            if (object == null) return "null";
            var string = toString.call(object).slice(8, -1).toLowerCase();
            if (string === "number" && isNaN(object)) return "null";
            if (types.test(string)) return string;
            return "object";
        };

        module.exports = type;
    },
    "7": function(require, module, exports, global) {
        /*
MooFx
*/
                "use strict";

        // requires
        var color = require("1"), frame = require("2");

        var cancelFrame = frame.cancel, requestFrame = frame.request;

        var prime = require("5"), array = require("3"), string = require("8");

        var camelize = string.camelize, clean = string.clean, capitalize = string.capitalize;

        var map = array.map, forEach = array.forEach, indexOf = array.indexOf;

        var elements = require("a");

        var fx = require("b");

        // util
        var hyphenated = {};

        var hyphenate = function(self) {
            return hyphenated[self] || (hyphenated[self] = string.hyphenate(self));
        };

        var round = function(n) {
            return Math.round(n * 1e3) / 1e3;
        };

        // compute > node > property
        var compute = global.getComputedStyle ? function(node) {
            var cts = getComputedStyle(node);
            return function(property) {
                return cts ? cts.getPropertyValue(hyphenate(property)) : "";
            };
        } : /*(css3)?*/ function(node) {
            var cts = node.currentStyle;
            return function(property) {
                return cts ? cts[camelize(property)] : "";
            };
        };

        /*:null*/
        // pixel ratio retriever
        var test = document.createElement("div");

        var cssText = "border:none;margin:none;padding:none;visibility:hidden;position:absolute;height:0;";

        // returns the amount of pixels that takes to make one of the unit
        var pixelRatio = function(element, u) {
            var parent = element.parentNode, ratio = 1;
            if (parent) {
                test.style.cssText = cssText + ("width:100" + u + ";");
                parent.appendChild(test);
                ratio = test.offsetWidth / 100;
                parent.removeChild(test);
            }
            return ratio;
        };

        // mirror 4 values
        var mirror4 = function(values) {
            var length = values.length;
            if (length === 1) values.push(values[0], values[0], values[0]); else if (length === 2) values.push(values[0], values[1]); else if (length === 3) values.push(values[1]);
            return values;
        };

        // regular expressions strings
        var sLength = "([-.\\d]+)(%|cm|mm|in|px|pt|pc|em|ex|ch|rem|vw|vh|vm)", sLengthNum = sLength + "?", sBorderStyle = "none|hidden|dotted|dashed|solid|double|groove|ridge|inset|outset|inherit";

        // regular expressions
        var rgLength = RegExp(sLength, "g"), rLengthNum = RegExp(sLengthNum), rgLengthNum = RegExp(sLengthNum, "g"), rBorderStyle = RegExp(sBorderStyle);

        // normalize > css
        var parseString = function(value) {
            return value == null ? "" : value + "";
        };

        var parseOpacity = function(value, normalize) {
            if (value == null || value === "") return normalize ? "1" : "";
            return isFinite(value = +value) ? value < 0 ? "0" : value + "" : "1";
        };

        try {
            test.style.color = "rgba(0,0,0,0.5)";
        } catch (e) {}

        var rgba = /^rgba/.test(test.style.color);

        var parseColor = function(value, normalize) {
            var black = "rgba(0,0,0,1)", c;
            if (!value || !(c = color(value, true))) return normalize ? black : "";
            if (normalize) return "rgba(" + c + ")";
            var alpha = c[3];
            if (alpha === 0) return "transparent";
            return !rgba || alpha === 1 ? "rgb(" + c.slice(0, 3) + ")" : "rgba(" + c + ")";
        };

        var parseLength = function(value, normalize) {
            if (value == null || value === "") return normalize ? "0px" : "";
            var match = string.match(value, rLengthNum);
            return match ? match[1] + (match[2] || "px") : value;
        };

        var parseBorderStyle = function(value, normalize) {
            if (value == null || value === "") return normalize ? "none" : "";
            var match = value.match(rBorderStyle);
            return match ? value : normalize ? "none" : "";
        };

        var parseBorder = function(value, normalize) {
            var normalized = "0px none rgba(0,0,0,1)";
            if (value == null || value === "") return normalize ? normalized : "";
            if (value === 0 || value === "none") return normalize ? normalized : value + "";
            var c;
            value = value.replace(color.x, function(match) {
                c = match;
                return "";
            });
            var s = value.match(rBorderStyle), l = value.match(rgLengthNum);
            return clean([ parseLength(l ? l[0] : "", normalize), parseBorderStyle(s ? s[0] : "", normalize), parseColor(c, normalize) ].join(" "));
        };

        var parseShort4 = function(value, normalize) {
            if (value == null || value === "") return normalize ? "0px 0px 0px 0px" : "";
            return clean(mirror4(map(clean(value).split(" "), function(v) {
                return parseLength(v, normalize);
            })).join(" "));
        };

        var parseShadow = function(value, normalize, len) {
            var transparent = "rgba(0,0,0,0)", normalized = len === 3 ? transparent + " 0px 0px 0px" : transparent + " 0px 0px 0px 0px";
            if (value == null || value === "") return normalize ? normalized : "";
            if (value === "none") return normalize ? normalized : value;
            var colors = [], value = clean(value).replace(color.x, function(match) {
                colors.push(match);
                return "";
            });
            return map(value.split(","), function(shadow, i) {
                var c = parseColor(colors[i], normalize), inset = /inset/.test(shadow), lengths = shadow.match(rgLengthNum) || [ "0px" ];
                lengths = map(lengths, function(m) {
                    return parseLength(m, normalize);
                });
                while (lengths.length < len) lengths.push("0px");
                var ret = inset ? [ "inset", c ] : [ c ];
                return ret.concat(lengths).join(" ");
            }).join(", ");
        };

        var parse = function(value, normalize) {
            if (value == null || value === "") return "";
            // cant normalize "" || null
            return value.replace(color.x, function(match) {
                return parseColor(match, normalize);
            }).replace(rgLength, function(match) {
                return parseLength(match, normalize);
            });
        };

        // get && set
        var getters = {}, setters = {}, parsers = {}, aliases = {};

        var getter = function(key) {
            return getters[key] || (getters[key] = function() {
                var alias = aliases[key] || key, parser = parsers[key] || parse;
                return function() {
                    return parser(compute(this)(alias), true);
                };
            }());
        };

        var setter = function(key) {
            return setters[key] || (setters[key] = function() {
                var alias = aliases[key] || key, parser = parsers[key] || parse;
                return function(value) {
                    this.style[alias] = parser(value, false);
                };
            }());
        };

        // parsers
        var trbl = [ "Top", "Right", "Bottom", "Left" ], tlbl = [ "TopLeft", "TopRight", "BottomRight", "BottomLeft" ];

        forEach(trbl, function(d) {
            var bd = "border" + d;
            forEach([ "margin" + d, "padding" + d, bd + "Width", d.toLowerCase() ], function(n) {
                parsers[n] = parseLength;
            });
            parsers[bd + "Color"] = parseColor;
            parsers[bd + "Style"] = parseBorderStyle;
            // borderDIR
            parsers[bd] = parseBorder;
            getters[bd] = function() {
                return [ getter(bd + "Width").call(this), getter(bd + "Style").call(this), getter(bd + "Color").call(this) ].join(" ");
            };
        });

        forEach(tlbl, function(d) {
            parsers["border" + d + "Radius"] = parseLength;
        });

        parsers.color = parsers.backgroundColor = parseColor;

        parsers.width = parsers.height = parsers.minWidth = parsers.minHeight = parsers.maxWidth = parsers.maxHeight = parsers.fontSize = parsers.backgroundSize = parseLength;

        // margin + padding
        forEach([ "margin", "padding" ], function(name) {
            parsers[name] = parseShort4;
            getters[name] = function() {
                return map(trbl, function(d) {
                    return getter(name + d).call(this);
                }, this).join(" ");
            };
        });

        // borders
        // borderDIRWidth, borderDIRStyle, borderDIRColor
        parsers.borderWidth = parseShort4;

        parsers.borderStyle = function(value, normalize) {
            if (value == null || value === "") return normalize ? mirror4([ "none" ]).join(" ") : "";
            value = clean(value).split(" ");
            return clean(mirror4(map(value, function(v) {
                parseBorderStyle(v, normalize);
            })).join(" "));
        };

        parsers.borderColor = function(value, normalize) {
            if (!value || !(value = string.match(value, color.x))) return normalize ? mirror4([ "rgba(0,0,0,1)" ]).join(" ") : "";
            return clean(mirror4(map(value, function(v) {
                return parseColor(v, normalize);
            })).join(" "));
        };

        forEach([ "Width", "Style", "Color" ], function(name) {
            getters["border" + name] = function() {
                return map(trbl, function(d) {
                    return getter("border" + d + name).call(this);
                }, this).join(" ");
            };
        });

        // borderRadius
        parsers.borderRadius = parseShort4;

        getters.borderRadius = function() {
            return map(tlbl, function(d) {
                return getter("border" + d + "Radius").call(this);
            }, this).join(" ");
        };

        // border
        parsers.border = parseBorder;

        getters.border = function() {
            var pvalue;
            for (var i = 0; i < trbl.length; i++) {
                var value = getter("border" + trbl[i]).call(this);
                if (pvalue && value !== pvalue) return null;
                pvalue = value;
            }
            return pvalue;
        };

        // zIndex
        parsers.zIndex = parseString;

        // opacity
        parsers.opacity = parseOpacity;

        /*(css3)?*/
        var filterName = test.style.MsFilter != null && "MsFilter" || test.style.filter != null && "filter";

        if (filterName && test.style.opacity == null) {
            var matchOp = /alpha\(opacity=([\d.]+)\)/i;
            setters.opacity = function(value) {
                value = (value = parseOpacity(value)) === "1" ? "" : "alpha(opacity=" + Math.round(value * 100) + ")";
                var filter = compute(this)(filterName);
                return this.style[filterName] = matchOp.test(filter) ? filter.replace(matchOp, value) : filter + " " + value;
            };
            getters.opacity = function() {
                var match = compute(this)(filterName).match(matchOp);
                return (!match ? 1 : match[1] / 100) + "";
            };
        }

        /*:*/
        var parseBoxShadow = parsers.boxShadow = function(value, normalize) {
            return parseShadow(value, normalize, 4);
        };

        var parseTextShadow = parsers.textShadow = function(value, normalize) {
            return parseShadow(value, normalize, 3);
        };

        // Aliases
        forEach([ "Webkit", "Moz", "ms", "O", null ], function(prefix) {
            forEach([ "transition", "transform", "transformOrigin", "transformStyle", "perspective", "perspectiveOrigin", "backfaceVisibility" ], function(style) {
                var cc = prefix ? prefix + capitalize(style) : style;
                if (prefix === "ms") hyphenated[cc] = "-ms-" + hyphenate(style);
                if (test.style[cc] != null) aliases[style] = cc;
            });
        });

        var transitionName = aliases.transition, transformName = aliases.transform;

        // manually disable css3 transitions in Opera, because they do not work properly.
        if (transitionName === "OTransition") transitionName = null;

        // this takes care of matrix decomposition on browsers that support only 2d transforms but no CSS3 transitions.
        // basically, IE9 (and Opera as well, since we disabled CSS3 transitions manually)
        var parseTransform2d, Transform2d;

        /*(css3)?*/
        if (!transitionName && transformName) (function() {
            var unmatrix = require("d");
            var v = "\\s*([-\\d\\w.]+)\\s*";
            var rMatrix = RegExp("matrix\\(" + [ v, v, v, v, v, v ] + "\\)");
            var decomposeMatrix = function(matrix) {
                var d = unmatrix.apply(null, matrix.match(rMatrix).slice(1)) || [ [ 0, 0 ], 0, 0, [ 0, 0 ] ];
                return [ "translate(" + map(d[0], function(v) {
                    return round(v) + "px";
                }) + ")", "rotate(" + round(d[1] * 180 / Math.PI) + "deg)", "skewX(" + round(d[2] * 180 / Math.PI) + "deg)", "scale(" + map(d[3], round) + ")" ].join(" ");
            };
            var def0px = function(value) {
                return value || "0px";
            }, def1 = function(value) {
                return value || "1";
            }, def0deg = function(value) {
                return value || "0deg";
            };
            var transforms = {
                translate: function(value) {
                    if (!value) value = "0px,0px";
                    var values = value.split(",");
                    if (!values[1]) values[1] = "0px";
                    return map(values, clean) + "";
                },
                translateX: def0px,
                translateY: def0px,
                scale: function(value) {
                    if (!value) value = "1,1";
                    var values = value.split(",");
                    if (!values[1]) values[1] = values[0];
                    return map(values, clean) + "";
                },
                scaleX: def1,
                scaleY: def1,
                rotate: def0deg,
                skewX: def0deg,
                skewY: def0deg
            };
            Transform2d = prime({
                constructor: function(transform) {
                    var names = this.names = [];
                    var values = this.values = [];
                    transform.replace(/(\w+)\(([-.\d\s\w,]+)\)/g, function(match, name, value) {
                        names.push(name);
                        values.push(value);
                    });
                },
                identity: function() {
                    var functions = [];
                    forEach(this.names, function(name) {
                        var fn = transforms[name];
                        if (fn) functions.push(name + "(" + fn() + ")");
                    });
                    return functions.join(" ");
                },
                sameType: function(transformObject) {
                    return this.names.toString() === transformObject.names.toString();
                },
                // this is, basically, cheating.
                // retrieving the matrix value from the dom, rather than calculating it
                decompose: function() {
                    var transform = this.toString();
                    test.style.cssText = cssText + hyphenate(transformName) + ":" + transform + ";";
                    document.body.appendChild(test);
                    var m = compute(test)(transformName);
                    if (!m || m === "none") m = "matrix(1, 0, 0, 1, 0, 0)";
                    document.body.removeChild(test);
                    return decomposeMatrix(m);
                }
            });
            Transform2d.prototype.toString = function(clean) {
                var values = this.values, functions = [];
                forEach(this.names, function(name, i) {
                    var fn = transforms[name];
                    if (!fn) return;
                    var value = fn(values[i]);
                    if (!clean || value !== fn()) functions.push(name + "(" + value + ")");
                });
                return functions.length ? functions.join(" ") : "none";
            };
            Transform2d.union = function(from, to) {
                if (from === to) return;
                // nothing to do
                var fromMap, toMap;
                if (from === "none") {
                    toMap = new Transform2d(to);
                    to = toMap.toString();
                    from = toMap.identity();
                    fromMap = new Transform2d(from);
                } else if (to === "none") {
                    fromMap = new Transform2d(from);
                    from = fromMap.toString();
                    to = fromMap.identity();
                    toMap = new Transform2d(to);
                } else {
                    fromMap = new Transform2d(from);
                    from = fromMap.toString();
                    toMap = new Transform2d(to);
                    to = toMap.toString();
                }
                if (from === to) return;
                // nothing to do
                if (!fromMap.sameType(toMap)) {
                    from = fromMap.decompose();
                    to = toMap.decompose();
                }
                if (from === to) return;
                // nothing to do
                return [ from, to ];
            };
            // this parser makes sure it never gets "matrix"
            parseTransform2d = parsers.transform = function(transform) {
                if (!transform || transform === "none") return "none";
                return new Transform2d(rMatrix.test(transform) ? decomposeMatrix(transform) : transform).toString(true);
            };
            // this getter makes sure we read from the dom only the first time
            // this way we save the actual transform and not "matrix"
            // setting matrix() will use parseTransform2d as well, thus setting the decomposed matrix
            getters.transform = function() {
                var s = this.style;
                return s[transformName] || (s[transformName] = parseTransform2d(compute(this)(transformName)));
            };
        })();

        /*:*/
        // tries to match from and to values
        var prepare = function(node, property, to) {
            var parser = parsers[property] || parse, from = getter(property).call(node), // "normalized" by the getter
            to = parser(to, true);
            // normalize parsed property
            if (from === to) return;
            if (parser === parseLength || parser === parseBorder || parser === parseShort4) {
                var toAll = to.match(rgLength), i = 0;
                // this should always match something
                if (toAll) from = from.replace(rgLength, function(fromFull, fromValue, fromUnit) {
                    var toFull = toAll[i++], toMatched = toFull.match(rLengthNum), toUnit = toMatched[2];
                    if (fromUnit !== toUnit) {
                        var fromPixels = fromUnit === "px" ? fromValue : pixelRatio(node, fromUnit) * fromValue;
                        return round(fromPixels / pixelRatio(node, toUnit)) + toUnit;
                    }
                    return fromFull;
                });
                if (i > 0) setter(property).call(node, from);
            } else if (parser === parseTransform2d) {
                // IE9/Opera
                return Transform2d.union(from, to);
            }
            /*:*/
            return from !== to ? [ from, to ] : null;
        };

        // BrowserAnimation
        var BrowserAnimation = prime({
            inherits: fx,
            constructor: function BrowserAnimation(node, property) {
                var _getter = getter(property), _setter = setter(property);
                this.get = function() {
                    return _getter.call(node);
                };
                this.set = function(value) {
                    return _setter.call(node, value);
                };
                BrowserAnimation.parent.constructor.call(this, this.set);
                this.node = node;
                this.property = property;
            }
        });

        var JSAnimation;

        /*(css3)?*/
        JSAnimation = prime({
            inherits: BrowserAnimation,
            constructor: function JSAnimation() {
                return JSAnimation.parent.constructor.apply(this, arguments);
            },
            start: function(to) {
                this.stop();
                if (this.duration === 0) {
                    this.cancel(to);
                    return this;
                }
                var fromTo = prepare(this.node, this.property, to);
                if (!fromTo) {
                    this.cancel(to);
                    return this;
                }
                JSAnimation.parent.start.apply(this, fromTo);
                if (!this.cancelStep) return this;
                // the animation would have started but we need additional checks
                var parser = parsers[this.property] || parse;
                // complex interpolations JSAnimation can't handle
                // even CSS3 animation gracefully fail with some of those edge cases
                // other "simple" properties, such as `border` can have different templates
                // because of string properties like "solid" and "dashed"
                if ((parser === parseBoxShadow || parser === parseTextShadow || parser === parse) && this.templateFrom !== this.templateTo) {
                    this.cancelStep();
                    delete this.cancelStep;
                    this.cancel(to);
                }
                return this;
            },
            parseEquation: function(equation) {
                if (typeof equation === "string") return JSAnimation.parent.parseEquation.call(this, equation);
            }
        });

        /*:*/
        // CSSAnimation
        var remove3 = function(value, a, b, c) {
            var index = indexOf(a, value);
            if (index !== -1) {
                a.splice(index, 1);
                b.splice(index, 1);
                c.splice(index, 1);
            }
        };

        var CSSAnimation = prime({
            inherits: BrowserAnimation,
            constructor: function CSSAnimation(node, property) {
                CSSAnimation.parent.constructor.call(this, node, property);
                this.hproperty = hyphenate(aliases[property] || property);
                var self = this;
                this.bSetTransitionCSS = function(time) {
                    self.setTransitionCSS(time);
                };
                this.bSetStyleCSS = function(time) {
                    self.setStyleCSS(time);
                };
                this.bComplete = function() {
                    self.complete();
                };
            },
            start: function(to) {
                this.stop();
                if (this.duration === 0) {
                    this.cancel(to);
                    return this;
                }
                var fromTo = prepare(this.node, this.property, to);
                if (!fromTo) {
                    this.cancel(to);
                    return this;
                }
                this.to = fromTo[1];
                // setting transition styles immediately will make good browsers behave weirdly
                // because DOM changes are always deferred, so we requestFrame
                this.cancelSetTransitionCSS = requestFrame(this.bSetTransitionCSS);
                return this;
            },
            setTransitionCSS: function(time) {
                delete this.cancelSetTransitionCSS;
                this.resetCSS(true);
                // firefox flickers if we set css for transition as well as styles at the same time
                // so, other than deferring transition styles we defer actual styles as well on a requestFrame
                this.cancelSetStyleCSS = requestFrame(this.bSetStyleCSS);
            },
            setStyleCSS: function(time) {
                delete this.cancelSetStyleCSS;
                var duration = this.duration;
                // we use setTimeout instead of transitionEnd because some browsers (looking at you foxy)
                // incorrectly set event.propertyName, so we cannot check which animation we are canceling
                this.cancelComplete = setTimeout(this.bComplete, duration);
                this.endTime = time + duration;
                this.set(this.to);
            },
            complete: function() {
                delete this.cancelComplete;
                this.resetCSS();
                this.callback(this.endTime);
            },
            stop: function(hard) {
                if (this.cancelExit) {
                    this.cancelExit();
                    delete this.cancelExit;
                } else if (this.cancelSetTransitionCSS) {
                    // if cancelSetTransitionCSS is set, means nothing is set yet
                    this.cancelSetTransitionCSS();
                    //so we cancel and we're good
                    delete this.cancelSetTransitionCSS;
                } else if (this.cancelSetStyleCSS) {
                    // if cancelSetStyleCSS is set, means transition css has been set, but no actual styles.
                    this.cancelSetStyleCSS();
                    delete this.cancelSetStyleCSS;
                    // if its a hard stop (and not another start on top of the current animation)
                    // we need to reset the transition CSS
                    if (hard) this.resetCSS();
                } else if (this.cancelComplete) {
                    // if cancelComplete is set, means style and transition css have been set, not yet completed.
                    clearTimeout(this.cancelComplete);
                    delete this.cancelComplete;
                    // if its a hard stop (and not another start on top of the current animation)
                    // we need to reset the transition CSS set the current animation styles
                    if (hard) {
                        this.resetCSS();
                        this.set(this.get());
                    }
                }
                return this;
            },
            resetCSS: function(inclusive) {
                var rules = compute(this.node), properties = (rules(transitionName + "Property").replace(/\s+/g, "") || "all").split(","), durations = (rules(transitionName + "Duration").replace(/\s+/g, "") || "0s").split(","), equations = (rules(transitionName + "TimingFunction").replace(/\s+/g, "") || "ease").match(/cubic-bezier\([\d-.,]+\)|([a-z-]+)/g);
                remove3("all", properties, durations, equations);
                remove3(this.hproperty, properties, durations, equations);
                if (inclusive) {
                    properties.push(this.hproperty);
                    durations.push(this.duration + "ms");
                    equations.push("cubic-bezier(" + this.equation + ")");
                }
                var nodeStyle = this.node.style;
                nodeStyle[transitionName + "Property"] = properties;
                nodeStyle[transitionName + "Duration"] = durations;
                nodeStyle[transitionName + "TimingFunction"] = equations;
            },
            parseEquation: function(equation) {
                if (typeof equation === "string") return CSSAnimation.parent.parseEquation.call(this, equation, true);
            }
        });

        // elements methods
        var BaseAnimation = transitionName ? CSSAnimation : JSAnimation;

        var moofx = function(x, y) {
            return typeof x === "function" ? fx(x) : elements(x, y);
        };

        elements.implement({
            // {properties}, options or
            // property, value options
            animate: function(A, B, C) {
                var styles = A, options = B;
                if (typeof A === "string") {
                    styles = {};
                    styles[A] = B;
                    options = C;
                }
                if (options == null) options = {};
                var type = typeof options;
                options = type === "function" ? {
                    callback: options
                } : type === "string" || type === "number" ? {
                    duration: options
                } : options;
                var callback = options.callback || function() {}, completed = 0, length = 0;
                options.callback = function(t) {
                    if (++completed === length) callback(t);
                };
                for (var property in styles) {
                    var value = styles[property], property = camelize(property);
                    this.forEach(function(node) {
                        length++;
                        var self = elements(node), anims = self._animations || (self._animations = {});
                        var anim = anims[property] || (anims[property] = new BaseAnimation(node, property));
                        anim.setOptions(options).start(value);
                    });
                }
                return this;
            },
            // {properties} or
            // property, value
            style: function(A, B) {
                var styles = A;
                if (typeof A === "string") {
                    styles = {};
                    styles[A] = B;
                }
                for (var property in styles) {
                    var value = styles[property], set = setter(property = camelize(property));
                    this.forEach(function(node) {
                        var self = elements(node), anims = self._animations, anim;
                        if (anims && (anim = anims[property])) anim.stop(true);
                        set.call(node, value);
                    });
                }
                return this;
            },
            compute: function(property) {
                property = camelize(property);
                var node = this[0];
                // return default matrix for transform, instead of parsed (for consistency)
                if (property === "transform" && parseTransform2d) return compute(node)(transformName);
                var value = getter(property).call(node);
                // unit conversion to `px`
                return value != null ? value.replace(rgLength, function(match, value, unit) {
                    return unit === "px" ? match : pixelRatio(node, unit) * value + "px";
                }) : "";
            }
        });

        moofx.parse = function(property, value, normalize) {
            return (parsers[camelize(property)] || parse)(value, normalize);
        };

        module.exports = moofx;
    },
    "8": function(require, module, exports, global) {
        /*
string methods
 - string shell
*/
                "use strict";

        var string = require("9");

        string.implement({
            clean: function() {
                return string.trim((this + "").replace(/\s+/g, " "));
            },
            camelize: function() {
                return (this + "").replace(/-\D/g, function(match) {
                    return match.charAt(1).toUpperCase();
                });
            },
            hyphenate: function() {
                return (this + "").replace(/[A-Z]/g, function(match) {
                    return "-" + match.toLowerCase();
                });
            },
            capitalize: function() {
                return (this + "").replace(/\b[a-z]/g, function(match) {
                    return match.toUpperCase();
                });
            },
            escape: function() {
                return (this + "").replace(/([-.*+?^${}()|[\]\/\\])/g, "\\$1");
            },
            number: function() {
                return parseFloat(this);
            }
        });

        if (typeof JSON !== "undefined") string.implement({
            decode: function() {
                return JSON.parse(this);
            }
        });

        module.exports = string;
    },
    "9": function(require, module, exports, global) {
        /*
string
 - string es5 shell
*/
                "use strict";

        var string = require("4")["string"];

        var names = ("charAt,charCodeAt,concat,contains,endsWith,indexOf,lastIndexOf,localeCompare,match,replace,search,slice,split" + ",startsWith,substr,substring,toLocaleLowerCase,toLocaleUpperCase,toLowerCase,toString,toUpperCase,trim,valueOf").split(",");

        for (var methods = {}, i = 0, name, method; name = names[i++]; ) if (method = String.prototype[name]) methods[name] = method;

        if (!methods.trim) methods.trim = function() {
            return (this + "").replace(/^\s+|\s+$/g, "");
        };

        module.exports = string.implement(methods);
    },
    a: function(require, module, exports, global) {
        /*
elements
*/
                "use strict";

        var prime = require("5"), array = require("3").prototype;

        // uniqueID
        var uniqueIndex = 0;

        var uniqueID = function(n) {
            return n === global ? "global" : n.uniqueNumber || (n.uniqueNumber = "n:" + (uniqueIndex++).toString(36));
        };

        var instances = {};

        // elements prime
        var $ = prime({
            constructor: function $(n, context) {
                if (n == null) return this && this.constructor === $ ? new elements() : null;
                var self = n;
                if (n.constructor !== elements) {
                    self = new elements();
                    var uid;
                    if (typeof n === "string") {
                        if (!self.search) return null;
                        self[self.length++] = context || document;
                        return self.search(n);
                    }
                    if (n.nodeType || n === global) {
                        self[self.length++] = n;
                    } else if (n.length) {
                        // this could be an array, or any object with a length attribute,
                        // including another instance of elements from another interface.
                        var uniques = {};
                        for (var i = 0, l = n.length; i < l; i++) {
                            // perform elements flattening
                            var nodes = $(n[i], context);
                            if (nodes && nodes.length) for (var j = 0, k = nodes.length; j < k; j++) {
                                var node = nodes[j];
                                uid = uniqueID(node);
                                if (!uniques[uid]) {
                                    self[self.length++] = node;
                                    uniques[uid] = true;
                                }
                            }
                        }
                    }
                }
                if (!self.length) return null;
                // when length is 1 always use the same elements instance
                if (self.length === 1) {
                    uid = uniqueID(self[0]);
                    return instances[uid] || (instances[uid] = self);
                }
                return self;
            }
        });

        var elements = prime({
            inherits: $,
            constructor: function elements() {
                this.length = 0;
            },
            unlink: function() {
                return this.map(function(node, i) {
                    delete instances[uniqueID(node)];
                    return node;
                });
            },
            // straight es5 prototypes (or emulated methods)
            forEach: array.forEach,
            map: array.map,
            filter: array.filter,
            every: array.every,
            some: array.some
        });

        module.exports = $;
    },
    b: function(require, module, exports, global) {
        /*
fx
*/
                "use strict";

        var prime = require("5"), requestFrame = require("2").request, bezier = require("c");

        var map = require("3").map;

        var sDuration = "([\\d.]+)(s|ms)?", sCubicBezier = "cubic-bezier\\(([-.\\d]+),([-.\\d]+),([-.\\d]+),([-.\\d]+)\\)";

        var rDuration = RegExp(sDuration), rCubicBezier = RegExp(sCubicBezier), rgCubicBezier = RegExp(sCubicBezier, "g");

        // equations collection
        var equations = {
            "default": "cubic-bezier(0.25, 0.1, 0.25, 1.0)",
            linear: "cubic-bezier(0, 0, 1, 1)",
            "ease-in": "cubic-bezier(0.42, 0, 1.0, 1.0)",
            "ease-out": "cubic-bezier(0, 0, 0.58, 1.0)",
            "ease-in-out": "cubic-bezier(0.42, 0, 0.58, 1.0)"
        };

        equations.ease = equations["default"];

        var compute = function(from, to, delta) {
            return (to - from) * delta + from;
        };

        var divide = function(string) {
            var numbers = [];
            var template = (string + "").replace(/[-.\d]+/g, function(number) {
                numbers.push(+number);
                return "@";
            });
            return [ numbers, template ];
        };

        var Fx = prime({
            constructor: function Fx(render, options) {
                // set options
                this.setOptions(options);
                // renderer
                this.render = render || function() {};
                // bound functions
                var self = this;
                this.bStep = function(t) {
                    return self.step(t);
                };
                this.bExit = function(time) {
                    self.exit(time);
                };
            },
            setOptions: function(options) {
                if (options == null) options = {};
                if (!(this.duration = this.parseDuration(options.duration || "500ms"))) throw new Error("invalid duration");
                if (!(this.equation = this.parseEquation(options.equation || "default"))) throw new Error("invalid equation");
                this.callback = options.callback || function() {};
                return this;
            },
            parseDuration: function(duration) {
                if (duration = (duration + "").match(rDuration)) {
                    var time = +duration[1], unit = duration[2] || "ms";
                    if (unit === "s") return time * 1e3;
                    if (unit === "ms") return time;
                }
            },
            parseEquation: function(equation, array) {
                var type = typeof equation;
                if (type === "function") {
                    // function
                    return equation;
                } else if (type === "string") {
                    // cubic-bezier string
                    equation = equations[equation] || equation;
                    var match = equation.replace(/\s+/g, "").match(rCubicBezier);
                    if (match) {
                        equation = map(match.slice(1), function(v) {
                            return +v;
                        });
                        if (array) return equation;
                        if (equation.toString() === "0,0,1,1") return function(x) {
                            return x;
                        };
                        type = "object";
                    }
                }
                if (type === "object") {
                    // array
                    return bezier(equation[0], equation[1], equation[2], equation[3], 1e3 / 60 / this.duration / 4);
                }
            },
            cancel: function(to) {
                this.to = to;
                this.cancelExit = requestFrame(this.bExit);
            },
            exit: function(time) {
                this.render(this.to);
                delete this.cancelExit;
                this.callback(time);
            },
            start: function(from, to) {
                this.stop();
                if (this.duration === 0) {
                    this.cancel(to);
                    return this;
                }
                this.isArray = false;
                this.isNumber = false;
                var fromType = typeof from, toType = typeof to;
                if (fromType === "object" && toType === "object") {
                    this.isArray = true;
                } else if (fromType === "number" && toType === "number") {
                    this.isNumber = true;
                }
                var from_ = divide(from), to_ = divide(to);
                this.from = from_[0];
                this.to = to_[0];
                this.templateFrom = from_[1];
                this.templateTo = to_[1];
                if (this.from.length !== this.to.length || this.from.toString() === this.to.toString()) {
                    this.cancel(to);
                    return this;
                }
                delete this.time;
                this.length = this.from.length;
                this.cancelStep = requestFrame(this.bStep);
                return this;
            },
            stop: function() {
                if (this.cancelExit) {
                    this.cancelExit();
                    delete this.cancelExit;
                } else if (this.cancelStep) {
                    this.cancelStep();
                    delete this.cancelStep;
                }
                return this;
            },
            step: function(now) {
                this.time || (this.time = now);
                var factor = (now - this.time) / this.duration;
                if (factor > 1) factor = 1;
                var delta = this.equation(factor), from = this.from, to = this.to, tpl = this.templateTo;
                for (var i = 0, l = this.length; i < l; i++) {
                    var f = from[i], t = to[i];
                    tpl = tpl.replace("@", t !== f ? compute(f, t, delta) : t);
                }
                this.render(this.isArray ? tpl.split(",") : this.isNumber ? +tpl : tpl, factor);
                if (factor !== 1) {
                    this.cancelStep = requestFrame(this.bStep);
                } else {
                    delete this.cancelStep;
                    this.callback(now);
                }
            }
        });

        var fx = function(render) {
            var ffx = new Fx(render);
            return {
                start: function(from, to, options) {
                    var type = typeof options;
                    ffx.setOptions(type === "function" ? {
                        callback: options
                    } : type === "string" || type === "number" ? {
                        duration: options
                    } : options).start(from, to);
                    return this;
                },
                stop: function() {
                    ffx.stop();
                    return this;
                }
            };
        };

        fx.prototype = Fx.prototype;

        module.exports = fx;
    },
    c: function(require, module, exports, global) {
                module.exports = function(x1, y1, x2, y2, epsilon) {
            var curveX = function(t) {
                var v = 1 - t;
                return 3 * v * v * t * x1 + 3 * v * t * t * x2 + t * t * t;
            };
            var curveY = function(t) {
                var v = 1 - t;
                return 3 * v * v * t * y1 + 3 * v * t * t * y2 + t * t * t;
            };
            var derivativeCurveX = function(t) {
                var v = 1 - t;
                return 3 * (2 * (t - 1) * t + v * v) * x1 + 3 * (-t * t * t + 2 * v * t) * x2;
            };
            return function(t) {
                var x = t, t0, t1, t2, x2, d2, i;
                // First try a few iterations of Newton's method -- normally very fast.
                for (t2 = x, i = 0; i < 8; i++) {
                    x2 = curveX(t2) - x;
                    if (Math.abs(x2) < epsilon) return curveY(t2);
                    d2 = derivativeCurveX(t2);
                    if (Math.abs(d2) < 1e-6) break;
                    t2 = t2 - x2 / d2;
                }
                t0 = 0, t1 = 1, t2 = x;
                if (t2 < t0) return curveY(t0);
                if (t2 > t1) return curveY(t1);
                // Fallback to the bisection method for reliability.
                while (t0 < t1) {
                    x2 = curveX(t2);
                    if (Math.abs(x2 - x) < epsilon) return curveY(t2);
                    if (x > x2) t0 = t2; else t1 = t2;
                    t2 = (t1 - t0) * .5 + t0;
                }
                // Failure
                return curveY(t2);
            };
        };
    },
    d: function(require, module, exports, global) {
        /*
Unmatrix 2d
 - a crude implementation of the slightly bugged pseudo code in http://www.w3.org/TR/css3-2d-transforms/#matrix-decomposition
*/
                "use strict";

        // returns the length of the passed vector
        var length = function(a) {
            return Math.sqrt(a[0] * a[0] + a[1] * a[1]);
        };

        // normalizes the length of the passed point to 1
        var normalize = function(a) {
            var l = length(a);
            return l ? [ a[0] / l, a[1] / l ] : [ 0, 0 ];
        };

        // returns the dot product of the passed points
        var dot = function(a, b) {
            return a[0] * b[0] + a[1] * b[1];
        };

        // returns the principal value of the arc tangent of
        // y/x, using the signs of both arguments to determine
        // the quadrant of the return value
        var atan2 = Math.atan2;

        var combine = function(a, b, ascl, bscl) {
            return [ ascl * a[0] + bscl * b[0], ascl * a[1] + bscl * b[1] ];
        };

        module.exports = function(a, b, c, d, tx, ty) {
            // Make sure the matrix is invertible
            if (a * d - b * c === 0) return false;
            // Take care of translation
            var translate = [ tx, ty ];
            // Put the components into a 2x2 matrix
            var m = [ [ a, b ], [ c, d ] ];
            // Compute X scale factor and normalize first row.
            var scale = [ length(m[0]) ];
            m[0] = normalize(m[0]);
            // Compute shear factor and make 2nd row orthogonal to 1st.
            var skew = dot(m[0], m[1]);
            m[1] = combine(m[1], m[0], 1, -skew);
            // Now, compute Y scale and normalize 2nd row.
            scale[1] = length(m[1]);
            // m[1] = normalize(m[1]) //
            skew /= scale[1];
            // Now, get the rotation out
            var rotate = atan2(m[0][1], m[0][0]);
            return [ translate, rotate, skew, scale ];
        };
    }
});
((function(){

	if (typeof this.RokBox == 'undefined') this.RokBox = {};

	this.RokBox.Media = new Class({

		Implements: [Options, Events],

		options:{
			data: 'rokbox',

			formats: {
				image: {
					matcher: /(^data:image\/.*,)|(\.(jp(e|g|eg)|gif|png|bmp|webp)((\?|#).*)?$)/i,
					params : {},
					type   : 'image'
				},

				iframe: {
					matcher: '',
					params: {},
					type: 'iframe'
				},

				audio: {
					matcher: /(\.(mp3|wav|ogg)((\?|#).*)?$)/i,
					params : {
						autoplay: 'autoplay',
						controls: 'controls'
					},
					type   : 'audio'
				},

				video: {
					matcher: /(\.(ogm|ogv|webm|mp4|swf)((\?|#).*)?$)/i,
					params : {
						autoplay: 'autoplay',
						controls: 'controls'
					},
					type   : 'video'
				},

				youtube: {
					matcher : /(youtube\.com|youtu\.be|youtube-nocookie\.com)\/(watch\?v=|v\/|u\/|embed\/?)?(videoseries\?list=(.*)|[\w-]{11}|\?listType=(.*)&list=(.*)).*/i,
					params  : {
						autoplay    : 1,
						autohide    : 1,
						fs          : 1,
						rel         : 0,
						hd          : 1,
						vq			: 'hd1080',
						wmode       : 'opaque',
						enablejsapi : 1
					},
					type : 'iframe',
					url  : '//www.youtube.com/embed/$3'
				},
				vimeo : {
					matcher : /(?:vimeo(?:pro)?.com)\/(?:[^\d]+)?(\d+)(?:.*)/,
					params  : {
						autoplay      : 1,
						hd            : 1,
						show_title    : 1,
						show_byline   : 1,
						show_portrait : 0,
						fullscreen    : 1
					},
					type : 'iframe',
					url  : '//player.vimeo.com/video/$1'
				},
				metacafe : {
					matcher : /metacafe.com\/(?:watch|fplayer)\/([\w\-]{1,10})/,
					params  : {
						flashVars: "playerVars=autoPlay=yes"
					},
					type    : 'swf',
					aspect  : {w: 600, h: 338},
					url     : function(rez, params, obj) {
						return '//www.metacafe.com/fplayer/' + rez[1] + '/.swf';
					}
				},
				dailymotion : {
					matcher : /dailymotion.com\/video\/(.*)\/?(.*)/,
					params  : {
						additionalInfos : 0,
						autoStart : 1
					},
					type : 'swf',
					url  : '//www.dailymotion.com/swf/video/$1'
				},
				twitvid : {
					matcher : /(twitvid|telly)\.com\/([a-zA-Z0-9_\-\?\=]+)/i,
					params  : {
						autoplay : 1
					},
					type : 'iframe',
					url  : '//www.$1.com/embed.php?guid=$2'
				},
				spotify : {
					matcher : /open\.spotify\.com\/([a-zA-Z0-9\/]+)/i,
					params  : {},
					type    : 'iframe',
					aspect  : {w: 300, h: 380},
					url     : function(rez, params){
						return '//embed.spotify.com/?uri=' + rez[1].split('/').join(':');
					}
				},
				twitpic : {
					matcher : /twitpic\.com\/(?!(?:place|photos|events)\/)([a-zA-Z0-9\?\=\-]+)/i,
					type : 'image',
					url  : '//twitpic.com/show/full/$1/'
				},
				instagram : {
					matcher : /(instagr\.am|instagram\.com)\/p\/([a-zA-Z0-9_\-]+)\/?/i,
					type : 'image',
					url  : '//$1/p/$2/media/?size=l'
				},
				google_maps : {
					matcher : /maps\.google\.([a-z]{2,3}(\.[a-z]{2})?)\/(\?ll=|maps\?)(.*)/i,
					type    : 'iframe',
					aspect  : {w: 640, h: 480},
					url     : function(rez, params) {
						return '//maps.google.' + rez[1] + '/' + rez[3] + '' + rez[4] + '&output=' + (rez[4].indexOf('layer=c') > 0 ? 'svembed' : 'embed');
					}
				}
			}
		},

		initialize: function(options){
			this.setOptions(options);
		},

		format: function(url){
			var format = this.getFormat(url),
				matcher = url.match(format.matcher ? format.matcher : ''),
				params = format.params || {};

			if (typeof format.url == 'function'){
				return format.url.call(this, matcher, params);
			}

			url = format.url || url;

			matcher.forEach(function(value, key){
				url = url.replace( '$' + key, value || '' );
			}, this);

			params = Object.toQueryString(params);
			if (params.length) {
				url += (url.indexOf('?') > 0 ? '&' : '?') + params;
			}

			return url;
		},

		getFormat: function(url){
			var format;

			Object.forEach(this.options.formats, function(obj, type){
				if (obj.matcher && url.match(obj.matcher)) format = obj;
			});

			return format || 'iframe';
		},

		getType: function(url){
			var match;

			Object.forEach(this.options.formats, function(obj, type){
				if (obj.matcher && url.match(obj.matcher)) match = obj.type;
			});

			return match || 'iframe';
		},

		getAspect: function(url){
			var match;

			Object.forEach(this.options.formats, function(obj, type){
				if (obj.matcher && url.match(obj.matcher)) match = obj.aspect;
			});

			return match || this.options.formats.iframe.aspect || {w: 1280, h: 720};
		},

		getParams: function(url){
			var match;

			Object.forEach(this.options.formats, function(obj, type){
				if (obj.matcher && url.match(obj.matcher)) match = obj.params;
			});

			return match || {};
		}
	});

})());
((function(){

    if (typeof this.rokbox != 'undefined') return;
    if (typeof this.RokBox == 'undefined') this.RokBox = {};

    var RokBox = this.RokBox;

    var isWebkit = navigator.userAgent.match(/Webkit/i),
        isSafari6 = navigator.userAgent.match(/Version\/6.+Safari/i),
        isIE8 = navigator.userAgent.match(/MSIE\s8.0.+Trident/i),
        isiOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );

    var TOUCH_START_COORDS = {x: 0, y: 0};

    String.implement({
        htmlEncode: function(){
            return this.replace(/&[^(#\d+;|a-z+;)]/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        },

        htmlDecode: function(){
            return this.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"');
        }
    });

    this.RokBox.Class = new Class({

        Implements: [Options, Events],

        options:{
            data: 'rokbox'
        },

        initialize: function(options){
            this.setOptions(options, RokBoxSettings || {});
            Browser.Features.Touch = (function(){ return !!('ontouchstart' in window); })();

            this.bound = {
                resize: null
            };

            this._build();
            this.attach();
            this.isFitting = false;
            this.isTouch = Browser.Features.Touch;
            if (this.isTouch) this.wrapper.addClass('touch-device');
            this.media = new RokBox.Media();
        },

        attach: function(){
            if (document.retrieve(this.options.data + ':attached', false)) return;

            var touchMove = 0;
            var open = document.retrieve(this.options.data + ':open', function(event, element){
                    if (event && (event.shift || event.meta || event.rightClick)) return true;
                    if (event) event.preventDefault();

                    this.open(element, event);
                }.bind(this)),

                close = document.retrieve(this.options.data + ':close', function(event, element){
                    if ((!this.isOpen && !this.isOpening) || (event && event.rightClick)) return true;
                    if (event.target && !event.target.get('data-rokboxclose') && (event.target == this.container || this.container.contains(event.target))) return true;
                    if (event.target && !event.target.get('data-rokboxclose') && (
                        (event.target == this.header || this.header.contains(event.target)) ||
                        (event.target == this.footer || this.footer.contains(event.target))
                    )) return true;

                    this.close(element, event);
                    //event.stop();
                }.bind(this)),

                fitscreen = document.retrieve(this.options.data + ':fitscreen', function(event, element){
                    if (event && event.rightClick) return true;
                    this.fitscreen(element, event);
                }.bind(this)),

                unfitscreen = document.retrieve(this.options.data + ':unfitscreen', function(event, element){
                    if (event && event.rightClick) return true;
                    this.unfitscreen(element, event);
                }.bind(this)),

                toggleFitscreen = document.retrieve(this.options.data + ':toggleFitscreen', function(event, element){
                    if (event && event.rightClick) return true;
                    this[(this.isFitting) ? 'unfitscreen' : 'fitscreen'](element);
                }.bind(this)),

                navigation = document.retrieve(this.options.data + ':navigation', function(event, element){
                    var item;
                    if (event && event.rightClick) return true;
                    if (!element && (event.key == 'left' || event.key == 'right')){
                        element = document.getElement('[data-rokbox' + (event.key == 'left' ? 'previous' : 'next') + ']');
                    }
                    if (!element && (event.direction == 'left' || event.direction == 'right')){
                        element = document.getElement('[data-rokbox' + (event.direction == 'left' ? 'previous' : 'next') + ']');
                    }

                    item = document.getElement('[href=' + element.get('data-rokboxnavigation') + '][data-rokbox-album=' + element.get('data-rokboxnavigation-album') + ']');
                    if (item) this.load(item, event);
                }.bind(this));

            document.addEvents({
                'touchstart': this.storeMove,
                'click:relay([data-rokbox])': open,
                'touchend:relay([data-rokbox])': open,
                'keyup:keys(esc)': close,
                'click:relay([data-rokboxwrapper])': close,
                'touchend:relay([data-rokboxwrapper])': close,
                'keydown:keys(f)': toggleFitscreen,
                'click:relay([data-rokboxfitscreen])': fitscreen,
                'click:relay([data-rokboxunfitscreen])': unfitscreen,
                'click:relay([data-rokboxprevious], [data-rokboxnext])': navigation,
                'touchend:relay([data-rokboxfitscreen])': fitscreen,
                'touchend:relay([data-rokboxunfitscreen])': unfitscreen,
                'touchend:relay([data-rokboxprevious], [data-rokboxnext])': navigation,
                'keydown:keys(right)': navigation,
                'keydown:keys(left)': navigation
            });

            document.store(this.options.data + ':attached', true);

        },

        storeMove: function(event) {
            TOUCH_START_COORDS = {
                x: event.changedTouches ? event.changedTouches[0].pageX : event.pageX,
                y: event.changedTouches ? event.changedTouches[0].pageY : event.pageY
            }
        },

        didItMove: function(event) {
            var tolerance = 3,
                didItMove = {
                    x: event.changedTouches ? event.changedTouches[0].pageX : event.pageX,
                    y: event.changedTouches ? event.changedTouches[0].pageY : event.pageY
                };

            return (Math.abs(didItMove.x - TOUCH_START_COORDS.x) > tolerance || Math.abs(didItMove.y - TOUCH_START_COORDS.y) > tolerance);
        },

        open: function(element, event){
            if (this.didItMove(event)) { return this; }
            if (this.isOpening) return this;
            if (this.isOpen) return this.load(element, event);

            this.isOpening = true;

            this._openAndFixJump();

            moofx(this.wrapper).style({display: 'block'}).animate({opacity: 1}, {duration: 300});
            this.containerCaption.set('html', '').addClass('rokbox-hidden');
            this.footer.setStyle('display', 'none');
            moofx(this.container).style({top: '-50%', opacity: 0}).animate({top: 0, opacity: 1}, {
                duration: 300,
                callback: function(){

                    /*['-webkit-', '-moz-', '-ms-', '-o-', ''].each(function(pfx){
                        moofx(document.body.getElements('> *:not([class=rokbox-wrapper])')).style(pfx + 'filter', 'blur(5px)');
                    });*/

                    this.load(element, event);
                }.bind(this)
            });
        },

        close: function(element, event){
            if (this.didItMove(event)) { return this; }
            moofx(this.wrapper).animate({opacity: 0}, {
                duration: 300,
                callback: function(){
                    window.removeEvent('resize', this.bound.resize);

                    /*['-webkit-', '-moz-', '-ms-', '-o-', ''].each(function(pfx){
                        moofx(document.body.getElements('> *:not([class=rokbox-wrapper])')).style(pfx + 'filter', 'none');
                    });*/

                    this.wrapper.setStyle('display', 'none');
                    this.container.setStyles({maxWidth: null, maxHeight: null});
                    if (!this.isTouch) this.containerControls.setStyle('display', 'none');
                    this.containerContent.setStyles({maxWidth: null, maxHeight: null, width: null, height: null}).empty();
                    this.object = null;
                    this.isOpen = false;
                    this.isOpening = false;
                    document.body.setStyle('margin-right', 0).removeClass('rokbox-opened');
                }.bind(this)
            });
        },

        load: function(element, event){
            if (this.didItMove(event)) { return this; }
            if (!element) return;

            var url = element.get('href'),
                href = this.media.format(url),
                type = this.media.getType(url),
                aspect = this.media.getAspect(url),
                object, attr, data = {}, params, size, waitLoad = true, overrideSize;

            window.removeEvent('resize', this.bound.resize);

            this.setNavigation(element);
            this.showSpinner();
            //this.setType(type);
            this.setFitting();

            if (element.get('data-rokbox-element')) type = 'element';
            size = element.get('data-rokbox-size') ? element.get('data-rokbox-size').split(' ') : false;

            data = {
                href: href,
                element: element,
                type: type,
                aspect: aspect,
                size: size
            };

            switch(type){

                case 'image':
                    object = new Image();
                    break;

                case 'element':
                    object = new Element('div');
                    data.rule = element.get('data-rokbox-element');
                    waitLoad = false;
                    break;

                case 'audio':
                    overrideSize = size ? {width: size[0], height: size[1]} : {width: 300, height: 30};
                    if (url.match(/\.mp3$/i) && Browser.firefox){
                        object = new Element('object', {data: url, type: 'application/x-mplayer-2', width: overrideSize.width, height: overrideSize.height + 60}).
                            set('html', '<param name="filename" value="'+url+'"><param name="ShowControls" value="1"><param name="AutoStart" value="true"><embed type="application/x-mplayer-2" src="'+url+'" width="'+overrideSize.width+'" height="'+(overrideSize.height+60)+'" showcontrols="true" autostart="true"></embed>').
                            setStyles({width: overrideSize.width, height: overrideSize.height});

                        data.html5 = false;
                    } else {
                        var mode = url.match(/\.mp3$/i) ? 'video' : 'audio';
                        object = new Element(mode).set('html', 'Your browser does not support the <code><audio></code> element.');
                        params = this.media.getParams(url);
                        object.set('src', url).set('width', overrideSize.width).set('height', overrideSize.height + 60);

                        Object.forEach(params, function(value, param){
                            object.set(param, value);
                        }, this);

                        if (mode == 'video') object.set('type', 'audio/mpeg');
                        data.html5 = true;
                    }

                    waitLoad = false;
                    break;

                case 'video':
                    overrideSize = size ? {width: size[0], height: size[1]} : {width: 600, height: 400};
                    if (url.match(/\.swf$/i) && Browser.firefox){
                        object = new Element('object', {classid: "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000", codebase: "http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0", width: overrideSize.width, height: overrideSize.height}).
                            set('html', '<param name="movie" value="'+url+'"><param name="quality" value="high"><embed src="'+url+'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" width="'+overrideSize.width+'" height="'+overrideSize.height+'" type="application/x-shockwave-flash"></embed>').
                            setStyles({width: overrideSize.width, height: overrideSize.height});

                        data.html5 = false;
                        waitLoad = false;
                    } else {
                        object = new Element('video').set('html', 'Your browser does not support the <code>video</code> element.');

                        params = this.media.getParams(url);
                        if (url.match(/\.mp4$/i) && object.canPlayType('video/mp4').length) object.set('src', href);
                        if (url.match(/\.webm$/i) && object.canPlayType('video/webm').length) object.set('src', href);
                        if (url.match(/\.ogg$/i) && object.canPlayType('video/ogg').length) object.set('src', href);
                        if (url.match(/\.ogv$/i) && object.canPlayType('video/ogv').length) object.set('src', href);
                        if (size) object.setStyles(overrideSize);
                        else object.setStyles({width: '100%', height: 'auto'});

                        Object.forEach(params, function(value, param){
                            object.set(param, value);
                        }, this);

                        data.html5 = true;
                        waitLoad = true;
                    }
                    break;

                case 'swf':
                    var w = Math.min(window.getSize().x - 100, aspect.w),
                        h = w * aspect.h / aspect.w;

                    if (size){
                        w = size[0]; h = size[1];
                    }

                    var embed = new Element('embed', {src: href, type: 'application/x-shockwave-flash', width: w, height: h});
                    params = this.media.getParams(url);
                    object = new Element('object', {
                        classid: 'clsid:D27CDB6E-AE6D-11cf-96B8-444553540000',
                        width: '100%',
                        height: '100%'
                    });

                    object.adopt(new Element('param', {name: 'move', value: href}));

                    Object.forEach(params, function(value, param){
                        new Element('param').set(param, value);
                        embed.set(param, value);
                    }, this);

                    embed.inject(object);

                    this.bound.resize = this._resizeObject.pass([object, data], this);
                    if (!isWebkit) window.addEvent('resize', this.bound.resize);

                    waitLoad = false;
                    break;

                case 'iframe': default:
                    var w = Math.min(window.getSize().x - 100, aspect.w),
                        h = w * aspect.h / aspect.w;

                    if (size){
                        w = size[0]; h = size[1];
                    }

                    object = new IFrame({resize: true, frameborder: 0, webkitAllowFullScreen: true, mozallowfullscreen: true, allowFullScreen: true, width: w, height: h});

                    this.bound.resize = this._resizeObject.pass([object, data], this);
                    if (!isWebkit) window.addEvent('resize', this.bound.resize);

                    if (href.match(/\.pdf$/)){
                        waitLoad = false;
                        object.set('src', href);
                    }

                    break;
            }

            document.id(object).inject(document.body).setStyle('display', 'none');

            if (waitLoad){
                if (data.html5 && data.type == 'video'){
                    object.addEventListener('canplay', function(){
                        data.videoSize = {width: object.videoWidth, height: object.videoHeight};
                        this['_load' + type.capitalize()](object, data);
                    }.bind(this), false);
                    if (isiOS){
                        data.videoSize = {width: object.videoWidth, height: object.videoHeight};
                        this['_load' + type.capitalize()](object, data);
                        object.play();
                    }
                } else {
                    object.addEvent('load', this['_load' + type.capitalize()].pass([object, data], this));
                    object.set('src', href);
                }

                if (Browser.firefox && !!url.match(/\.mp4$/i)){
                    // mp4 is not supported by firefox, yet
                    // so we are going to add a webm and ogv fallback
                    if (!object.canPlayType('video/mp4')){
                        new Element('source')
                                .set('src', url)
                                .set('type', 'video/mp4')
                                .inject(object, 'top');
                        new Element('source')
                                .set('src', url.replace(/\.mp4/, '.ogg'))
                                .set('type', 'video/ogg')
                                .inject(object, 'top');
                        new Element('source')
                                .set('src', url.replace(/\.mp4/, '.ogv'))
                                .set('type', 'video/ogv')
                                .inject(object, 'top');
                        new Element('source')
                                .set('src', url.replace(/\.mp4/, '.webm'))
                                .set('type', 'video/webm')
                                .inject(object, 'top')
                                .onerror = this._setError.pass([object, data], this);
                    }
                }

                object.onerror = this._setError.pass([object, data], this);
            } else {
                this['_load' + type.capitalize()](object, data);
            }
        },

        fitscreen: function(element, event){
            if (this.didItMove(event)) { return this; }
            var dummy = (this.containerContent.getElement('img') || this.containerContent.getElement('iframe'));
            if (!dummy) return;
            var size = {
                    maxWidth: this.containerContent.getStyle('maxWidth'),
                    maxHeight: this.containerContent.getStyle('maxHeight'),
                    width: dummy ? dummy.getStyle('width').toInt() || 'auto' : 'auto',
                    height: dummy ? dummy.getStyle('height').toInt() || 'auto' : 'auto'
                },
                viewport = window.getSize();

            if (isSafari6) moofx(dummy).style({width: 'inherit'});

            this.container.store(this.options.data + ':fitscreen-size', {
                maxWidth: size.maxWidth,
                maxHeight: size.maxHeight,
                width: size.width,
                height: size.height,
                viewport: viewport
            });

            moofx(this.containerContent).style({maxWidth: null, maxHeight: null});
            moofx(dummy).style({width: null, height: null});
            moofx(this.container).style({maxWidth: '100%', maxHeight: '100%'});
            document.getElement('[data-rokboxfitscreen]').setStyle('display', 'none');
            document.getElement('[data-rokboxunfitscreen]').setStyle('display', 'block');

            (function(){
                if (isSafari6 || isIE8) moofx(dummy).style({width: '100%'});
            }.bind(this)).delay(5);

            this.isFitting = true;
        },

        unfitscreen: function(element, event){
            if (this.didItMove(event)) { return this; }
            var size = {
                    maxWidth: this.container.retrieve(this.options.data + ':fitscreen-size').maxWidth,
                    maxHeight: this.container.retrieve(this.options.data + ':fitscreen-size').maxHeight,
                    width: this.container.retrieve(this.options.data + ':fitscreen-size').width,
                    height: this.container.retrieve(this.options.data + ':fitscreen-size').height
                },
                viewport = this.container.retrieve(this.options.data + ':fitscreen-size').viewport;

            if (isSafari6 || isIE8) moofx(this.containerContent.getElement('img')).style({width: 'inherit'});

            this.containerContent.setStyles({maxWidth: size.maxWidth, maxHeight: size.maxHeight});
            moofx(this.container).style({maxWidth: null, maxHeight: null});
            moofx(this.containerContent.getElement('img') || this.containerContent.getElement('iframe')).style({width: size.width, height: size.height});
            document.getElement('[data-rokboxunfitscreen]').setStyle('display', 'none');
            document.getElement('[data-rokboxfitscreen]').setStyle('display', 'block');

            if (isSafari6 || isIE8) moofx(this.containerContent.getElement('img')).style({width: '100%'});

            this.isFitting = false;
        },

        setNavigation: function(element){
            var gallery = element.get('data-rokbox-album'),
                arrows = document.getElements('[data-rokboxprevious], [data-rokboxnext]'),
                prevs = document.getElements('[data-rokboxprevious]'),
                nexts = document.getElements('[data-rokboxnext]');

            if (!gallery){
                arrows.setStyle('display', 'none');
                return this;
            }

            var elements = document.getElements('[data-rokbox-album='+gallery+']'),
                index = elements.indexOf(element),
                previous = elements[index - 1],
                next = elements[index + 1];

            if (index == -1 || elements.length == 1){
                arrows.setStyle('display', 'none');
                this.footer.setStyle('display', 'none');
                return this;
            }

            if (typeof previous == 'undefined') previous = elements[elements.length - 1];
            if (typeof next == 'undefined') next = elements[0];

            prevs.set('data-rokboxnavigation', previous.get('href')).set('data-rokboxnavigation-album', previous.get('data-rokbox-album'));
            nexts.set('data-rokboxnavigation', next.get('href')).set('data-rokboxnavigation-album', next.get('data-rokbox-album'));
            arrows.setStyle('display', 'block');
            if (Browser.Features.Touch) this.footer.setStyle('display', 'block');

            return this;
        },

        setType: function(type){
            this.container.removeClass(this.options.data + '-type-' + this.type);
            this.type = type;
            this.container.addClass(this.options.data + '-type-' + this.type);
        },

        setFitting: function(){
            document.getElements(this.isFitting ? '[data-rokboxunfitscreen]' : '[data-rokboxfitscreen]').setStyle('display', 'none');
        },

        showSpinner: function(){
            this.container.addClass('rokbox-loading');
        },

        hideSpinner: function(){
            this.container.removeClass('rokbox-loading');
        },

        /* Private */
        _build: function(){
            if (this.wrapper || document.getElement('[data-rokboxwrapper]')) return this;

            var list = ['outer', 'row', 'inner', 'container'],
                containerList = ['loader', 'content', 'controls'],
                controlsList = {
                    x: 'close',
                    p: 'previous',
                    n: 'next',
                    /*f: 'fullscreen',*/
                    d: 'fitscreen',
                    w: 'unfitscreen'
                },
                Label;

            this.wrapper = new Element('div[data-rokboxwrapper].' + this.options.data + '-wrapper').inject(document.body);
            list.forEach(function(mode, i){
                this[mode] = new Element('div[data-' + this.options.data + mode + '].' + this.options.data + '-' + mode).inject(this[list[i - 1]] || this.wrapper);
            }, this);

            containerList.forEach(function(mode){
                this['container' + mode.capitalize()] = new Element('div[data-' + this.options.data + mode + '].' + this.options.data + '-' + mode).inject(this.container);
            }, this);


            // touch devices
            ['header', 'footer'].forEach(function(row, r){
                this[row] = new Element('div[data-' + this.options.data + row + '].' + this.options.data + '-' + row).inject(this.row, !r ? 'before' : 'after');
            }, this);
            // end touch devices

            Object.forEach(controlsList, function(label, mode){
                Label = label.capitalize();
                if (mode != 'p' && mode != 'n'){
                    this['controls' + Label] = new Element('div[data-rokboxicon="'+mode+'"][data-' + this.options.data + label + '].' + this.options.data + '-' + label).inject(this.containerControls);
                } else {
                    this['controls' + Label] = new Element('div[data-' + this.options.data + label + '].' + this.options.data + '-' + label).inject(this.containerControls);
                    new Element('div[data-rokboxicon="'+mode+'"]').inject(this['controls' + Label]);
                }

                if (['p', 'n'].contains(mode)) this['controls' + Label].clone().inject(this.footer);
                if (mode == 'x') this['controls' + Label].clone().inject(this.header);

            }, this);

            this.containerLoader.adopt(new Element('div.' + this.options.data + '-loader-image'));
            this.containerCaption = new Element('div[data-' + this.options.data + 'caption].' + this.options.data + '-caption').inject(this.container);
        },

        _loadImage: function(object, data){
            var sizeBefore = this.containerContent.getSize(),
                size, computedHeight, imgOriginalSize = {};

            this.setType(data.type);

            if (isIE8){
                // soon you will die, IE8.
                var ie8 = object.clone().inject(document.body).setStyles({display: 'block', 'visibility': 'visible', position: 'absolute', top: '-30000px'});
                imgOriginalSize = {width: ie8.width, height: ie8.height}
                ie8.dispose();
            }

            // initial cleanup
            moofx(object).style({opacity: 0, visibility: 'hidden', display: 'block'});

            if (isSafari6 || isIE8) moofx(object).style({width: 'inherit'}); // safari 6 sux

            if (data.size){
                moofx(object).style({width: data.size[0], height: data.size[1]});
            }

            object.inject(this.containerContent.empty());
            if (data.type != 'element' && data.type != 'audio' && data.type != 'video') this.containerContent.adopt(new Element('div.rokbox-contentborder'));

            moofx(this.containerContent).style({maxWidth: null, maxHeight: null});
            if (data.size && this.isFitting){
                moofx(object).style({width: null, height: null});
            }

            // caption
            this.containerCaption.set('html', data.element.get('data-rokbox-caption') || '').removeClass('rokbox-hidden');

            // size checks and setup
            size = this.containerContent.getSize();
            if (data.videoSize) size = {x: data.videoSize.width || size.x, y: data.videoSize.height || size.y};
            computedHeight = this.container.getComputedSize({styles: ['padding', 'border', 'margin']}).totalHeight;
            //if (data.videoSize) computedHeight += data.videoSize.height;
            if (!size.x) return;

            // hide object and caption before animating
            moofx(object).style({display: 'none'});
            moofx(this.containerCaption.addClass('rokbox-hidden')).style({opacity: 0});

            if (isSafari6 || isIE8) moofx(object).style({width: '100%'}); // safari 6 sux

            moofx(this.containerContent).style({
                width: !this.isOpen ? this.containerContent.getSize().x : sizeBefore.x,
                height: !this.isOpen ? this.containerContent.getSize().y : sizeBefore.y
            });

            // ensure the height fits on the viewport
            var innerSize = window.getSize(),
                contentSize = this.containerContent.getSize();

            if (computedHeight >= innerSize.y){
                var margin = this.container.getStyle('margin-bottom').toInt(),
                    caption = document.getElement('[data-rokboxcaption]'),
                    captionSize = caption ? caption.getSize().y : 0,

                    objectHeight = Math.round(innerSize.y * size.y / computedHeight) - captionSize - margin,
                    objectWidth = Math.round(size.x * objectHeight / size.y);

                if (!this.isFitting){
                    size.x = objectWidth;
                    size.y = objectHeight;
                }
            }

            // show / hide the fit button
            if (!data.error && data.type != 'element'){
                document.getElements(this.isFitting ? '[data-rokboxunfitscreen]' : '[data-rokboxfitscreen]').setStyle('display', object.width == size.x ? 'none' : 'block');
            } else {
                document.getElements(this.isFitting ? '[data-rokboxunfitscreen]' : '[data-rokboxfitscreen]').setStyle('display', 'none');
            }

            // show caption if needed
            if (!data.error && data.element.get('data-rokbox-caption') && data.element.get('data-rokbox-caption').length){
                moofx(this.containerCaption.removeClass('rokbox-hidden')).animate({opacity: 1});
            }

            //if (data.html5) moofx(object).style({width: '100%', height: '100%'});

            // animate
            moofx(this.containerContent)['animate']({width: size.x, height: size.y}, {
                duration: 250,
                callback: function(){
                    moofx(object).style({
                        display: 'block',
                        visibility: 'visible'
                    });

                    if (data.html5 && object.play) object.play();

                    moofx(object).style({
                        maxWidth: (object.naturalWidth || imgOriginalSize.width || object.width || object.videoWidth || size.x) + 'px',
                        maxHeight: (object.naturalHeight || imgOriginalSize.height || object.height || object.videoHeight || size.y) + 'px'
                    });

                    moofx(object)[!isIE8 ? 'animate' : 'style']({opacity: 1});

                    this.containerContent.setStyles({maxWidth: size.x, maxHeight: size.y, width: null, height: null});
                    if (!this.isTouch) this.containerControls.setStyle('display', 'block');

                    if (this.isFitting) this.fitscreen();

                    this.container.store(this.options.data + ':fitscreen-size', {
                        maxWidth: objectWidth || size.x,
                        maxHeight: objectHeight || size.y
                    });

                }.bind(this)
            });

            this.isOpen = true;
            this.isOpening = false;
            this.object = object;

            this.hideSpinner();
        },

        _loadIframe: function(object, data){
            var sizeBefore = this.containerContent.getSize(),
                size, computedHeight;

            object.removeEvents('load');
            this.setType(data.type);

            // hide and inject in the cleaned out content container
            object.setStyles({visibility: 'hidden', display: 'block'});
            object.inject(this.containerContent.empty());
            if (isIE8) object.setStyle('max-width', 'inherit');

            // max width and height are useless with iframes, unfortunately
            moofx(this.containerContent).style({
                maxWidth: null,
                maxHeight: null
            });

            // caption
            this.containerCaption.set('html', data.element.get('data-rokbox-caption') || '').removeClass('rokbox-hidden');

            // size checks and setup
            size = this.containerContent.getSize();
            if (size.x <= parseInt(object.get('width'), 10)) size.x = parseInt(object.get('width'), 10);
            computedHeight = this.container.getComputedSize({styles: ['padding', 'border', 'margin']}).totalHeight || this.container.getSize().y;

            if (!size.x) return;
            size.y = size.y || 100;

            // hide object and caption before animating
            object.setStyles({display: 'none'});
            moofx(this.containerCaption.addClass('rokbox-hidden')).style({opacity: 0});

            moofx(this.containerContent).style({
                width: !this.isOpen ? this.containerContent.getSize().x : sizeBefore.x,
                height: !this.isOpen ? this.containerContent.getSize().y : sizeBefore.y
            });

            // ensure the height fits on the viewport
            var innerSize = window.getSize(),
                contentSize = this.containerContent.getSize();

            if (computedHeight >= innerSize.y){
                var margin = this.container.getStyle('margin-bottom').toInt(),
                    caption = document.getElement('[data-rokboxcaption]'),
                    captionSize = caption ? caption.getSize().y : 0,

                    objectHeight = Math.round(innerSize.y * size.y / computedHeight) - captionSize - margin,
                    objectWidth = Math.round(size.x * objectHeight / size.y);

                if (!this.isFitting){
                    size.x = objectWidth;
                    size.y = objectHeight;
                }
            }

            document.getElements(this.isFitting ? '[data-rokboxunfitscreen]' : '[data-rokboxfitscreen]').setStyle('display', 'none');

            if (data.element.get('data-rokbox-caption') && data.element.get('data-rokbox-caption').length) moofx(this.containerCaption.removeClass('rokbox-hidden')).animate({opacity: 1});
            moofx(this.containerContent).animate({width: size.x, height: size.y}, {
                duration: 250,
                callback: function(){
                    object.setStyles({
                        display: 'block',
                        visibility: 'visible',
                        width: size.x,
                        height: size.y
                    });
                    this.containerContent.setStyles({maxWidth: size.x, maxHeight: size.y, width: null, height: null});
                    if (!this.isTouch) this.containerControls.setStyle('display', 'block');

                    this.container.store(this.options.data + ':fitscreen-size', {
                        maxWidth: objectWidth || size.x,
                        maxHeight: objectHeight || size.y
                    });

                }.bind(this)
            });

            this.isOpen = true;
            this.isOpening = false;
            this.hideSpinner();
        },

        _loadSwf: function(object, data){
            this._loadIframe(object, data);
        },

        _loadAudio: function(object, data){
            this[data.html5 ? '_loadImage' : '_loadIframe'](object, data);
        },

        _loadVideo: function(object, data){
            this[data.html5 ? '_loadImage' : '_loadIframe'](object, data);
        },

        _loadElement: function(object, data){
            var element = document.getElement(data.rule);
            if (!element){
                return this._setError(object, data);
                //throw Error('Element[@'+data.rule+'] not found. Unable to load RokBox.');
            }

            object.adopt(element.clone(true, true).cloneEvents(element).setStyle('display', 'block').addClass('rokbox-content-element'));
            this._loadImage(object, data);
        },

        _setError: function(object, data){
            var error = new Element('div#rokbox-error.rokbox-error' + data.type);

            if (data.type == 'element') error.set('html', '<h3>Error</h3><p>The '+data.type+' <code>'+data.rule+'</code> was not found in the DOM.</p>');
            else if (data.type == 'video' && data.html5 && Browser.firefox && data.href.match(/\.mp4/i)){
                error.set('html', '<h3>Error</h3><p>An error occurred while trying to load the '+data.type+' link: <br /> <code>'+data.href+'</code> <br />Note that Firefox does not support MP4 files. Try adding a WebM or Ogg converted file at the same location of the video above (<a target="_blank" href="https://developer.mozilla.org/en-US/docs/HTML/Supported_media_formats?redirectlocale=en-US&redirectslug=Media_formats_supported_by_the_audio_and_video_elements#WebM">More details</a>).</p>');
            }
            else error.set('html', '<h3>Error</h3><p>An error occurred while trying to load the '+data.type+' link: <br /> <code>'+data.href+'</code></p>');

            data.error = true;
            this['_load' + (data.type == 'element' ? 'Image' : data.type.capitalize())](error, data);
        },

        _resizeObject: function(object, data){
            var margin = this.container.getStyle('margin-bottom').toInt(),
                caption = document.getElement('[data-rokboxcaption]'),
                captionSize = caption ? caption.getSize().y : 0,
                viewport = window.getSize(),
                containerSize = this.containerContent.getSize(),
                computedHeight = this.container.getComputedSize({styles: ['padding', 'border', 'margin']}).totalHeight || this.container.getSize().y;
                size = {};

            size.y = Math.round(viewport.y * containerSize.y / computedHeight) - captionSize - margin;
            size.x = Math.round(viewport.x * size.y / viewport.y);

            moofx(this.containerContent).style({maxWidth: size.x, maxHeight: size.y});
            moofx(object).style({maxWidth: size.x, maxHeight: size.y});
        },

        _openAndFixJump: function(){
            var previousSize = afterSize = document.body.scrollWidth, compensation = 0;
            document.body.addClass('rokbox-opened');
            afterSize = document.body.scrollWidth;
            compensation = afterSize - previousSize;
            document.body.setStyle('margin-right', compensation);
        }

    });

    window.addEvent('domready', function(){
        this.rokbox = new RokBox.Class();
    });

})());

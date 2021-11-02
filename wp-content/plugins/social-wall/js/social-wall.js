var sbsw_js_exists = (typeof sbsw_js_exists !== 'undefined') ? true : false;
if(!sbsw_js_exists) {
    /*!
         * Isotope PACKAGED v3.0.6
         *
         * Licensed GPLv3 for open source use
         * or Isotope Commercial License for commercial use
         *
         * https://isotope.metafizzy.co
         * Copyright 2010-2018 Metafizzy
         */

    !function (t, e) {
        "function" == typeof define && define.amd ? define("jquery-bridget/jquery-bridget", ["jquery"], function (i) {
            return e(t, i)
        }) : "object" == typeof module && module.exports ? module.exports = e(t, require("jquery")) : t.jQueryBridget = e(t, t.jQuery)
    }(window, function (t, e) {
        "use strict";

        function i(i, s, a) {
            function u(t, e, o) {
                var n, s = "$()." + i + '("' + e + '")';
                return t.each(function (t, u) {
                    var h = a.data(u, i);
                    if (!h) return void r(i + " not initialized. Cannot call methods, i.e. " + s);
                    var d = h[e];
                    if (!d || "_" == e.charAt(0)) return void r(s + " is not a valid method");
                    var l = d.apply(h, o);
                    n = void 0 === n ? l : n
                }), void 0 !== n ? n : t
            }

            function h(t, e) {
                t.each(function (t, o) {
                    var n = a.data(o, i);
                    n ? (n.option(e), n._init()) : (n = new s(o, e), a.data(o, i, n))
                })
            }

            a = a || e || t.jQuery, a && (s.prototype.option || (s.prototype.option = function (t) {
                a.isPlainObject(t) && (this.options = a.extend(!0, this.options, t))
            }), a.fn[i] = function (t) {
                if ("string" == typeof t) {
                    var e = n.call(arguments, 1);
                    return u(this, t, e)
                }
                return h(this, t), this
            }, o(a))
        }

        function o(t) {
            !t || t && t.bridget || (t.bridget = i)
        }

        var n = Array.prototype.slice, s = t.console, r = "undefined" == typeof s ? function () {
        } : function (t) {
            s.error(t)
        };
        return o(e || t.jQuery), i
    }), function (t, e) {
        "function" == typeof define && define.amd ? define("ev-emitter/ev-emitter", e) : "object" == typeof module && module.exports ? module.exports = e() : t.EvEmitter = e()
    }("undefined" != typeof window ? window : this, function () {
        function t() {
        }

        var e = t.prototype;
        return e.on = function (t, e) {
            if (t && e) {
                var i = this._events = this._events || {}, o = i[t] = i[t] || [];
                return o.indexOf(e) == -1 && o.push(e), this
            }
        }, e.once = function (t, e) {
            if (t && e) {
                this.on(t, e);
                var i = this._onceEvents = this._onceEvents || {}, o = i[t] = i[t] || {};
                return o[e] = !0, this
            }
        }, e.off = function (t, e) {
            var i = this._events && this._events[t];
            if (i && i.length) {
                var o = i.indexOf(e);
                return o != -1 && i.splice(o, 1), this
            }
        }, e.emitEvent = function (t, e) {
            var i = this._events && this._events[t];
            if (i && i.length) {
                i = i.slice(0), e = e || [];
                for (var o = this._onceEvents && this._onceEvents[t], n = 0; n < i.length; n++) {
                    var s = i[n], r = o && o[s];
                    r && (this.off(t, s), delete o[s]), s.apply(this, e)
                }
                return this
            }
        }, e.allOff = function () {
            delete this._events, delete this._onceEvents
        }, t
    }), function (t, e) {
        "function" == typeof define && define.amd ? define("get-size/get-size", e) : "object" == typeof module && module.exports ? module.exports = e() : t.getSize = e()
    }(window, function () {
        "use strict";

        function t(t) {
            var e = parseFloat(t), i = t.indexOf("%") == -1 && !isNaN(e);
            return i && e
        }

        function e() {
        }

        function i() {
            for (var t = {
                width: 0,
                height: 0,
                innerWidth: 0,
                innerHeight: 0,
                outerWidth: 0,
                outerHeight: 0
            }, e = 0; e < h; e++) {
                var i = u[e];
                t[i] = 0
            }
            return t
        }

        function o(t) {
            var e = getComputedStyle(t);
            return e || a("Style returned " + e + ". Are you running this code in a hidden iframe on Firefox? See https://bit.ly/getsizebug1"), e
        }

        function n() {
            if (!d) {
                d = !0;
                var e = document.createElement("div");
                e.style.width = "200px", e.style.padding = "1px 2px 3px 4px", e.style.borderStyle = "solid", e.style.borderWidth = "1px 2px 3px 4px", e.style.boxSizing = "border-box";
                var i = document.body || document.documentElement;
                i.appendChild(e);
                var n = o(e);
                r = 200 == Math.round(t(n.width)), s.isBoxSizeOuter = r, i.removeChild(e)
            }
        }

        function s(e) {
            if (n(), "string" == typeof e && (e = document.querySelector(e)), e && "object" == typeof e && e.nodeType) {
                var s = o(e);
                if ("none" == s.display) return i();
                var a = {};
                a.width = e.offsetWidth, a.height = e.offsetHeight;
                for (var d = a.isBorderBox = "border-box" == s.boxSizing, l = 0; l < h; l++) {
                    var f = u[l], c = s[f], m = parseFloat(c);
                    a[f] = isNaN(m) ? 0 : m
                }
                var p = a.paddingLeft + a.paddingRight, y = a.paddingTop + a.paddingBottom,
                    g = a.marginLeft + a.marginRight, v = a.marginTop + a.marginBottom,
                    _ = a.borderLeftWidth + a.borderRightWidth, z = a.borderTopWidth + a.borderBottomWidth, I = d && r,
                    x = t(s.width);
                x !== !1 && (a.width = x + (I ? 0 : p + _));
                var S = t(s.height);
                return S !== !1 && (a.height = S + (I ? 0 : y + z)), a.innerWidth = a.width - (p + _), a.innerHeight = a.height - (y + z), a.outerWidth = a.width + g, a.outerHeight = a.height + v, a
            }
        }

        var r, a = "undefined" == typeof console ? e : function (t) {
                console.error(t)
            },
            u = ["paddingLeft", "paddingRight", "paddingTop", "paddingBottom", "marginLeft", "marginRight", "marginTop", "marginBottom", "borderLeftWidth", "borderRightWidth", "borderTopWidth", "borderBottomWidth"],
            h = u.length, d = !1;
        return s
    }), function (t, e) {
        "use strict";
        "function" == typeof define && define.amd ? define("desandro-matches-selector/matches-selector", e) : "object" == typeof module && module.exports ? module.exports = e() : t.matchesSelector = e()
    }(window, function () {
        "use strict";
        var t = function () {
            var t = window.Element.prototype;
            if (t.matches) return "matches";
            if (t.matchesSelector) return "matchesSelector";
            for (var e = ["webkit", "moz", "ms", "o"], i = 0; i < e.length; i++) {
                var o = e[i], n = o + "MatchesSelector";
                if (t[n]) return n
            }
        }();
        return function (e, i) {
            return e[t](i)
        }
    }), function (t, e) {
        "function" == typeof define && define.amd ? define("fizzy-ui-utils/utils", ["desandro-matches-selector/matches-selector"], function (i) {
            return e(t, i)
        }) : "object" == typeof module && module.exports ? module.exports = e(t, require("desandro-matches-selector")) : t.fizzyUIUtils = e(t, t.matchesSelector)
    }(window, function (t, e) {
        var i = {};
        i.extend = function (t, e) {
            for (var i in e) t[i] = e[i];
            return t
        }, i.modulo = function (t, e) {
            return (t % e + e) % e
        };
        var o = Array.prototype.slice;
        i.makeArray = function (t) {
            if (Array.isArray(t)) return t;
            if (null === t || void 0 === t) return [];
            var e = "object" == typeof t && "number" == typeof t.length;
            return e ? o.call(t) : [t]
        }, i.removeFrom = function (t, e) {
            var i = t.indexOf(e);
            i != -1 && t.splice(i, 1)
        }, i.getParent = function (t, i) {
            for (; t.parentNode && t != document.body;) if (t = t.parentNode, e(t, i)) return t
        }, i.getQueryElement = function (t) {
            return "string" == typeof t ? document.querySelector(t) : t
        }, i.handleEvent = function (t) {
            var e = "on" + t.type;
            this[e] && this[e](t)
        }, i.filterFindElements = function (t, o) {
            t = i.makeArray(t);
            var n = [];
            return t.forEach(function (t) {
                if (t instanceof HTMLElement) {
                    if (!o) return void n.push(t);
                    e(t, o) && n.push(t);
                    for (var i = t.querySelectorAll(o), s = 0; s < i.length; s++) n.push(i[s])
                }
            }), n
        }, i.debounceMethod = function (t, e, i) {
            i = i || 100;
            var o = t.prototype[e], n = e + "Timeout";
            t.prototype[e] = function () {
                var t = this[n];
                clearTimeout(t);
                var e = arguments, s = this;
                this[n] = setTimeout(function () {
                    o.apply(s, e), delete s[n]
                }, i)
            }
        }, i.docReady = function (t) {
            var e = document.readyState;
            "complete" == e || "interactive" == e ? setTimeout(t) : document.addEventListener("DOMContentLoaded", t)
        }, i.toDashed = function (t) {
            return t.replace(/(.)([A-Z])/g, function (t, e, i) {
                return e + "-" + i
            }).toLowerCase()
        };
        var n = t.console;
        return i.htmlInit = function (e, o) {
            i.docReady(function () {
                var s = i.toDashed(o), r = "data-" + s, a = document.querySelectorAll("[" + r + "]"),
                    u = document.querySelectorAll(".js-" + s), h = i.makeArray(a).concat(i.makeArray(u)),
                    d = r + "-options", l = t.jQuery;
                h.forEach(function (t) {
                    var i, s = t.getAttribute(r) || t.getAttribute(d);
                    try {
                        i = s && JSON.parse(s)
                    } catch (a) {
                        return void (n && n.error("Error parsing " + r + " on " + t.className + ": " + a))
                    }
                    var u = new e(t, i);
                    l && l.data(t, o, u)
                })
            })
        }, i
    }), function (t, e) {
        "function" == typeof define && define.amd ? define("outlayer/item", ["ev-emitter/ev-emitter", "get-size/get-size"], e) : "object" == typeof module && module.exports ? module.exports = e(require("ev-emitter"), require("get-size")) : (t.Outlayer = {}, t.Outlayer.Item = e(t.EvEmitter, t.getSize))
    }(window, function (t, e) {
        "use strict";

        function i(t) {
            for (var e in t) return !1;
            return e = null, !0
        }

        function o(t, e) {
            t && (this.element = t, this.layout = e, this.position = {x: 0, y: 0}, this._create())
        }

        function n(t) {
            return t.replace(/([A-Z])/g, function (t) {
                return "-" + t.toLowerCase()
            })
        }

        var s = document.documentElement.style, r = "string" == typeof s.transition ? "transition" : "WebkitTransition",
            a = "string" == typeof s.transform ? "transform" : "WebkitTransform",
            u = {WebkitTransition: "webkitTransitionEnd", transition: "transitionend"}[r], h = {
                transform: a,
                transition: r,
                transitionDuration: r + "Duration",
                transitionProperty: r + "Property",
                transitionDelay: r + "Delay"
            }, d = o.prototype = Object.create(t.prototype);
        d.constructor = o, d._create = function () {
            this._transn = {ingProperties: {}, clean: {}, onEnd: {}}, this.css({position: "absolute"})
        }, d.handleEvent = function (t) {
            var e = "on" + t.type;
            this[e] && this[e](t)
        }, d.getSize = function () {
            this.size = e(this.element)
        }, d.css = function (t) {
            var e = this.element.style;
            for (var i in t) {
                var o = h[i] || i;
                e[o] = t[i]
            }
        }, d.getPosition = function () {
            var t = getComputedStyle(this.element), e = this.layout._getOption("originLeft"),
                i = this.layout._getOption("originTop"), o = t[e ? "left" : "right"], n = t[i ? "top" : "bottom"],
                s = parseFloat(o), r = parseFloat(n), a = this.layout.size;
            o.indexOf("%") != -1 && (s = s / 100 * a.width), n.indexOf("%") != -1 && (r = r / 100 * a.height), s = isNaN(s) ? 0 : s, r = isNaN(r) ? 0 : r, s -= e ? a.paddingLeft : a.paddingRight, r -= i ? a.paddingTop : a.paddingBottom, this.position.x = s, this.position.y = r
        }, d.layoutPosition = function () {
            var t = this.layout.size, e = {}, i = this.layout._getOption("originLeft"),
                o = this.layout._getOption("originTop"), n = i ? "paddingLeft" : "paddingRight",
                s = i ? "left" : "right", r = i ? "right" : "left", a = this.position.x + t[n];
            e[s] = this.getXValue(a), e[r] = "";
            var u = o ? "paddingTop" : "paddingBottom", h = o ? "top" : "bottom", d = o ? "bottom" : "top",
                l = this.position.y + t[u];
            e[h] = this.getYValue(l), e[d] = "", this.css(e), this.emitEvent("layout", [this])
        }, d.getXValue = function (t) {
            var e = this.layout._getOption("horizontal");
            return this.layout.options.percentPosition && !e ? t / this.layout.size.width * 100 + "%" : t + "px"
        }, d.getYValue = function (t) {
            var e = this.layout._getOption("horizontal");
            return this.layout.options.percentPosition && e ? t / this.layout.size.height * 100 + "%" : t + "px"
        }, d._transitionTo = function (t, e) {
            this.getPosition();
            var i = this.position.x, o = this.position.y, n = t == this.position.x && e == this.position.y;
            if (this.setPosition(t, e), n && !this.isTransitioning) return void this.layoutPosition();
            var s = t - i, r = e - o, a = {};
            a.transform = this.getTranslate(s, r), this.transition({
                to: a,
                onTransitionEnd: {transform: this.layoutPosition},
                isCleaning: !0
            })
        }, d.getTranslate = function (t, e) {
            var i = this.layout._getOption("originLeft"), o = this.layout._getOption("originTop");
            return t = i ? t : -t, e = o ? e : -e, "translate3d(" + t + "px, " + e + "px, 0)"
        }, d.goTo = function (t, e) {
            this.setPosition(t, e), this.layoutPosition()
        }, d.moveTo = d._transitionTo, d.setPosition = function (t, e) {
            this.position.x = parseFloat(t), this.position.y = parseFloat(e)
        }, d._nonTransition = function (t) {
            this.css(t.to), t.isCleaning && this._removeStyles(t.to);
            for (var e in t.onTransitionEnd) t.onTransitionEnd[e].call(this)
        }, d.transition = function (t) {
            if (!parseFloat(this.layout.options.transitionDuration)) return void this._nonTransition(t);
            var e = this._transn;
            for (var i in t.onTransitionEnd) e.onEnd[i] = t.onTransitionEnd[i];
            for (i in t.to) e.ingProperties[i] = !0, t.isCleaning && (e.clean[i] = !0);
            if (t.from) {
                this.css(t.from);
                var o = this.element.offsetHeight;
                o = null
            }
            this.enableTransition(t.to), this.css(t.to), this.isTransitioning = !0
        };
        var l = "opacity," + n(a);
        d.enableTransition = function () {
            if (!this.isTransitioning) {
                var t = this.layout.options.transitionDuration;
                t = "number" == typeof t ? t + "ms" : t, this.css({
                    transitionProperty: l,
                    transitionDuration: t,
                    transitionDelay: this.staggerDelay || 0
                }), this.element.addEventListener(u, this, !1)
            }
        }, d.onwebkitTransitionEnd = function (t) {
            this.ontransitionend(t)
        }, d.onotransitionend = function (t) {
            this.ontransitionend(t)
        };
        var f = {"-webkit-transform": "transform"};
        d.ontransitionend = function (t) {
            if (t.target === this.element) {
                var e = this._transn, o = f[t.propertyName] || t.propertyName;
                if (delete e.ingProperties[o], i(e.ingProperties) && this.disableTransition(), o in e.clean && (this.element.style[t.propertyName] = "", delete e.clean[o]), o in e.onEnd) {
                    var n = e.onEnd[o];
                    n.call(this), delete e.onEnd[o]
                }
                this.emitEvent("transitionEnd", [this])
            }
        }, d.disableTransition = function () {
            this.removeTransitionStyles(), this.element.removeEventListener(u, this, !1), this.isTransitioning = !1
        }, d._removeStyles = function (t) {
            var e = {};
            for (var i in t) e[i] = "";
            this.css(e)
        };
        var c = {transitionProperty: "", transitionDuration: "", transitionDelay: ""};
        return d.removeTransitionStyles = function () {
            this.css(c)
        }, d.stagger = function (t) {
            t = isNaN(t) ? 0 : t, this.staggerDelay = t + "ms"
        }, d.removeElem = function () {
            this.element.parentNode.removeChild(this.element), this.css({display: ""}), this.emitEvent("remove", [this])
        }, d.remove = function () {
            return r && parseFloat(this.layout.options.transitionDuration) ? (this.once("transitionEnd", function () {
                this.removeElem()
            }), void this.hide()) : void this.removeElem()
        }, d.reveal = function () {
            delete this.isHidden, this.css({display: ""});
            var t = this.layout.options, e = {}, i = this.getHideRevealTransitionEndProperty("visibleStyle");
            e[i] = this.onRevealTransitionEnd, this.transition({
                from: t.hiddenStyle,
                to: t.visibleStyle,
                isCleaning: !0,
                onTransitionEnd: e
            })
        }, d.onRevealTransitionEnd = function () {
            this.isHidden || this.emitEvent("reveal")
        }, d.getHideRevealTransitionEndProperty = function (t) {
            var e = this.layout.options[t];
            if (e.opacity) return "opacity";
            for (var i in e) return i
        }, d.hide = function () {
            this.isHidden = !0, this.css({display: ""});
            var t = this.layout.options, e = {}, i = this.getHideRevealTransitionEndProperty("hiddenStyle");
            e[i] = this.onHideTransitionEnd, this.transition({
                from: t.visibleStyle,
                to: t.hiddenStyle,
                isCleaning: !0,
                onTransitionEnd: e
            })
        }, d.onHideTransitionEnd = function () {
            this.isHidden && (this.css({display: "none"}), this.emitEvent("hide"))
        }, d.destroy = function () {
            this.css({position: "", left: "", right: "", top: "", bottom: "", transition: "", transform: ""})
        }, o
    }), function (t, e) {
        "use strict";
        "function" == typeof define && define.amd ? define("outlayer/outlayer", ["ev-emitter/ev-emitter", "get-size/get-size", "fizzy-ui-utils/utils", "./item"], function (i, o, n, s) {
            return e(t, i, o, n, s)
        }) : "object" == typeof module && module.exports ? module.exports = e(t, require("ev-emitter"), require("get-size"), require("fizzy-ui-utils"), require("./item")) : t.Outlayer = e(t, t.EvEmitter, t.getSize, t.fizzyUIUtils, t.Outlayer.Item)
    }(window, function (t, e, i, o, n) {
        "use strict";

        function s(t, e) {
            var i = o.getQueryElement(t);
            if (!i) return void (u && u.error("Bad element for " + this.constructor.namespace + ": " + (i || t)));
            this.element = i, h && (this.$element = h(this.element)), this.options = o.extend({}, this.constructor.defaults), this.option(e);
            var n = ++l;
            this.element.outlayerGUID = n, f[n] = this, this._create();
            var s = this._getOption("initLayout");
            s && this.layout()
        }

        function r(t) {
            function e() {
                t.apply(this, arguments)
            }

            return e.prototype = Object.create(t.prototype), e.prototype.constructor = e, e
        }

        function a(t) {
            if ("number" == typeof t) return t;
            var e = t.match(/(^\d*\.?\d*)(\w*)/), i = e && e[1], o = e && e[2];
            if (!i.length) return 0;
            i = parseFloat(i);
            var n = m[o] || 1;
            return i * n
        }

        var u = t.console, h = t.jQuery, d = function () {
        }, l = 0, f = {};
        s.namespace = "outlayer", s.Item = n, s.defaults = {
            containerStyle: {position: "relative"},
            initLayout: !0,
            originLeft: !0,
            originTop: !0,
            resize: !0,
            resizeContainer: !0,
            transitionDuration: "0.4s",
            hiddenStyle: {opacity: 0, transform: "scale(0.001)"},
            visibleStyle: {opacity: 1, transform: "scale(1)"}
        };
        var c = s.prototype;
        o.extend(c, e.prototype), c.option = function (t) {
            o.extend(this.options, t)
        }, c._getOption = function (t) {
            var e = this.constructor.compatOptions[t];
            return e && void 0 !== this.options[e] ? this.options[e] : this.options[t]
        }, s.compatOptions = {
            initLayout: "isInitLayout",
            horizontal: "isHorizontal",
            layoutInstant: "isLayoutInstant",
            originLeft: "isOriginLeft",
            originTop: "isOriginTop",
            resize: "isResizeBound",
            resizeContainer: "isResizingContainer"
        }, c._create = function () {
            this.reloadItems(), this.stamps = [], this.stamp(this.options.stamp), o.extend(this.element.style, this.options.containerStyle);
            var t = this._getOption("resize");
            t && this.bindResize()
        }, c.reloadItems = function () {
            this.items = this._itemize(this.element.children)
        }, c._itemize = function (t) {
            for (var e = this._filterFindItemElements(t), i = this.constructor.Item, o = [], n = 0; n < e.length; n++) {
                var s = e[n], r = new i(s, this);
                o.push(r)
            }
            return o
        }, c._filterFindItemElements = function (t) {
            return o.filterFindElements(t, this.options.itemSelector)
        }, c.getItemElements = function () {
            return this.items.map(function (t) {
                return t.element
            })
        }, c.layout = function () {
            this._resetLayout(), this._manageStamps();
            var t = this._getOption("layoutInstant"), e = void 0 !== t ? t : !this._isLayoutInited;
            this.layoutItems(this.items, e), this._isLayoutInited = !0
        }, c._init = c.layout, c._resetLayout = function () {
            this.getSize()
        }, c.getSize = function () {
            this.size = i(this.element)
        }, c._getMeasurement = function (t, e) {
            var o, n = this.options[t];
            n ? ("string" == typeof n ? o = this.element.querySelector(n) : n instanceof HTMLElement && (o = n), this[t] = o ? i(o)[e] : n) : this[t] = 0
        }, c.layoutItems = function (t, e) {
            t = this._getItemsForLayout(t), this._layoutItems(t, e), this._postLayout()
        }, c._getItemsForLayout = function (t) {
            return t.filter(function (t) {
                return !t.isIgnored
            })
        }, c._layoutItems = function (t, e) {
            if (this._emitCompleteOnItems("layout", t), t && t.length) {
                var i = [];
                t.forEach(function (t) {
                    var o = this._getItemLayoutPosition(t);
                    o.item = t, o.isInstant = e || t.isLayoutInstant, i.push(o)
                }, this), this._processLayoutQueue(i)
            }
        }, c._getItemLayoutPosition = function () {
            return {x: 0, y: 0}
        }, c._processLayoutQueue = function (t) {
            this.updateStagger(), t.forEach(function (t, e) {
                this._positionItem(t.item, t.x, t.y, t.isInstant, e)
            }, this)
        }, c.updateStagger = function () {
            var t = this.options.stagger;
            return null === t || void 0 === t ? void (this.stagger = 0) : (this.stagger = a(t), this.stagger)
        }, c._positionItem = function (t, e, i, o, n) {
            o ? t.goTo(e, i) : (t.stagger(n * this.stagger), t.moveTo(e, i))
        }, c._postLayout = function () {
            this.resizeContainer()
        }, c.resizeContainer = function () {
            var t = this._getOption("resizeContainer");
            if (t) {
                var e = this._getContainerSize();
                e && (this._setContainerMeasure(e.width, !0), this._setContainerMeasure(e.height, !1))
            }
        }, c._getContainerSize = d, c._setContainerMeasure = function (t, e) {
            if (void 0 !== t) {
                var i = this.size;
                i.isBorderBox && (t += e ? i.paddingLeft + i.paddingRight + i.borderLeftWidth + i.borderRightWidth : i.paddingBottom + i.paddingTop + i.borderTopWidth + i.borderBottomWidth), t = Math.max(t, 0), this.element.style[e ? "width" : "height"] = t + "px"
            }
        }, c._emitCompleteOnItems = function (t, e) {
            function i() {
                n.dispatchEvent(t + "Complete", null, [e])
            }

            function o() {
                r++, r == s && i()
            }

            var n = this, s = e.length;
            if (!e || !s) return void i();
            var r = 0;
            e.forEach(function (e) {
                e.once(t, o)
            })
        }, c.dispatchEvent = function (t, e, i) {
            var o = e ? [e].concat(i) : i;
            if (this.emitEvent(t, o), h) if (this.$element = this.$element || h(this.element), e) {
                var n = h.Event(e);
                n.type = t, this.$element.trigger(n, i)
            } else this.$element.trigger(t, i)
        }, c.ignore = function (t) {
            var e = this.getItem(t);
            e && (e.isIgnored = !0)
        }, c.unignore = function (t) {
            var e = this.getItem(t);
            e && delete e.isIgnored
        }, c.stamp = function (t) {
            t = this._find(t), t && (this.stamps = this.stamps.concat(t), t.forEach(this.ignore, this))
        }, c.unstamp = function (t) {
            t = this._find(t), t && t.forEach(function (t) {
                o.removeFrom(this.stamps, t), this.unignore(t)
            }, this)
        }, c._find = function (t) {
            if (t) return "string" == typeof t && (t = this.element.querySelectorAll(t)), t = o.makeArray(t)
        }, c._manageStamps = function () {
            this.stamps && this.stamps.length && (this._getBoundingRect(), this.stamps.forEach(this._manageStamp, this))
        }, c._getBoundingRect = function () {
            var t = this.element.getBoundingClientRect(), e = this.size;
            this._boundingRect = {
                left: t.left + e.paddingLeft + e.borderLeftWidth,
                top: t.top + e.paddingTop + e.borderTopWidth,
                right: t.right - (e.paddingRight + e.borderRightWidth),
                bottom: t.bottom - (e.paddingBottom + e.borderBottomWidth)
            }
        }, c._manageStamp = d, c._getElementOffset = function (t) {
            var e = t.getBoundingClientRect(), o = this._boundingRect, n = i(t), s = {
                left: e.left - o.left - n.marginLeft,
                top: e.top - o.top - n.marginTop,
                right: o.right - e.right - n.marginRight,
                bottom: o.bottom - e.bottom - n.marginBottom
            };
            return s
        }, c.handleEvent = o.handleEvent, c.bindResize = function () {
            t.addEventListener("resize", this), this.isResizeBound = !0
        }, c.unbindResize = function () {
            t.removeEventListener("resize", this), this.isResizeBound = !1
        }, c.onresize = function () {
            this.resize()
        }, o.debounceMethod(s, "onresize", 100), c.resize = function () {
            this.isResizeBound && this.needsResizeLayout() && this.layout()
        }, c.needsResizeLayout = function () {
            var t = i(this.element), e = this.size && t;
            return e && t.innerWidth !== this.size.innerWidth
        }, c.addItems = function (t) {
            var e = this._itemize(t);
            return e.length && (this.items = this.items.concat(e)), e
        }, c.appended = function (t) {
            var e = this.addItems(t);
            e.length && (this.layoutItems(e, !0), this.reveal(e))
        }, c.prepended = function (t) {
            var e = this._itemize(t);
            if (e.length) {
                var i = this.items.slice(0);
                this.items = e.concat(i), this._resetLayout(), this._manageStamps(), this.layoutItems(e, !0), this.reveal(e), this.layoutItems(i)
            }
        }, c.reveal = function (t) {
            if (this._emitCompleteOnItems("reveal", t), t && t.length) {
                var e = this.updateStagger();
                t.forEach(function (t, i) {
                    t.stagger(i * e), t.reveal()
                })
            }
        }, c.hide = function (t) {
            if (this._emitCompleteOnItems("hide", t), t && t.length) {
                var e = this.updateStagger();
                t.forEach(function (t, i) {
                    t.stagger(i * e), t.hide()
                })
            }
        }, c.revealItemElements = function (t) {
            var e = this.getItems(t);
            this.reveal(e)
        }, c.hideItemElements = function (t) {
            var e = this.getItems(t);
            this.hide(e)
        }, c.getItem = function (t) {
            for (var e = 0; e < this.items.length; e++) {
                var i = this.items[e];
                if (i.element == t) return i
            }
        }, c.getItems = function (t) {
            t = o.makeArray(t);
            var e = [];
            return t.forEach(function (t) {
                var i = this.getItem(t);
                i && e.push(i)
            }, this), e
        }, c.remove = function (t) {
            var e = this.getItems(t);
            this._emitCompleteOnItems("remove", e), e && e.length && e.forEach(function (t) {
                t.remove(), o.removeFrom(this.items, t)
            }, this)
        }, c.destroy = function () {
            var t = this.element.style;
            t.height = "", t.position = "", t.width = "", this.items.forEach(function (t) {
                t.destroy()
            }), this.unbindResize();
            var e = this.element.outlayerGUID;
            delete f[e], delete this.element.outlayerGUID, h && h.removeData(this.element, this.constructor.namespace)
        }, s.data = function (t) {
            t = o.getQueryElement(t);
            var e = t && t.outlayerGUID;
            return e && f[e]
        }, s.create = function (t, e) {
            var i = r(s);
            return i.defaults = o.extend({}, s.defaults), o.extend(i.defaults, e), i.compatOptions = o.extend({}, s.compatOptions), i.namespace = t, i.data = s.data, i.Item = r(n), o.htmlInit(i, t), h && h.bridget && h.bridget(t, i), i
        };
        var m = {ms: 1, s: 1e3};
        return s.Item = n, s
    }), function (t, e) {
        "function" == typeof define && define.amd ? define("isotope-layout/js/item", ["outlayer/outlayer"], e) : "object" == typeof module && module.exports ? module.exports = e(require("outlayer")) : (t.Smashotope = t.Smashotope || {}, t.Smashotope.Item = e(t.Outlayer))
    }(window, function (t) {
        "use strict";

        function e() {
            t.Item.apply(this, arguments)
        }

        var i = e.prototype = Object.create(t.Item.prototype), o = i._create;
        i._create = function () {
            this.id = this.layout.itemGUID++, o.call(this), this.sortData = {}
        }, i.updateSortData = function () {
            if (!this.isIgnored) {
                this.sortData.id = this.id, this.sortData["original-order"] = this.id, this.sortData.random = Math.random();
                var t = this.layout.options.getSortData, e = this.layout._sorters;
                for (var i in t) {
                    var o = e[i];
                    this.sortData[i] = o(this.element, this)
                }
            }
        };
        var n = i.destroy;
        return i.destroy = function () {
            n.apply(this, arguments), this.css({display: ""})
        }, e
    }), function (t, e) {
        "function" == typeof define && define.amd ? define("isotope-layout/js/layout-mode", ["get-size/get-size", "outlayer/outlayer"], e) : "object" == typeof module && module.exports ? module.exports = e(require("get-size"), require("outlayer")) : (t.Smashotope = t.Smashotope || {}, t.Smashotope.LayoutMode = e(t.getSize, t.Outlayer))
    }(window, function (t, e) {
        "use strict";

        function i(t) {
            this.smashotope = t, t && (this.options = t.options[this.namespace], this.element = t.element, this.items = t.filteredItems, this.size = t.size)
        }

        var o = i.prototype,
            n = ["_resetLayout", "_getItemLayoutPosition", "_manageStamp", "_getContainerSize", "_getElementOffset", "needsResizeLayout", "_getOption"];
        return n.forEach(function (t) {
            o[t] = function () {
                return e.prototype[t].apply(this.smashotope, arguments)
            }
        }), o.needsVerticalResizeLayout = function () {
            var e = t(this.smashotope.element), i = this.smashotope.size && e;
            return i && e.innerHeight != this.smashotope.size.innerHeight
        }, o._getMeasurement = function () {
            this.smashotope._getMeasurement.apply(this, arguments)
        }, o.getColumnWidth = function () {
            this.getSegmentSize("column", "Width")
        }, o.getRowHeight = function () {
            this.getSegmentSize("row", "Height")
        }, o.getSegmentSize = function (t, e) {
            var i = t + e, o = "outer" + e;
            if (this._getMeasurement(i, o), !this[i]) {
                var n = this.getFirstItemSize();
                this[i] = n && n[o] || this.smashotope.size["inner" + e]
            }
        }, o.getFirstItemSize = function () {
            var e = this.smashotope.filteredItems[0];
            return e && e.element && t(e.element)
        }, o.layout = function () {
            this.smashotope.layout.apply(this.smashotope, arguments)
        }, o.getSize = function () {
            this.smashotope.getSize(), this.size = this.smashotope.size
        }, i.modes = {}, i.create = function (t, e) {
            function n() {
                i.apply(this, arguments)
            }

            return n.prototype = Object.create(o), n.prototype.constructor = n, e && (n.options = e), n.prototype.namespace = t, i.modes[t] = n, n
        }, i
    }), function (t, e) {
        "function" == typeof define && define.amd ? define("masonry-layout/masonry", ["outlayer/outlayer", "get-size/get-size"], e) : "object" == typeof module && module.exports ? module.exports = e(require("outlayer"), require("get-size")) : t.Masonry = e(t.Outlayer, t.getSize)
    }(window, function (t, e) {
        var i = t.create("masonry");
        i.compatOptions.fitWidth = "isFitWidth";
        var o = i.prototype;
        return o._resetLayout = function () {
            this.getSize(), this._getMeasurement("columnWidth", "outerWidth"), this._getMeasurement("gutter", "outerWidth"), this.measureColumns(), this.colYs = [];
            for (var t = 0; t < this.cols; t++) this.colYs.push(0);
            this.maxY = 0, this.horizontalColIndex = 0
        }, o.measureColumns = function () {
            if (this.getContainerWidth(), !this.columnWidth) {
                var t = this.items[0], i = t && t.element;
                this.columnWidth = i && e(i).outerWidth || this.containerWidth
            }
            var o = this.columnWidth += this.gutter, n = this.containerWidth + this.gutter, s = n / o, r = o - n % o,
                a = r && r < 1 ? "round" : "floor";
            s = Math[a](s), this.cols = Math.max(s, 1)
        }, o.getContainerWidth = function () {
            var t = this._getOption("fitWidth"), i = t ? this.element.parentNode : this.element, o = e(i);
            this.containerWidth = o && o.innerWidth
        }, o._getItemLayoutPosition = function (t) {
            t.getSize();
            var e = t.size.outerWidth % this.columnWidth, i = e && e < 1 ? "round" : "ceil",
                o = Math[i](t.size.outerWidth / this.columnWidth);
            o = Math.min(o, this.cols);
            for (var n = this.options.horizontalOrder ? "_getHorizontalColPosition" : "_getTopColPosition", s = this[n](o, t), r = {
                x: this.columnWidth * s.col,
                y: s.y
            }, a = s.y + t.size.outerHeight, u = o + s.col, h = s.col; h < u; h++) this.colYs[h] = a;
            return r
        }, o._getTopColPosition = function (t) {
            var e = this._getTopColGroup(t), i = Math.min.apply(Math, e);
            return {col: e.indexOf(i), y: i}
        }, o._getTopColGroup = function (t) {
            if (t < 2) return this.colYs;
            for (var e = [], i = this.cols + 1 - t, o = 0; o < i; o++) e[o] = this._getColGroupY(o, t);
            return e
        }, o._getColGroupY = function (t, e) {
            if (e < 2) return this.colYs[t];
            var i = this.colYs.slice(t, t + e);
            return Math.max.apply(Math, i)
        }, o._getHorizontalColPosition = function (t, e) {
            var i = this.horizontalColIndex % this.cols, o = t > 1 && i + t > this.cols;
            i = o ? 0 : i;
            var n = e.size.outerWidth && e.size.outerHeight;
            return this.horizontalColIndex = n ? i + t : this.horizontalColIndex, {col: i, y: this._getColGroupY(i, t)}
        }, o._manageStamp = function (t) {
            var i = e(t), o = this._getElementOffset(t), n = this._getOption("originLeft"), s = n ? o.left : o.right,
                r = s + i.outerWidth, a = Math.floor(s / this.columnWidth);
            a = Math.max(0, a);
            var u = Math.floor(r / this.columnWidth);
            u -= r % this.columnWidth ? 0 : 1, u = Math.min(this.cols - 1, u);
            for (var h = this._getOption("originTop"), d = (h ? o.top : o.bottom) + i.outerHeight, l = a; l <= u; l++) this.colYs[l] = Math.max(d, this.colYs[l])
        }, o._getContainerSize = function () {
            this.maxY = Math.max.apply(Math, this.colYs);
            var t = {height: this.maxY};
            return this._getOption("fitWidth") && (t.width = this._getContainerFitWidth()), t
        }, o._getContainerFitWidth = function () {
            for (var t = 0, e = this.cols; --e && 0 === this.colYs[e];) t++;
            return (this.cols - t) * this.columnWidth - this.gutter
        }, o.needsResizeLayout = function () {
            var t = this.containerWidth;
            return this.getContainerWidth(), t != this.containerWidth
        }, i
    }), function (t, e) {
        "function" == typeof define && define.amd ? define("isotope-layout/js/layout-modes/masonry", ["../layout-mode", "masonry-layout/masonry"], e) : "object" == typeof module && module.exports ? module.exports = e(require("../layout-mode"), require("masonry-layout")) : e(t.Smashotope.LayoutMode, t.Masonry)
    }(window, function (t, e) {
        "use strict";
        var i = t.create("masonry"), o = i.prototype, n = {_getElementOffset: !0, layout: !0, _getMeasurement: !0};
        for (var s in e.prototype) n[s] || (o[s] = e.prototype[s]);
        var r = o.measureColumns;
        o.measureColumns = function () {
            this.items = this.smashotope.filteredItems, r.call(this)
        };
        var a = o._getOption;
        return o._getOption = function (t) {
            return "fitWidth" == t ? void 0 !== this.options.isFitWidth ? this.options.isFitWidth : this.options.fitWidth : a.apply(this.smashotope, arguments)
        }, i
    }), function (t, e) {
        "function" == typeof define && define.amd ? define("isotope-layout/js/layout-modes/fit-rows", ["../layout-mode"], e) : "object" == typeof exports ? module.exports = e(require("../layout-mode")) : e(t.Smashotope.LayoutMode)
    }(window, function (t) {
        "use strict";
        var e = t.create("fitRows"), i = e.prototype;
        return i._resetLayout = function () {
            this.x = 0, this.y = 0, this.maxY = 0, this._getMeasurement("gutter", "outerWidth")
        }, i._getItemLayoutPosition = function (t) {
            t.getSize();
            var e = t.size.outerWidth + this.gutter, i = this.smashotope.size.innerWidth + this.gutter;
            0 !== this.x && e + this.x > i && (this.x = 0, this.y = this.maxY);
            var o = {x: this.x, y: this.y};
            return this.maxY = Math.max(this.maxY, this.y + t.size.outerHeight), this.x += e, o
        }, i._getContainerSize = function () {
            return {height: this.maxY}
        }, e
    }), function (t, e) {
        "function" == typeof define && define.amd ? define("isotope-layout/js/layout-modes/vertical", ["../layout-mode"], e) : "object" == typeof module && module.exports ? module.exports = e(require("../layout-mode")) : e(t.Smashotope.LayoutMode)
    }(window, function (t) {
        "use strict";
        var e = t.create("vertical", {horizontalAlignment: 0}), i = e.prototype;
        return i._resetLayout = function () {
            this.y = 0
        }, i._getItemLayoutPosition = function (t) {
            t.getSize();
            var e = (this.smashotope.size.innerWidth - t.size.outerWidth) * this.options.horizontalAlignment,
                i = this.y;
            return this.y += t.size.outerHeight, {x: e, y: i}
        }, i._getContainerSize = function () {
            return {height: this.y}
        }, e
    }), function (t, e) {
        "function" == typeof define && define.amd ? define(["outlayer/outlayer", "get-size/get-size", "desandro-matches-selector/matches-selector", "fizzy-ui-utils/utils", "isotope-layout/js/item", "isotope-layout/js/layout-mode", "isotope-layout/js/layout-modes/masonry", "isotope-layout/js/layout-modes/fit-rows", "isotope-layout/js/layout-modes/vertical"], function (i, o, n, s, r, a) {
            return e(t, i, o, n, s, r, a)
        }) : "object" == typeof module && module.exports ? module.exports = e(t, require("outlayer"), require("get-size"), require("desandro-matches-selector"), require("fizzy-ui-utils"), require("isotope-layout/js/item"), require("isotope-layout/js/layout-mode"), require("isotope-layout/js/layout-modes/masonry"), require("isotope-layout/js/layout-modes/fit-rows"), require("isotope-layout/js/layout-modes/vertical")) : t.Smashotope = e(t, t.Outlayer, t.getSize, t.matchesSelector, t.fizzyUIUtils, t.Smashotope.Item, t.Smashotope.LayoutMode)
    }(window, function (t, e, i, o, n, s, r) {
        function a(t, e) {
            return function (i, o) {
                for (var n = 0; n < t.length; n++) {
                    var s = t[n], r = i.sortData[s], a = o.sortData[s];
                    if (r > a || r < a) {
                        var u = void 0 !== e[s] ? e[s] : e, h = u ? 1 : -1;
                        return (r > a ? 1 : -1) * h
                    }
                }
                return 0
            }
        }

        var u = t.jQuery, h = String.prototype.trim ? function (t) {
            return t.trim()
        } : function (t) {
            return t.replace(/^\s+|\s+$/g, "")
        }, d = e.create("smashotope", {layoutMode: "masonry", isJQueryFiltering: !0, sortAscending: !0});
        d.Item = s, d.LayoutMode = r;
        var l = d.prototype;
        l._create = function () {
            this.itemGUID = 0, this._sorters = {}, this._getSorters(), e.prototype._create.call(this), this.modes = {}, this.filteredItems = this.items, this.sortHistory = ["original-order"];
            for (var t in r.modes) this._initLayoutMode(t)
        }, l.reloadItems = function () {
            this.itemGUID = 0, e.prototype.reloadItems.call(this)
        }, l._itemize = function () {
            for (var t = e.prototype._itemize.apply(this, arguments), i = 0; i < t.length; i++) {
                var o = t[i];
                o.id = this.itemGUID++
            }
            return this._updateItemsSortData(t), t
        }, l._initLayoutMode = function (t) {
            var e = r.modes[t], i = this.options[t] || {};
            this.options[t] = e.options ? n.extend(e.options, i) : i, this.modes[t] = new e(this)
        }, l.layout = function () {
            return !this._isLayoutInited && this._getOption("initLayout") ? void this.arrange() : void this._layout()
        }, l._layout = function () {
            var t = this._getIsInstant();
            this._resetLayout(), this._manageStamps(), this.layoutItems(this.filteredItems, t), this._isLayoutInited = !0
        }, l.arrange = function (t) {
            this.option(t), this._getIsInstant();
            var e = this._filter(this.items);
            this.filteredItems = e.matches, this._bindArrangeComplete(), this._isInstant ? this._noTransition(this._hideReveal, [e]) : this._hideReveal(e), this._sort(), this._layout()
        }, l._init = l.arrange, l._hideReveal = function (t) {
            this.reveal(t.needReveal), this.hide(t.needHide)
        }, l._getIsInstant = function () {
            var t = this._getOption("layoutInstant"), e = void 0 !== t ? t : !this._isLayoutInited;
            return this._isInstant = e, e
        }, l._bindArrangeComplete = function () {
            function t() {
                e && i && o && n.dispatchEvent("arrangeComplete", null, [n.filteredItems])
            }

            var e, i, o, n = this;
            this.once("layoutComplete", function () {
                e = !0, t()
            }), this.once("hideComplete", function () {
                i = !0, t()
            }), this.once("revealComplete", function () {
                o = !0, t()
            })
        }, l._filter = function (t) {
            var e = this.options.filter;
            e = e || "*";
            for (var i = [], o = [], n = [], s = this._getFilterTest(e), r = 0; r < t.length; r++) {
                var a = t[r];
                if (!a.isIgnored) {
                    var u = s(a);
                    u && i.push(a), u && a.isHidden ? o.push(a) : u || a.isHidden || n.push(a)
                }
            }
            return {matches: i, needReveal: o, needHide: n}
        }, l._getFilterTest = function (t) {
            return u && this.options.isJQueryFiltering ? function (e) {
                return u(e.element).is(t);
            } : "function" == typeof t ? function (e) {
                return t(e.element)
            } : function (e) {
                return o(e.element, t)
            }
        }, l.updateSortData = function (t) {
            var e;
            t ? (t = n.makeArray(t), e = this.getItems(t)) : e = this.items, this._getSorters(), this._updateItemsSortData(e)
        }, l._getSorters = function () {
            var t = this.options.getSortData;
            for (var e in t) {
                var i = t[e];
                this._sorters[e] = f(i)
            }
        }, l._updateItemsSortData = function (t) {
            for (var e = t && t.length, i = 0; e && i < e; i++) {
                var o = t[i];
                o.updateSortData()
            }
        };
        var f = function () {
            function t(t) {
                if ("string" != typeof t) return t;
                var i = h(t).split(" "), o = i[0], n = o.match(/^\[(.+)\]$/), s = n && n[1], r = e(s, o),
                    a = d.sortDataParsers[i[1]];
                return t = a ? function (t) {
                    return t && a(r(t))
                } : function (t) {
                    return t && r(t)
                }
            }

            function e(t, e) {
                return t ? function (e) {
                    return e.getAttribute(t)
                } : function (t) {
                    var i = t.querySelector(e);
                    return i && i.textContent
                }
            }

            return t
        }();
        d.sortDataParsers = {
            parseInt: function (t) {
                return parseInt(t, 10)
            }, parseFloat: function (t) {
                return parseFloat(t)
            }
        }, l._sort = function () {
            if (this.options.sortBy) {
                var t = n.makeArray(this.options.sortBy);
                this._getIsSameSortBy(t) || (this.sortHistory = t.concat(this.sortHistory));
                var e = a(this.sortHistory, this.options.sortAscending);
                this.filteredItems.sort(e)
            }
        }, l._getIsSameSortBy = function (t) {
            for (var e = 0; e < t.length; e++) if (t[e] != this.sortHistory[e]) return !1;
            return !0
        }, l._mode = function () {
            var t = this.options.layoutMode, e = this.modes[t];
            if (!e) throw new Error("No layout mode: " + t);
            return e.options = this.options[t], e
        }, l._resetLayout = function () {
            e.prototype._resetLayout.call(this), this._mode()._resetLayout()
        }, l._getItemLayoutPosition = function (t) {
            return this._mode()._getItemLayoutPosition(t)
        }, l._manageStamp = function (t) {
            this._mode()._manageStamp(t)
        }, l._getContainerSize = function () {
            return this._mode()._getContainerSize()
        }, l.needsResizeLayout = function () {
            return this._mode().needsResizeLayout()
        }, l.appended = function (t) {
            var e = this.addItems(t);
            if (e.length) {
                var i = this._filterRevealAdded(e);
                this.filteredItems = this.filteredItems.concat(i)
            }
        }, l.prepended = function (t) {
            var e = this._itemize(t);
            if (e.length) {
                this._resetLayout(), this._manageStamps();
                var i = this._filterRevealAdded(e);
                this.layoutItems(this.filteredItems), this.filteredItems = i.concat(this.filteredItems), this.items = e.concat(this.items)
            }
        }, l._filterRevealAdded = function (t) {
            var e = this._filter(t);
            return this.hide(e.needHide), this.reveal(e.matches), this.layoutItems(e.matches, !0), e.matches
        }, l.insert = function (t) {
            var e = this.addItems(t);
            if (e.length) {
                var i, o, n = e.length;
                for (i = 0; i < n; i++) o = e[i], this.element.appendChild(o.element);
                var s = this._filter(e).matches;
                for (i = 0; i < n; i++) e[i].isLayoutInstant = !0;
                for (this.arrange(), i = 0; i < n; i++) delete e[i].isLayoutInstant;
                this.reveal(s)
            }
        };
        var c = l.remove;
        return l.remove = function (t) {
            t = n.makeArray(t);
            var e = this.getItems(t);
            c.call(this, t);
            for (var i = e && e.length, o = 0; i && o < i; o++) {
                var s = e[o];
                n.removeFrom(this.filteredItems, s)
            }
        }, l.shuffle = function () {
            for (var t = 0; t < this.items.length; t++) {
                var e = this.items[t];
                e.sortData.random = Math.random()
            }
            this.options.sortBy = "random", this._sort(), this._layout()
        }, l._noTransition = function (t, e) {
            var i = this.options.transitionDuration;
            this.options.transitionDuration = 0;
            var o = t.apply(this, e);
            return this.options.transitionDuration = i, o
        }, l.getFilteredItemElements = function () {
            return this.filteredItems.map(function (t) {
                return t.element
            })
        }, d
    });

    //Set Twitter Card text color to match post text
    jQuery('.sbsw-twitter-card').css('color', jQuery('.sbsw-content-text').css('color') );

    // Carousel
    !function (a, b, c, d) {
        function e(b, c) {
            this.settings = null, this.options = a.extend({}, e.Defaults, c), this.$element = a(b), this._handlers = {}, this._plugins = {}, this._supress = {}, this._current = null, this._speed = null, this._coordinates = [], this._breakpoint = null, this._width = null, this._items = [], this._clones = [], this._mergers = [], this._widths = [], this._invalidated = {}, this._pipe = [], this._drag = {
                time: null,
                target: null,
                pointer: null,
                stage: {start: null, current: null},
                direction: null
            }, this._states = {
                current: {},
                tags: {initializing: ["busy"], animating: ["busy"], dragging: ["interacting"]}
            }, a.each(["onResize", "onThrottledResize"], a.proxy(function (b, c) {
                this._handlers[c] = a.proxy(this[c], this)
            }, this)), a.each(e.Plugins, a.proxy(function (a, b) {
                this._plugins[a.charAt(0).toLowerCase() + a.slice(1)] = new b(this)
            }, this)), a.each(e.Workers, a.proxy(function (b, c) {
                this._pipe.push({filter: c.filter, run: a.proxy(c.run, this)})
            }, this)), this.setup(), this.initialize()
        }

        e.Defaults = {
            items: 3,
            loop: !1,
            center: !1,
            rewind: !1,
            mouseDrag: !0,
            touchDrag: !0,
            pullDrag: !0,
            freeDrag: !1,
            margin: 0,
            stagePadding: 0,
            merge: !1,
            mergeFit: !0,
            autoWidth: !1,
            startPosition: 0,
            rtl: !1,
            smartSpeed: 250,
            fluidSpeed: !1,
            dragEndSpeed: !1,
            responsive: {},
            responsiveRefreshRate: 200,
            responsiveBaseElement: b,
            fallbackEasing: "swing",
            info: !1,
            nestedItemSelector: !1,
            itemElement: "div",
            stageElement: "div",
            refreshClass: "sbi-owl-refresh",
            loadedClass: "sbi-owl-loaded",
            loadingClass: "sbi-owl-loading",
            rtlClass: "sbi-owl-rtl",
            responsiveClass: "sbi-owl-responsive",
            dragClass: "sbi-owl-drag",
            itemClass: "sbi-owl-item",
            stageClass: "sbi-owl-stage",
            stageOuterClass: "sbi-owl-stage-outer",
            grabClass: "sbi-owl-grab"
        }, e.Width = {Default: "default", Inner: "inner", Outer: "outer"}, e.Type = {
            Event: "event",
            State: "state"
        }, e.Plugins = {}, e.Workers = [{
            filter: ["width", "settings"], run: function () {
                this._width = this.$element.width()
            }
        }, {
            filter: ["width", "items", "settings"], run: function (a) {
                a.current = this._items && this._items[this.relative(this._current)]
            }
        }, {
            filter: ["items", "settings"], run: function () {
                this.$stage.children(".cloned").remove()
            }
        }, {
            filter: ["width", "items", "settings"], run: function (a) {
                var b = this.settings.margin || "", c = !this.settings.autoWidth, d = this.settings.rtl,
                    e = {width: "auto", "margin-left": d ? b : "", "margin-right": d ? "" : b};
                !c && this.$stage.children().css(e), a.css = e
            }
        }, {
            filter: ["width", "items", "settings"], run: function (a) {
                var b = (this.width() / this.settings.items).toFixed(3) - this.settings.margin, c = null,
                    d = this._items.length, e = !this.settings.autoWidth, f = [];
                for (a.items = {
                    merge: !1,
                    width: b
                }; d--;) c = this._mergers[d], c = this.settings.mergeFit && Math.min(c, this.settings.items) || c, a.items.merge = c > 1 || a.items.merge, f[d] = e ? b * c : this._items[d].width();
                this._widths = f
            }
        }, {
            filter: ["items", "settings"], run: function () {
                var b = [], c = this._items, d = this.settings, e = Math.max(2 * d.items, 4),
                    f = 2 * Math.ceil(c.length / 2), g = d.loop && c.length ? d.rewind ? e : Math.max(e, f) : 0, h = "",
                    i = "";
                for (g /= 2; g--;) b.push(this.normalize(b.length / 2, !0)), h += c[b[b.length - 1]][0].outerHTML, b.push(this.normalize(c.length - 1 - (b.length - 1) / 2, !0)), i = c[b[b.length - 1]][0].outerHTML + i;
                this._clones = b, a(h).addClass("cloned").appendTo(this.$stage), a(i).addClass("cloned").prependTo(this.$stage)
            }
        }, {
            filter: ["width", "items", "settings"], run: function () {
                for (var a = this.settings.rtl ? 1 : -1, b = this._clones.length + this._items.length, c = -1, d = 0, e = 0, f = []; ++c < b;) d = f[c - 1] || 0, e = this._widths[this.relative(c)] + this.settings.margin, f.push(d + e * a);
                this._coordinates = f
            }
        }, {
            filter: ["width", "items", "settings"], run: function () {
                var a = this.settings.stagePadding, b = this._coordinates, c = {
                    width: Math.ceil(Math.abs(b[b.length - 1])) + 2 * a,
                    "padding-left": a || "",
                    "padding-right": a || ""
                };
                this.$stage.css(c)
            }
        }, {
            filter: ["width", "items", "settings"], run: function (a) {
                var b = this._coordinates.length, c = !this.settings.autoWidth, d = this.$stage.children();
                if (c && a.items.merge) for (; b--;) a.css.width = this._widths[this.relative(b)], d.eq(b).css(a.css); else c && (a.css.width = a.items.width, d.css(a.css))
            }
        }, {
            filter: ["items"], run: function () {
                this._coordinates.length < 1 && this.$stage.removeAttr("style")
            }
        }, {
            filter: ["width", "items", "settings"], run: function (a) {
                a.current = a.current ? this.$stage.children().index(a.current) : 0, a.current = Math.max(this.minimum(), Math.min(this.maximum(), a.current)), this.reset(a.current)
            }
        }, {
            filter: ["position"], run: function () {
                this.animate(this.coordinates(this._current))
            }
        }, {
            filter: ["width", "position", "items", "settings"], run: function () {
                var a, b, c, d, e = this.settings.rtl ? 1 : -1, f = 2 * this.settings.stagePadding,
                    g = this.coordinates(this.current()) + f, h = g + this.width() * e, i = [];
                for (c = 0, d = this._coordinates.length; c < d; c++) a = this._coordinates[c - 1] || 0, b = Math.abs(this._coordinates[c]) + f * e, (this.op(a, "<=", g) && this.op(a, ">", h) || this.op(b, "<", g) && this.op(b, ">", h)) && i.push(c);
                this.$stage.children(".active").removeClass("active"), this.$stage.children(":eq(" + i.join("), :eq(") + ")").addClass("active"), this.settings.center && (this.$stage.children(".center").removeClass("center"), this.$stage.children().eq(this.current()).addClass("center"))
            }
        }], e.prototype.initialize = function () {
            if (this.enter("initializing"), this.trigger("initialize"), this.$element.toggleClass(this.settings.rtlClass, this.settings.rtl), this.settings.autoWidth && !this.is("pre-loading")) {
                var b, c, e;
                b = this.$element.find("img"), c = this.settings.nestedItemSelector ? "." + this.settings.nestedItemSelector : d, e = this.$element.children(c).width(), b.length && e <= 0 && this.preloadAutoWidthImages(b)
            }
            this.$element.addClass(this.options.loadingClass), this.$stage = a("<" + this.settings.stageElement + ' class="' + this.settings.stageClass + '"/>').wrap('<div class="' + this.settings.stageOuterClass + '"/>'), this.$element.append(this.$stage.parent()), this.replace(this.$element.children().not(this.$stage.parent())), this.$element.is(":visible") ? this.refresh() : this.invalidate("width"), this.$element.removeClass(this.options.loadingClass).addClass(this.options.loadedClass), this.registerEventHandlers(), this.leave("initializing"), this.trigger("initialized")
        }, e.prototype.setup = function () {
            var b = this.viewport(), c = this.options.responsive, d = -1, e = null;
            c ? (a.each(c, function (a) {
                a <= b && a > d && (d = Number(a))
            }), e = a.extend({}, this.options, c[d]), "function" == typeof e.stagePadding && (e.stagePadding = e.stagePadding()), delete e.responsive, e.responsiveClass && this.$element.attr("class", this.$element.attr("class").replace(new RegExp("(" + this.options.responsiveClass + "-)\\S+\\s", "g"), "$1" + d))) : e = a.extend({}, this.options), this.trigger("change", {
                property: {
                    name: "settings",
                    value: e
                }
            }), this._breakpoint = d, this.settings = e, this.invalidate("settings"), this.trigger("changed", {
                property: {
                    name: "settings",
                    value: this.settings
                }
            })
        }, e.prototype.optionsLogic = function () {
            this.settings.autoWidth && (this.settings.stagePadding = !1, this.settings.merge = !1)
        }, e.prototype.prepare = function (b) {
            var c = this.trigger("prepare", {content: b});
            return c.data || (c.data = a("<" + this.settings.itemElement + "/>").addClass(this.options.itemClass).append(b)), this.trigger("prepared", {content: c.data}), c.data
        }, e.prototype.update = function () {
            for (var b = 0, c = this._pipe.length, d = a.proxy(function (a) {
                return this[a]
            }, this._invalidated), e = {}; b < c;) (this._invalidated.all || a.grep(this._pipe[b].filter, d).length > 0) && this._pipe[b].run(e), b++;
            this._invalidated = {}, !this.is("valid") && this.enter("valid")
        }, e.prototype.width = function (a) {
            switch (a = a || e.Width.Default) {
                case e.Width.Inner:
                case e.Width.Outer:
                    return this._width;
                default:
                    return this._width - 2 * this.settings.stagePadding + this.settings.margin
            }
        }, e.prototype.refresh = function () {
            this.enter("refreshing"), this.trigger("refresh"), this.setup(), this.optionsLogic(), this.$element.addClass(this.options.refreshClass), this.update(), this.$element.removeClass(this.options.refreshClass), this.leave("refreshing"), this.trigger("refreshed")
        }, e.prototype.onThrottledResize = function () {
            b.clearTimeout(this.resizeTimer), this.resizeTimer = b.setTimeout(this._handlers.onResize, this.settings.responsiveRefreshRate)
        }, e.prototype.onResize = function () {
            return !!this._items.length && (this._width !== this.$element.width() && (!!this.$element.is(":visible") && (this.enter("resizing"), this.trigger("resize").isDefaultPrevented() ? (this.leave("resizing"), !1) : (this.invalidate("width"), this.refresh(), this.leave("resizing"), void this.trigger("resized")))))
        }, e.prototype.registerEventHandlers = function () {
            a.support.transition && this.$stage.on(a.support.transition.end + ".owl.core", a.proxy(this.onTransitionEnd, this)), this.settings.responsive !== !1 && this.on(b, "resize", this._handlers.onThrottledResize), this.settings.mouseDrag && (this.$element.addClass(this.options.dragClass), this.$stage.on("mousedown.owl.core", a.proxy(this.onDragStart, this)), this.$stage.on("dragstart.owl.core selectstart.owl.core", function () {
                return !1
            })), this.settings.touchDrag && (this.$stage.on("touchstart.owl.core", a.proxy(this.onDragStart, this)), this.$stage.on("touchcancel.owl.core", a.proxy(this.onDragEnd, this)))
        }, e.prototype.onDragStart = function (b) {
            var d = null;
            3 !== b.which && (a.support.transform ? (d = this.$stage.css("transform").replace(/.*\(|\)| /g, "").split(","), d = {
                x: d[16 === d.length ? 12 : 4],
                y: d[16 === d.length ? 13 : 5]
            }) : (d = this.$stage.position(), d = {
                x: this.settings.rtl ? d.left + this.$stage.width() - this.width() + this.settings.margin : d.left,
                y: d.top
            }), this.is("animating") && (a.support.transform ? this.animate(d.x) : this.$stage.stop(), this.invalidate("position")), this.$element.toggleClass(this.options.grabClass, "mousedown" === b.type), this.speed(0), this._drag.time = (new Date).getTime(), this._drag.target = a(b.target), this._drag.stage.start = d, this._drag.stage.current = d, this._drag.pointer = this.pointer(b), a(c).on("mouseup.owl.core touchend.owl.core", a.proxy(this.onDragEnd, this)), a(c).one("mousemove.owl.core touchmove.owl.core", a.proxy(function (b) {
                var d = this.difference(this._drag.pointer, this.pointer(b));
                a(c).on("mousemove.owl.core touchmove.owl.core", a.proxy(this.onDragMove, this)), Math.abs(d.x) < Math.abs(d.y) && this.is("valid") || (b.preventDefault(), this.enter("dragging"), this.trigger("drag"))
            }, this)))
        }, e.prototype.onDragMove = function (a) {
            var b = null, c = null, d = null, e = this.difference(this._drag.pointer, this.pointer(a)),
                f = this.difference(this._drag.stage.start, e);
            this.is("dragging") && (a.preventDefault(), this.settings.loop ? (b = this.coordinates(this.minimum()), c = this.coordinates(this.maximum() + 1) - b, f.x = ((f.x - b) % c + c) % c + b) : (b = this.settings.rtl ? this.coordinates(this.maximum()) : this.coordinates(this.minimum()), c = this.settings.rtl ? this.coordinates(this.minimum()) : this.coordinates(this.maximum()), d = this.settings.pullDrag ? -1 * e.x / 5 : 0, f.x = Math.max(Math.min(f.x, b + d), c + d)), this._drag.stage.current = f, this.animate(f.x))
        }, e.prototype.onDragEnd = function (b) {
            var d = this.difference(this._drag.pointer, this.pointer(b)), e = this._drag.stage.current,
                f = d.x > 0 ^ this.settings.rtl ? "left" : "right";
            a(c).off(".owl.core"), this.$element.removeClass(this.options.grabClass), (0 !== d.x && this.is("dragging") || !this.is("valid")) && (this.speed(this.settings.dragEndSpeed || this.settings.smartSpeed), this.current(this.closest(e.x, 0 !== d.x ? f : this._drag.direction)), this.invalidate("position"), this.update(), this._drag.direction = f, (Math.abs(d.x) > 3 || (new Date).getTime() - this._drag.time > 300) && this._drag.target.one("click.owl.core", function () {
                return !1
            })), this.is("dragging") && (this.leave("dragging"), this.trigger("dragged"))
        }, e.prototype.closest = function (b, c) {
            var d = -1, e = 30, f = this.width(), g = this.coordinates();
            return this.settings.freeDrag || a.each(g, a.proxy(function (a, h) {
                return "left" === c && b > h - e && b < h + e ? d = a : "right" === c && b > h - f - e && b < h - f + e ? d = a + 1 : this.op(b, "<", h) && this.op(b, ">", g[a + 1] || h - f) && (d = "left" === c ? a + 1 : a), d === -1
            }, this)), this.settings.loop || (this.op(b, ">", g[this.minimum()]) ? d = b = this.minimum() : this.op(b, "<", g[this.maximum()]) && (d = b = this.maximum())), d
        }, e.prototype.animate = function (b) {
            var c = this.speed() > 0;
            this.is("animating") && this.onTransitionEnd(), c && (this.enter("animating"), this.trigger("translate")), a.support.transform3d && a.support.transition ? this.$stage.css({
                transform: "translate3d(" + b + "px,0px,0px)",
                transition: this.speed() / 1e3 + "s"
            }) : c ? this.$stage.animate({left: b + "px"}, this.speed(), this.settings.fallbackEasing, a.proxy(this.onTransitionEnd, this)) : this.$stage.css({left: b + "px"})
        }, e.prototype.is = function (a) {
            return this._states.current[a] && this._states.current[a] > 0
        }, e.prototype.current = function (a) {
            if (a === d) return this._current;
            if (0 === this._items.length) return d;
            if (a = this.normalize(a), this._current !== a) {
                var b = this.trigger("change", {property: {name: "position", value: a}});
                b.data !== d && (a = this.normalize(b.data)), this._current = a, this.invalidate("position"), this.trigger("changed", {
                    property: {
                        name: "position",
                        value: this._current
                    }
                })
            }
            return this._current
        }, e.prototype.invalidate = function (b) {
            return "string" === a.type(b) && (this._invalidated[b] = !0, this.is("valid") && this.leave("valid")), a.map(this._invalidated, function (a, b) {
                return b
            })
        }, e.prototype.reset = function (a) {
            a = this.normalize(a), a !== d && (this._speed = 0, this._current = a, this.suppress(["translate", "translated"]), this.animate(this.coordinates(a)), this.release(["translate", "translated"]))
        }, e.prototype.normalize = function (a, b) {
            var c = this._items.length, e = b ? 0 : this._clones.length;
            return !this.isNumeric(a) || c < 1 ? a = d : (a < 0 || a >= c + e) && (a = ((a - e / 2) % c + c) % c + e / 2), a
        }, e.prototype.relative = function (a) {
            return a -= this._clones.length / 2, this.normalize(a, !0)
        }, e.prototype.maximum = function (a) {
            var b, c, d, e = this.settings, f = this._coordinates.length;
            if (e.loop) f = this._clones.length / 2 + this._items.length - 1; else if (e.autoWidth || e.merge) {
                for (b = this._items.length, c = this._items[--b].width(), d = this.$element.width(); b-- && (c += this._items[b].width() + this.settings.margin, !(c > d));) ;
                f = b + 1
            } else f = e.center ? this._items.length - 1 : this._items.length - e.items;
            return a && (f -= this._clones.length / 2), Math.max(f, 0)
        }, e.prototype.minimum = function (a) {
            return a ? 0 : this._clones.length / 2
        }, e.prototype.items = function (a) {
            return a === d ? this._items.slice() : (a = this.normalize(a, !0), this._items[a])
        }, e.prototype.mergers = function (a) {
            return a === d ? this._mergers.slice() : (a = this.normalize(a, !0), this._mergers[a])
        }, e.prototype.clones = function (b) {
            var c = this._clones.length / 2, e = c + this._items.length, f = function (a) {
                return a % 2 === 0 ? e + a / 2 : c - (a + 1) / 2
            };
            return b === d ? a.map(this._clones, function (a, b) {
                return f(b)
            }) : a.map(this._clones, function (a, c) {
                return a === b ? f(c) : null
            })
        }, e.prototype.speed = function (a) {
            return a !== d && (this._speed = a), this._speed
        }, e.prototype.coordinates = function (b) {
            var c, e = 1, f = b - 1;
            return b === d ? a.map(this._coordinates, a.proxy(function (a, b) {
                return this.coordinates(b)
            }, this)) : (this.settings.center ? (this.settings.rtl && (e = -1, f = b + 1), c = this._coordinates[b], c += (this.width() - c + (this._coordinates[f] || 0)) / 2 * e) : c = this._coordinates[f] || 0, c = Math.ceil(c))
        }, e.prototype.duration = function (a, b, c) {
            return 0 === c ? 0 : Math.min(Math.max(Math.abs(b - a), 1), 6) * Math.abs(c || this.settings.smartSpeed)
        }, e.prototype.to = function (a, b) {
            var c = this.current(), d = null, e = a - this.relative(c), f = (e > 0) - (e < 0), g = this._items.length,
                h = this.minimum(), i = this.maximum();
            this.settings.loop ? (!this.settings.rewind && Math.abs(e) > g / 2 && (e += f * -1 * g), a = c + e, d = ((a - h) % g + g) % g + h, d !== a && d - e <= i && d - e > 0 && (c = d - e, a = d, this.reset(c))) : this.settings.rewind ? (i += 1, a = (a % i + i) % i) : a = Math.max(h, Math.min(i, a)), this.speed(this.duration(c, a, b)), this.current(a), this.$element.is(":visible") && this.update()
        }, e.prototype.next = function (a) {
            a = a || !1, this.to(this.relative(this.current()) + 1, a)
        }, e.prototype.prev = function (a) {
            a = a || !1, this.to(this.relative(this.current()) - 1, a)
        }, e.prototype.onTransitionEnd = function (a) {
            if (a !== d && (a.stopPropagation(), (a.target || a.srcElement || a.originalTarget) !== this.$stage.get(0))) return !1;
            this.leave("animating"), this.trigger("translated")
        }, e.prototype.viewport = function () {
            var d;
            return this.options.responsiveBaseElement !== b ? d = a(this.options.responsiveBaseElement).width() : b.innerWidth ? d = b.innerWidth : c.documentElement && c.documentElement.clientWidth ? d = c.documentElement.clientWidth : console.warn("Can not detect viewport width."), d
        }, e.prototype.replace = function (b) {
            this.$stage.empty(), this._items = [], b && (b = b instanceof jQuery ? b : a(b)), this.settings.nestedItemSelector && (b = b.find("." + this.settings.nestedItemSelector)), b.filter(function () {
                return 1 === this.nodeType
            }).each(a.proxy(function (a, b) {
                b = this.prepare(b), this.$stage.append(b), this._items.push(b), this._mergers.push(1 * b.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1)
            }, this)), this.reset(this.isNumeric(this.settings.startPosition) ? this.settings.startPosition : 0), this.invalidate("items")
        }, e.prototype.add = function (b, c) {
            var e = this.relative(this._current);
            c = c === d ? this._items.length : this.normalize(c, !0), b = b instanceof jQuery ? b : a(b), this.trigger("add", {
                content: b,
                position: c
            }), b = this.prepare(b), 0 === this._items.length || c === this._items.length ? (0 === this._items.length && this.$stage.append(b), 0 !== this._items.length && this._items[c - 1].after(b), this._items.push(b), this._mergers.push(1 * b.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1)) : (this._items[c].before(b), this._items.splice(c, 0, b), this._mergers.splice(c, 0, 1 * b.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1)), this._items[e] && this.reset(this._items[e].index()), this.invalidate("items"), this.trigger("added", {
                content: b,
                position: c
            })
        }, e.prototype.remove = function (a) {
            a = this.normalize(a, !0), a !== d && (this.trigger("remove", {
                content: this._items[a],
                position: a
            }), this._items[a].remove(), this._items.splice(a, 1), this._mergers.splice(a, 1), this.invalidate("items"), this.trigger("removed", {
                content: null,
                position: a
            }))
        }, e.prototype.preloadAutoWidthImages = function (b) {
            b.each(a.proxy(function (b, c) {
                this.enter("pre-loading"), c = a(c), a(new Image).one("load", a.proxy(function (a) {
                    c.attr("src", a.target.src), c.css("opacity", 1), this.leave("pre-loading"), !this.is("pre-loading") && !this.is("initializing") && this.refresh()
                }, this)).attr("src", c.attr("src") || c.attr("data-src") || c.attr("data-src-retina"))
            }, this))
        }, e.prototype.destroy = function () {
            this.$element.off(".owl.core"), this.$stage.off(".owl.core"), a(c).off(".owl.core"), this.settings.responsive !== !1 && (b.clearTimeout(this.resizeTimer), this.off(b, "resize", this._handlers.onThrottledResize));
            for (var d in this._plugins) this._plugins[d].destroy();
            this.$stage.children(".cloned").remove(), this.$stage.unwrap(), this.$stage.children().contents().unwrap(), this.$stage.children().unwrap(), this.$element.removeClass(this.options.refreshClass).removeClass(this.options.loadingClass).removeClass(this.options.loadedClass).removeClass(this.options.rtlClass).removeClass(this.options.dragClass).removeClass(this.options.grabClass).attr("class", this.$element.attr("class").replace(new RegExp(this.options.responsiveClass + "-\\S+\\s", "g"), "")).removeData("owl.carousel")
        }, e.prototype.op = function (a, b, c) {
            var d = this.settings.rtl;
            switch (b) {
                case"<":
                    return d ? a > c : a < c;
                case">":
                    return d ? a < c : a > c;
                case">=":
                    return d ? a <= c : a >= c;
                case"<=":
                    return d ? a >= c : a <= c
            }
        }, e.prototype.on = function (a, b, c, d) {
            a.addEventListener ? a.addEventListener(b, c, d) : a.attachEvent && a.attachEvent("on" + b, c)
        }, e.prototype.off = function (a, b, c, d) {
            a.removeEventListener ? a.removeEventListener(b, c, d) : a.detachEvent && a.detachEvent("on" + b, c)
        }, e.prototype.trigger = function (b, c, d, f, g) {
            var h = {item: {count: this._items.length, index: this.current()}},
                i = a.camelCase(a.grep(["on", b, d], function (a) {
                    return a
                }).join("-").toLowerCase()),
                j = a.Event([b, "owl", d || "carousel"].join(".").toLowerCase(), a.extend({relatedTarget: this}, h, c));
            return this._supress[b] || (a.each(this._plugins, function (a, b) {
                b.onTrigger && b.onTrigger(j)
            }), this.register({
                type: e.Type.Event,
                name: b
            }), this.$element.trigger(j), this.settings && "function" == typeof this.settings[i] && this.settings[i].call(this, j)), j
        }, e.prototype.enter = function (b) {
            a.each([b].concat(this._states.tags[b] || []), a.proxy(function (a, b) {
                this._states.current[b] === d && (this._states.current[b] = 0), this._states.current[b]++
            }, this))
        }, e.prototype.leave = function (b) {
            a.each([b].concat(this._states.tags[b] || []), a.proxy(function (a, b) {
                this._states.current[b]--
            }, this))
        }, e.prototype.register = function (b) {
            if (b.type === e.Type.Event) {
                if (a.event.special[b.name] || (a.event.special[b.name] = {}), !a.event.special[b.name].owl) {
                    var c = a.event.special[b.name]._default;
                    a.event.special[b.name]._default = function (a) {
                        return !c || !c.apply || a.namespace && a.namespace.indexOf("owl") !== -1 ? a.namespace && a.namespace.indexOf("owl") > -1 : c.apply(this, arguments)
                    }, a.event.special[b.name].owl = !0
                }
            } else b.type === e.Type.State && (this._states.tags[b.name] ? this._states.tags[b.name] = this._states.tags[b.name].concat(b.tags) : this._states.tags[b.name] = b.tags, this._states.tags[b.name] = a.grep(this._states.tags[b.name], a.proxy(function (c, d) {
                return a.inArray(c, this._states.tags[b.name]) === d
            }, this)))
        }, e.prototype.suppress = function (b) {
            a.each(b, a.proxy(function (a, b) {
                this._supress[b] = !0
            }, this))
        }, e.prototype.release = function (b) {
            a.each(b, a.proxy(function (a, b) {
                delete this._supress[b]
            }, this))
        }, e.prototype.pointer = function (a) {
            var c = {x: null, y: null};
            return a = a.originalEvent || a || b.event, a = a.touches && a.touches.length ? a.touches[0] : a.changedTouches && a.changedTouches.length ? a.changedTouches[0] : a, a.pageX ? (c.x = a.pageX, c.y = a.pageY) : (c.x = a.clientX, c.y = a.clientY), c
        }, e.prototype.isNumeric = function (a) {
            return !isNaN(parseFloat(a))
        }, e.prototype.difference = function (a, b) {
            return {x: a.x - b.x, y: a.y - b.y}
        }, a.fn.sbiOwlCarousel = function (b) {
            var c = Array.prototype.slice.call(arguments, 1);
            return this.each(function () {
                var d = a(this), f = d.data("owl.carousel");
                f || (f = new e(this, "object" == typeof b && b), d.data("owl.carousel", f), a.each(["next", "prev", "to", "destroy", "refresh", "replace", "add", "remove"], function (b, c) {
                    f.register({
                        type: e.Type.Event,
                        name: c
                    }), f.$element.on(c + ".owl.carousel.core", a.proxy(function (a) {
                        a.namespace && a.relatedTarget !== this && (this.suppress([c]), f[c].apply(this, [].slice.call(arguments, 1)), this.release([c]))
                    }, f))
                })), "string" == typeof b && "_" !== b.charAt(0) && f[b].apply(f, c)
            })
        }, a.fn.sbiOwlCarousel.Constructor = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this._core = b, this._interval = null, this._visible = null, this._handlers = {
                "initialized.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.autoRefresh && this.watch()
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers)
        };
        e.Defaults = {autoRefresh: !0, autoRefreshInterval: 500}, e.prototype.watch = function () {
            this._interval || (this._visible = this._core.$element.is(":visible"), this._interval = b.setInterval(a.proxy(this.refresh, this), this._core.settings.autoRefreshInterval))
        }, e.prototype.refresh = function () {
            this._core.$element.is(":visible") !== this._visible && (this._visible = !this._visible, this._core.$element.toggleClass("sbi-owl-hidden", !this._visible), this._visible && this._core.invalidate("width") && this._core.refresh())
        }, e.prototype.destroy = function () {
            var a, c;
            b.clearInterval(this._interval);
            for (a in this._handlers) this._core.$element.off(a, this._handlers[a]);
            for (c in Object.getOwnPropertyNames(this)) "function" != typeof this[c] && (this[c] = null)
        }, a.fn.sbiOwlCarousel.Constructor.Plugins.AutoRefresh = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this._core = b, this._loaded = [], this._handlers = {
                "initialized.owl.carousel change.owl.carousel resized.owl.carousel": a.proxy(function (b) {
                    if (b.namespace && this._core.settings && this._core.settings.lazyLoad && (b.property && "position" == b.property.name || "initialized" == b.type)) for (var c = this._core.settings, e = c.center && Math.ceil(c.items / 2) || c.items, f = c.center && e * -1 || 0, g = (b.property && b.property.value !== d ? b.property.value : this._core.current()) + f, h = this._core.clones().length, i = a.proxy(function (a, b) {
                        this.load(b)
                    }, this); f++ < e;) this.load(h / 2 + this._core.relative(g)), h && a.each(this._core.clones(this._core.relative(g)), i), g++
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers)
        };
        e.Defaults = {lazyLoad: !1}, e.prototype.load = function (c) {
            var d = this._core.$stage.children().eq(c), e = d && d.find(".sbi-owl-lazy");
            !e || a.inArray(d.get(0), this._loaded) > -1 || (e.each(a.proxy(function (c, d) {
                var e, f = a(d), g = b.devicePixelRatio > 1 && f.attr("data-src-retina") || f.attr("data-src");
                this._core.trigger("load", {
                    element: f,
                    url: g
                }, "lazy"), f.is("img") ? f.one("load.owl.lazy", a.proxy(function () {
                    f.css("opacity", 1), this._core.trigger("loaded", {element: f, url: g}, "lazy")
                }, this)).attr("src", g) : (e = new Image, e.onload = a.proxy(function () {
                    f.css({
                        "background-image": 'url("' + g + '")',
                        opacity: "1"
                    }), this._core.trigger("loaded", {element: f, url: g}, "lazy")
                }, this), e.src = g)
            }, this)), this._loaded.push(d.get(0)))
        }, e.prototype.destroy = function () {
            var a, b;
            for (a in this.handlers) this._core.$element.off(a, this.handlers[a]);
            for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
        }, a.fn.sbiOwlCarousel.Constructor.Plugins.Lazy = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this._core = b, this._handlers = {
                "initialized.owl.carousel refreshed.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.autoHeight && this.update()
                }, this), "changed.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.autoHeight && "position" == a.property.name && this.update()
                }, this), "loaded.owl.lazy": a.proxy(function (a) {
                    a.namespace && this._core.settings.autoHeight && a.element.closest("." + this._core.settings.itemClass).index() === this._core.current() && this.update()
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers)
        };
        e.Defaults = {autoHeight: !1, autoHeightClass: "sbi-owl-height"}, e.prototype.update = function () {
            var b = this._core._current, c = b + this._core.settings.items,
                d = this._core.$stage.children().toArray().slice(b, c), e = [], f = 0;
            a.each(d, function (b, c) {
                e.push(a(c).height())
            }), f = Math.max.apply(null, e), this._core.$stage.parent().height(f).addClass(this._core.settings.autoHeightClass)
        }, e.prototype.destroy = function () {
            var a, b;
            for (a in this._handlers) this._core.$element.off(a, this._handlers[a]);
            for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
        }, a.fn.sbiOwlCarousel.Constructor.Plugins.AutoHeight = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this._core = b, this._videos = {}, this._playing = null, this._handlers = {
                "initialized.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.register({type: "state", name: "playing", tags: ["interacting"]})
                }, this), "resize.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.video && this.isInFullScreen() && a.preventDefault()
                }, this), "refreshed.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.is("resizing") && this._core.$stage.find(".cloned .sbi-owl-video-frame").remove()
                }, this), "changed.owl.carousel": a.proxy(function (a) {
                    a.namespace && "position" === a.property.name && this._playing && this.stop()
                }, this), "prepared.owl.carousel": a.proxy(function (b) {
                    if (b.namespace) {
                        var c = a(b.content).find(".sbi-owl-video");
                        c.length && (c.css("display", "none"), this.fetch(c, a(b.content)))
                    }
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers), this._core.$element.on("click.owl.video", ".sbi-owl-video-play-icon", a.proxy(function (a) {
                this.play(a)
            }, this))
        };
        e.Defaults = {video: !1, videoHeight: !1, videoWidth: !1}, e.prototype.fetch = function (a, b) {
            var c = function () {
                    return a.attr("data-vimeo-id") ? "vimeo" : a.attr("data-vzaar-id") ? "vzaar" : "youtube"
                }(), d = a.attr("data-vimeo-id") || a.attr("data-youtube-id") || a.attr("data-vzaar-id"),
                e = a.attr("data-width") || this._core.settings.videoWidth,
                f = a.attr("data-height") || this._core.settings.videoHeight, g = a.attr("href");
            if (!g) throw new Error("Missing video URL.");
            if (d = g.match(/(http:|https:|)\/\/(player.|www.|app.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com)|vzaar\.com)\/(video\/|videos\/|embed\/|channels\/.+\/|groups\/.+\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/), d[3].indexOf("youtu") > -1) c = "youtube"; else if (d[3].indexOf("vimeo") > -1) c = "vimeo"; else {
                if (!(d[3].indexOf("vzaar") > -1)) throw new Error("Video URL not supported.");
                c = "vzaar"
            }
            d = d[6], this._videos[g] = {
                type: c,
                id: d,
                width: e,
                height: f
            }, b.attr("data-video", g), this.thumbnail(a, this._videos[g])
        }, e.prototype.thumbnail = function (b, c) {
            var d, e, f, g = c.width && c.height ? 'style="width:' + c.width + "px;height:" + c.height + 'px;"' : "",
                h = b.find("img"), i = "src", j = "", k = this._core.settings, l = function (a) {
                    e = '<div class="sbi-owl-video-play-icon"></div>', d = k.lazyLoad ? '<div class="sbi-owl-video-tn ' + j + '" ' + i + '="' + a + '"></div>' : '<div class="sbi-owl-video-tn" style="opacity:1;background-image:url(' + a + ')"></div>', b.after(d), b.after(e)
                };
            if (b.wrap('<div class="sbi-owl-video-wrapper"' + g + "></div>"), this._core.settings.lazyLoad && (i = "data-src", j = "sbi-owl-lazy"), h.length) return l(h.attr(i)), h.remove(), !1;
            "youtube" === c.type ? (f = "//img.youtube.com/vi/" + c.id + "/hqdefault.jpg", l(f)) : "vimeo" === c.type ? a.ajax({
                type: "GET",
                url: "//vimeo.com/api/v2/video/" + c.id + ".json",
                jsonp: "callback",
                dataType: "jsonp",
                success: function (a) {
                    f = a[0].thumbnail_large, l(f)
                }
            }) : "vzaar" === c.type && a.ajax({
                type: "GET",
                url: "//vzaar.com/api/videos/" + c.id + ".json",
                jsonp: "callback",
                dataType: "jsonp",
                success: function (a) {
                    f = a.framegrab_url, l(f)
                }
            })
        }, e.prototype.stop = function () {
            this._core.trigger("stop", null, "video"), this._playing.find(".sbi-owl-video-frame").remove(), this._playing.removeClass("sbi-owl-video-playing"), this._playing = null, this._core.leave("playing"), this._core.trigger("stopped", null, "video")
        }, e.prototype.play = function (b) {
            var c, d = a(b.target), e = d.closest("." + this._core.settings.itemClass),
                f = this._videos[e.attr("data-video")], g = f.width || "100%",
                h = f.height || this._core.$stage.height();
            this._playing || (this._core.enter("playing"), this._core.trigger("play", null, "video"), e = this._core.items(this._core.relative(e.index())), this._core.reset(e.index()), "youtube" === f.type ? c = '<iframe width="' + g + '" height="' + h + '" src="//www.youtube.com/embed/' + f.id + "?autoplay=1&rel=0&v=" + f.id + '" frameborder="0" allowfullscreen></iframe>' : "vimeo" === f.type ? c = '<iframe src="//player.vimeo.com/video/' + f.id + '?autoplay=1" width="' + g + '" height="' + h + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>' : "vzaar" === f.type && (c = '<iframe frameborder="0"height="' + h + '"width="' + g + '" allowfullscreen mozallowfullscreen webkitAllowFullScreen src="//view.vzaar.com/' + f.id + '/player?autoplay=true"></iframe>'), a('<div class="sbi-owl-video-frame">' + c + "</div>").insertAfter(e.find(".sbi-owl-video")), this._playing = e.addClass("sbi-owl-video-playing"))
        }, e.prototype.isInFullScreen = function () {
            var b = c.fullscreenElement || c.mozFullScreenElement || c.webkitFullscreenElement;
            return b && a(b).parent().hasClass("sbi-owl-video-frame")
        }, e.prototype.destroy = function () {
            var a, b;
            this._core.$element.off("click.owl.video");
            for (a in this._handlers) this._core.$element.off(a, this._handlers[a]);
            for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
        }, a.fn.sbiOwlCarousel.Constructor.Plugins.Video = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this.core = b, this.core.options = a.extend({}, e.Defaults, this.core.options), this.swapping = !0, this.previous = d, this.next = d, this.handlers = {
                "change.owl.carousel": a.proxy(function (a) {
                    a.namespace && "position" == a.property.name && (this.previous = this.core.current(), this.next = a.property.value)
                }, this), "drag.owl.carousel dragged.owl.carousel translated.owl.carousel": a.proxy(function (a) {
                    a.namespace && (this.swapping = "translated" == a.type)
                }, this), "translate.owl.carousel": a.proxy(function (a) {
                    a.namespace && this.swapping && (this.core.options.animateOut || this.core.options.animateIn) && this.swap()
                }, this)
            }, this.core.$element.on(this.handlers)
        };
        e.Defaults = {animateOut: !1, animateIn: !1}, e.prototype.swap = function () {
            if (1 === this.core.settings.items && a.support.animation && a.support.transition) {
                this.core.speed(0);
                var b, c = a.proxy(this.clear, this), d = this.core.$stage.children().eq(this.previous),
                    e = this.core.$stage.children().eq(this.next), f = this.core.settings.animateIn,
                    g = this.core.settings.animateOut;
                this.core.current() !== this.previous && (g && (b = this.core.coordinates(this.previous) - this.core.coordinates(this.next), d.one(a.support.animation.end, c).css({left: b + "px"}).addClass("animated sbi-owl-animated-out").addClass(g)), f && e.one(a.support.animation.end, c).addClass("animated sbi-owl-animated-in").addClass(f))
            }
        }, e.prototype.clear = function (b) {
            a(b.target).css({left: ""}).removeClass("animated sbi-owl-animated-out sbi-owl-animated-in").removeClass(this.core.settings.animateIn).removeClass(this.core.settings.animateOut), this.core.onTransitionEnd()
        }, e.prototype.destroy = function () {
            var a, b;
            for (a in this.handlers) this.core.$element.off(a, this.handlers[a]);
            for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
        },
            a.fn.sbiOwlCarousel.Constructor.Plugins.Animate = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this._core = b, this._timeout = null, this._paused = !1, this._handlers = {
                "changed.owl.carousel": a.proxy(function (a) {
                    a.namespace && "settings" === a.property.name ? this._core.settings.autoplay ? this.play() : this.stop() : a.namespace && "position" === a.property.name && this._core.settings.autoplay && this._setAutoPlayInterval()
                }, this), "initialized.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.autoplay && this.play()
                }, this), "play.owl.autoplay": a.proxy(function (a, b, c) {
                    a.namespace && this.play(b, c)
                }, this), "stop.owl.autoplay": a.proxy(function (a) {
                    a.namespace && this.stop()
                }, this), "mouseover.owl.autoplay": a.proxy(function () {
                    this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.pause()
                }, this), "mouseleave.owl.autoplay": a.proxy(function () {
                    this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.play()
                }, this), "touchstart.owl.core": a.proxy(function () {
                    this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.pause()
                }, this), "touchend.owl.core": a.proxy(function () {
                    this._core.settings.autoplayHoverPause && this.play()
                }, this)
            }, this._core.$element.on(this._handlers), this._core.options = a.extend({}, e.Defaults, this._core.options)
        };
        e.Defaults = {
            autoplay: !1,
            autoplayTimeout: 5e3,
            autoplayHoverPause: !1,
            autoplaySpeed: !1
        }, e.prototype.play = function (a, b) {
            this._paused = !1, this._core.is("rotating") || (this._core.enter("rotating"), this._setAutoPlayInterval())
        }, e.prototype._getNextTimeout = function (d, e) {
            return this._timeout && b.clearTimeout(this._timeout), b.setTimeout(a.proxy(function () {
                this._paused || this._core.is("busy") || this._core.is("interacting") || c.hidden || this._core.next(e || this._core.settings.autoplaySpeed)
            }, this), d || this._core.settings.autoplayTimeout)
        }, e.prototype._setAutoPlayInterval = function () {
            this._timeout = this._getNextTimeout()
        }, e.prototype.stop = function () {
            this._core.is("rotating") && (b.clearTimeout(this._timeout), this._core.leave("rotating"))
        }, e.prototype.pause = function () {
            this._core.is("rotating") && (this._paused = !0)
        }, e.prototype.destroy = function () {
            var a, b;
            this.stop();
            for (a in this._handlers) this._core.$element.off(a, this._handlers[a]);
            for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
        }, a.fn.sbiOwlCarousel.Constructor.Plugins.autoplay = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        "use strict";
        var e = function (b) {
            this._core = b, this._initialized = !1, this._pages = [], this._controls = {}, this._templates = [], this.$element = this._core.$element, this._overrides = {
                next: this._core.next,
                prev: this._core.prev,
                to: this._core.to
            }, this._handlers = {
                "prepared.owl.carousel": a.proxy(function (b) {
                    b.namespace && this._core.settings.dotsData && this._templates.push('<div class="' + this._core.settings.dotClass + '">' + a(b.content).find("[data-dot]").addBack("[data-dot]").attr("data-dot") + "</div>")
                }, this), "added.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.dotsData && this._templates.splice(a.position, 0, this._templates.pop())
                }, this), "remove.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.dotsData && this._templates.splice(a.position, 1)
                }, this), "changed.owl.carousel": a.proxy(function (a) {
                    a.namespace && "position" == a.property.name && this.draw()
                }, this), "initialized.owl.carousel": a.proxy(function (a) {
                    a.namespace && !this._initialized && (this._core.trigger("initialize", null, "navigation"), this.initialize(), this.update(), this.draw(), this._initialized = !0, this._core.trigger("initialized", null, "navigation"))
                }, this), "refreshed.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._initialized && (this._core.trigger("refresh", null, "navigation"), this.update(), this.draw(), this._core.trigger("refreshed", null, "navigation"))
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this.$element.on(this._handlers)
        };
        e.Defaults = {
            nav: !1,
            navText: ["prev", "next"],
            navSpeed: !1,
            navElement: "div",
            navContainer: !1,
            navContainerClass: "sbi-owl-nav",
            navClass: ["sbi-owl-prev", "sbi-owl-next"],
            slideBy: 1,
            dotClass: "sbi-owl-dot",
            dotsClass: "sbi-owl-dots",
            dots: !0,
            dotsEach: !1,
            dotsData: !1,
            dotsSpeed: !1,
            dotsContainer: !1
        }, e.prototype.initialize = function () {
            var b, c = this._core.settings;
            this._controls.$relative = (c.navContainer ? a(c.navContainer) : a("<div>").addClass(c.navContainerClass).appendTo(this.$element)).addClass("disabled"), this._controls.$previous = a("<" + c.navElement + ">").addClass(c.navClass[0]).html(c.navText[0]).prependTo(this._controls.$relative).on("click", a.proxy(function (a) {
                this.prev(c.navSpeed)
            }, this)), this._controls.$next = a("<" + c.navElement + ">").addClass(c.navClass[1]).html(c.navText[1]).appendTo(this._controls.$relative).on("click", a.proxy(function (a) {
                this.next(c.navSpeed)
            }, this)), c.dotsData || (this._templates = [a("<div>").addClass(c.dotClass).append(a("<span>")).prop("outerHTML")]), this._controls.$absolute = (c.dotsContainer ? a(c.dotsContainer) : a("<div>").addClass(c.dotsClass).appendTo(this.$element)).addClass("disabled"), this._controls.$absolute.on("click", "div", a.proxy(function (b) {
                var d = a(b.target).parent().is(this._controls.$absolute) ? a(b.target).index() : a(b.target).parent().index();
                b.preventDefault(), this.to(d, c.dotsSpeed)
            }, this));
            for (b in this._overrides) this._core[b] = a.proxy(this[b], this)
        }, e.prototype.destroy = function () {
            var a, b, c, d;
            for (a in this._handlers) this.$element.off(a, this._handlers[a]);
            for (b in this._controls) this._controls[b].remove();
            for (d in this.overides) this._core[d] = this._overrides[d];
            for (c in Object.getOwnPropertyNames(this)) "function" != typeof this[c] && (this[c] = null)
        }, e.prototype.update = function () {
            var a, b, c, d = this._core.clones().length / 2, e = d + this._core.items().length,
                f = this._core.maximum(!0), g = this._core.settings,
                h = g.center || g.autoWidth || g.dotsData ? 1 : g.dotsEach || g.items;
            if ("page" !== g.slideBy && (g.slideBy = Math.min(g.slideBy, g.items)), g.dots || "page" == g.slideBy) for (this._pages = [], a = d, b = 0, c = 0; a < e; a++) {
                if (b >= h || 0 === b) {
                    if (this._pages.push({
                        start: Math.min(f, a - d),
                        end: a - d + h - 1
                    }), Math.min(f, a - d) === f) break;
                    b = 0, ++c
                }
                b += this._core.mergers(this._core.relative(a))
            }
        }, e.prototype.draw = function () {
            var b, c = this._core.settings, d = this._core.items().length <= c.items,
                e = this._core.relative(this._core.current()), f = c.loop || c.rewind;
            this._controls.$relative.toggleClass("disabled", !c.nav || d), c.nav && (this._controls.$previous.toggleClass("disabled", !f && e <= this._core.minimum(!0)), this._controls.$next.toggleClass("disabled", !f && e >= this._core.maximum(!0))), this._controls.$absolute.toggleClass("disabled", !c.dots || d), c.dots && (b = this._pages.length - this._controls.$absolute.children().length, c.dotsData && 0 !== b ? this._controls.$absolute.html(this._templates.join("")) : b > 0 ? this._controls.$absolute.append(new Array(b + 1).join(this._templates[0])) : b < 0 && this._controls.$absolute.children().slice(b).remove(), this._controls.$absolute.find(".active").removeClass("active"), this._controls.$absolute.children().eq(a.inArray(this.current(), this._pages)).addClass("active"))
        }, e.prototype.onTrigger = function (b) {
            var c = this._core.settings;
            b.page = {
                index: a.inArray(this.current(), this._pages),
                count: this._pages.length,
                size: c && (c.center || c.autoWidth || c.dotsData ? 1 : c.dotsEach || c.items)
            }
        }, e.prototype.current = function () {
            var b = this._core.relative(this._core.current());
            return a.grep(this._pages, a.proxy(function (a, c) {
                return a.start <= b && a.end >= b
            }, this)).pop()
        }, e.prototype.getPosition = function (b) {
            var c, d, e = this._core.settings;
            return "page" == e.slideBy ? (c = a.inArray(this.current(), this._pages), d = this._pages.length, b ? ++c : --c, c = this._pages[(c % d + d) % d].start) : (c = this._core.relative(this._core.current()), d = this._core.items().length, b ? c += e.slideBy : c -= e.slideBy), c
        }, e.prototype.next = function (b) {
            a.proxy(this._overrides.to, this._core)(this.getPosition(!0), b)
        }, e.prototype.prev = function (b) {
            a.proxy(this._overrides.to, this._core)(this.getPosition(!1), b)
        }, e.prototype.to = function (b, c, d) {
            var e;
            !d && this._pages.length ? (e = this._pages.length, a.proxy(this._overrides.to, this._core)(this._pages[(b % e + e) % e].start, c)) : a.proxy(this._overrides.to, this._core)(b, c)
        }, a.fn.sbiOwlCarousel.Constructor.Plugins.Navigation = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        "use strict";
        var e = function (c) {
            this._core = c, this._hashes = {}, this.$element = this._core.$element, this._handlers = {
                "initialized.owl.carousel": a.proxy(function (c) {
                    c.namespace && "URLHash" === this._core.settings.startPosition && a(b).trigger("hashchange.owl.navigation")
                }, this), "prepared.owl.carousel": a.proxy(function (b) {
                    if (b.namespace) {
                        var c = a(b.content).find("[data-hash]").addBack("[data-hash]").attr("data-hash");
                        if (!c) return;
                        this._hashes[c] = b.content
                    }
                }, this), "changed.owl.carousel": a.proxy(function (c) {
                    if (c.namespace && "position" === c.property.name) {
                        var d = this._core.items(this._core.relative(this._core.current())),
                            e = a.map(this._hashes, function (a, b) {
                                return a === d ? b : null
                            }).join();
                        if (!e || b.location.hash.slice(1) === e) return;
                        b.location.hash = e
                    }
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this.$element.on(this._handlers), a(b).on("hashchange.owl.navigation", a.proxy(function (a) {
                var c = b.location.hash.substring(1), e = this._core.$stage.children(),
                    f = this._hashes[c] && e.index(this._hashes[c]);
                f !== d && f !== this._core.current() && this._core.to(this._core.relative(f), !1, !0)
            }, this))
        };
        e.Defaults = {URLhashListener: !1}, e.prototype.destroy = function () {
            var c, d;
            a(b).off("hashchange.owl.navigation");
            for (c in this._handlers) this._core.$element.off(c, this._handlers[c]);
            for (d in Object.getOwnPropertyNames(this)) "function" != typeof this[d] && (this[d] = null)
        }, a.fn.sbiOwlCarousel.Constructor.Plugins.Hash = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        function e(b, c) {
            var e = !1, f = b.charAt(0).toUpperCase() + b.slice(1);
            return a.each((b + " " + h.join(f + " ") + f).split(" "), function (a, b) {
                if (g[b] !== d) return e = !c || b, !1
            }), e
        }

        function f(a) {
            return e(a, !0)
        }

        var g = a("<support>").get(0).style, h = "Webkit Moz O ms".split(" "), i = {
            transition: {
                end: {
                    WebkitTransition: "webkitTransitionEnd",
                    MozTransition: "transitionend",
                    OTransition: "oTransitionEnd",
                    transition: "transitionend"
                }
            },
            animation: {
                end: {
                    WebkitAnimation: "webkitAnimationEnd",
                    MozAnimation: "animationend",
                    OAnimation: "oAnimationEnd",
                    animation: "animationend"
                }
            }
        }, j = {
            csstransforms: function () {
                return !!e("transform")
            }, csstransforms3d: function () {
                return !!e("perspective")
            }, csstransitions: function () {
                return !!e("transition")
            }, cssanimations: function () {
                return !!e("animation")
            }
        };
        j.csstransitions() && (a.support.transition = new String(f("transition")), a.support.transition.end = i.transition.end[a.support.transition]), j.cssanimations() && (a.support.animation = new String(f("animation")), a.support.animation.end = i.animation.end[a.support.animation]), j.csstransforms() && (a.support.transform = new String(f("transform")), a.support.transform3d = j.csstransforms3d())
    }(window.Zepto || window.jQuery, window, document);

    !function (a, b) {
        "function" == typeof define && define.amd ? define("packery/js/rect", b) : "object" == typeof module && module.exports ? module.exports = b() : (a.Packery = a.Packery || {}, a.Packery.Rect = b())
    }(window, function () {
        function a(b) {
            for (var c in a.defaults) this[c] = a.defaults[c];
            for (c in b) this[c] = b[c]
        }

        a.defaults = {x: 0, y: 0, width: 0, height: 0};
        var b = a.prototype;
        return b.contains = function (a) {
            var b = a.width || 0, c = a.height || 0;
            return this.x <= a.x && this.y <= a.y && this.x + this.width >= a.x + b && this.y + this.height >= a.y + c
        }, b.overlaps = function (a) {
            var b = this.x + this.width, c = this.y + this.height, d = a.x + a.width, e = a.y + a.height;
            return this.x < d && b > a.x && this.y < e && c > a.y
        }, b.getMaximalFreeRects = function (b) {
            if (!this.overlaps(b)) return !1;
            var c, d = [], e = this.x + this.width, f = this.y + this.height, g = b.x + b.width, h = b.y + b.height;
            return this.y < b.y && (c = new a({
                x: this.x,
                y: this.y,
                width: this.width,
                height: b.y - this.y
            }), d.push(c)), e > g && (c = new a({
                x: g,
                y: this.y,
                width: e - g,
                height: this.height
            }), d.push(c)), f > h && (c = new a({
                x: this.x,
                y: h,
                width: this.width,
                height: f - h
            }), d.push(c)), this.x < b.x && (c = new a({
                x: this.x,
                y: this.y,
                width: b.x - this.x,
                height: this.height
            }), d.push(c)), d
        }, b.canFit = function (a) {
            return this.width >= a.width && this.height >= a.height
        }, a
    }), function (a, b) {
        if ("function" == typeof define && define.amd) define("packery/js/packer", ["./rect"], b); else if ("object" == typeof module && module.exports) module.exports = b(require("./rect")); else {
            var c = a.Packery = a.Packery || {};
            c.Packer = b(c.Rect)
        }
    }(window, function (a) {
        function b(a, b, c) {
            this.width = a || 0, this.height = b || 0, this.sortDirection = c || "downwardLeftToRight", this.reset()
        }

        var c = b.prototype;
        c.reset = function () {
            this.spaces = [];
            var b = new a({x: 0, y: 0, width: this.width, height: this.height});
            this.spaces.push(b), this.sorter = d[this.sortDirection] || d.downwardLeftToRight
        }, c.pack = function (a) {
            for (var b = 0; b < this.spaces.length; b++) {
                var c = this.spaces[b];
                if (c.canFit(a)) {
                    this.placeInSpace(a, c);
                    break
                }
            }
        }, c.columnPack = function (a) {
            for (var b = 0; b < this.spaces.length; b++) {
                var c = this.spaces[b], d = c.x <= a.x && c.x + c.width >= a.x + a.width && c.height >= a.height - .01;
                if (d) {
                    a.y = c.y, this.placed(a);
                    break
                }
            }
        }, c.rowPack = function (a) {
            for (var b = 0; b < this.spaces.length; b++) {
                var c = this.spaces[b], d = c.y <= a.y && c.y + c.height >= a.y + a.height && c.width >= a.width - .01;
                if (d) {
                    a.x = c.x, this.placed(a);
                    break
                }
            }
        }, c.placeInSpace = function (a, b) {
            a.x = b.x, a.y = b.y, this.placed(a)
        }, c.placed = function (a) {
            for (var b = [], c = 0; c < this.spaces.length; c++) {
                var d = this.spaces[c], e = d.getMaximalFreeRects(a);
                e ? b.push.apply(b, e) : b.push(d)
            }
            this.spaces = b, this.mergeSortSpaces()
        }, c.mergeSortSpaces = function () {
            b.mergeRects(this.spaces), this.spaces.sort(this.sorter)
        }, c.addSpace = function (a) {
            this.spaces.push(a), this.mergeSortSpaces()
        }, b.mergeRects = function (a) {
            var b = 0, c = a[b];
            a:for (; c;) {
                for (var d = 0, e = a[b + d]; e;) {
                    if (e == c) d++; else {
                        if (e.contains(c)) {
                            a.splice(b, 1), c = a[b];
                            continue a
                        }
                        c.contains(e) ? a.splice(b + d, 1) : d++
                    }
                    e = a[b + d]
                }
                b++, c = a[b]
            }
            return a
        };
        var d = {
            downwardLeftToRight: function (a, b) {
                return a.y - b.y || a.x - b.x
            }, rightwardTopToBottom: function (a, b) {
                return a.x - b.x || a.y - b.y
            }
        };
        return b
    }), function (a, b) {
        "function" == typeof define && define.amd ? define("packery/js/item", ["outlayer/outlayer", "./rect"], b) : "object" == typeof module && module.exports ? module.exports = b(require("outlayer"), require("./rect")) : a.Packery.Item = b(a.Outlayer, a.Packery.Rect)
    }(window, function (a, b) {
        var c = document.documentElement.style, d = "string" == typeof c.transform ? "transform" : "WebkitTransform",
            e = function () {
                a.Item.apply(this, arguments)
            }, f = e.prototype = Object.create(a.Item.prototype), g = f._create;
        f._create = function () {
            g.call(this), this.rect = new b
        };
        var h = f.moveTo;
        return f.moveTo = function (a, b) {
            var c = Math.abs(this.position.x - a), d = Math.abs(this.position.y - b),
                e = this.layout.dragItemCount && !this.isPlacing && !this.isTransitioning && 1 > c && 1 > d;
            return e ? void this.goTo(a, b) : void h.apply(this, arguments)
        }, f.enablePlacing = function () {
            this.removeTransitionStyles(), this.isTransitioning && d && (this.element.style[d] = "none"), this.isTransitioning = !1, this.getSize(), this.layout._setRectSize(this.element, this.rect), this.isPlacing = !0
        }, f.disablePlacing = function () {
            this.isPlacing = !1
        }, f.removeElem = function () {
            this.element.parentNode.removeChild(this.element), this.layout.packer.addSpace(this.rect), this.emitEvent("remove", [this])
        }, f.showDropPlaceholder = function () {
            var a = this.dropPlaceholder;
            a || (a = this.dropPlaceholder = document.createElement("div"), a.className = "packery-drop-placeholder", a.style.position = "absolute"), a.style.width = this.size.width + "px", a.style.height = this.size.height + "px", this.positionDropPlaceholder(), this.layout.element.appendChild(a)
        }, f.positionDropPlaceholder = function () {
            this.dropPlaceholder.style[d] = "translate(" + this.rect.x + "px, " + this.rect.y + "px)"
        }, f.hideDropPlaceholder = function () {
            this.layout.element.removeChild(this.dropPlaceholder)
        }, e
    }), function (a, b) {
        "function" == typeof define && define.amd ? define("packery/js/packery", ["get-size/get-size", "outlayer/outlayer", "./rect", "./packer", "./item"], b) : "object" == typeof module && module.exports ? module.exports = b(require("get-size"), require("outlayer"), require("./rect"), require("./packer"), require("./item")) : a.Packery = b(a.getSize, a.Outlayer, a.Packery.Rect, a.Packery.Packer, a.Packery.Item)
    }(window, function (a, b, c, d, e) {
        function f(a, b) {
            return a.position.y - b.position.y || a.position.x - b.position.x
        }

        function g(a, b) {
            return a.position.x - b.position.x || a.position.y - b.position.y
        }

        function h(a, b) {
            var c = b.x - a.x, d = b.y - a.y;
            return Math.sqrt(c * c + d * d)
        }

        c.prototype.canFit = function (a) {
            return this.width >= a.width - 1 && this.height >= a.height - 1
        };
        var i = b.create("packery");
        i.Item = e;
        var j = i.prototype;
        j._create = function () {
            b.prototype._create.call(this), this.packer = new d, this.shiftPacker = new d, this.isEnabled = !0, this.dragItemCount = 0;
            var a = this;
            this.handleDraggabilly = {
                dragStart: function () {
                    a.itemDragStart(this.element)
                }, dragMove: function () {
                    a.itemDragMove(this.element, this.position.x, this.position.y)
                }, dragEnd: function () {
                    a.itemDragEnd(this.element)
                }
            }, this.handleUIDraggable = {
                start: function (b, c) {
                    c && a.itemDragStart(b.currentTarget)
                }, drag: function (b, c) {
                    c && a.itemDragMove(b.currentTarget, c.position.left, c.position.top)
                }, stop: function (b, c) {
                    c && a.itemDragEnd(b.currentTarget)
                }
            }
        }, j._resetLayout = function () {
            this.getSize(), this._getMeasurements();
            var a, b, c;
            this._getOption("horizontal") ? (a = 1 / 0, b = this.size.innerHeight + this.gutter, c = "rightwardTopToBottom") : (a = this.size.innerWidth + this.gutter, b = 1 / 0, c = "downwardLeftToRight"), this.packer.width = this.shiftPacker.width = a, this.packer.height = this.shiftPacker.height = b, this.packer.sortDirection = this.shiftPacker.sortDirection = c, this.packer.reset(), this.maxY = 0, this.maxX = 0
        }, j._getMeasurements = function () {
            this._getMeasurement("columnWidth", "width"), this._getMeasurement("rowHeight", "height"), this._getMeasurement("gutter", "width")
        }, j._getItemLayoutPosition = function (a) {
            if (this._setRectSize(a.element, a.rect), this.isShifting || this.dragItemCount > 0) {
                var b = this._getPackMethod();
                this.packer[b](a.rect)
            } else this.packer.pack(a.rect);
            return this._setMaxXY(a.rect), a.rect
        }, j.shiftLayout = function () {
            this.isShifting = !0, this.layout(), delete this.isShifting
        }, j._getPackMethod = function () {
            return this._getOption("horizontal") ? "rowPack" : "columnPack"
        }, j._setMaxXY = function (a) {
            this.maxX = Math.max(a.x + a.width, this.maxX), this.maxY = Math.max(a.y + a.height, this.maxY)
        }, j._setRectSize = function (b, c) {
            var d = a(b), e = d.outerWidth, f = d.outerHeight;
            (e || f) && (e = this._applyGridGutter(e, this.columnWidth), f = this._applyGridGutter(f, this.rowHeight)), c.width = Math.min(e, this.packer.width), c.height = Math.min(f, this.packer.height)
        }, j._applyGridGutter = function (a, b) {
            if (!b) return a + this.gutter;
            b += this.gutter;
            var c = a % b, d = c && 1 > c ? "round" : "ceil";
            return a = Math[d](a / b) * b
        }, j._getContainerSize = function () {
            return this._getOption("horizontal") ? {width: this.maxX - this.gutter} : {height: this.maxY - this.gutter}
        }, j._manageStamp = function (a) {
            var b, d = this.getItem(a);
            if (d && d.isPlacing) b = d.rect; else {
                var e = this._getElementOffset(a);
                b = new c({
                    x: this._getOption("originLeft") ? e.left : e.right,
                    y: this._getOption("originTop") ? e.top : e.bottom
                })
            }
            this._setRectSize(a, b), this.packer.placed(b), this._setMaxXY(b)
        }, j.sortItemsByPosition = function () {
            var a = this._getOption("horizontal") ? g : f;
            this.items.sort(a)
        }, j.fit = function (a, b, c) {
            var d = this.getItem(a);
            d && (this.stamp(d.element), d.enablePlacing(), this.updateShiftTargets(d), b = void 0 === b ? d.rect.x : b, c = void 0 === c ? d.rect.y : c, this.shift(d, b, c), this._bindFitEvents(d), d.moveTo(d.rect.x, d.rect.y), this.shiftLayout(), this.unstamp(d.element), this.sortItemsByPosition(), d.disablePlacing())
        }, j._bindFitEvents = function (a) {
            function b() {
                d++, 2 == d && c.dispatchEvent("fitComplete", null, [a])
            }

            var c = this, d = 0;
            a.once("layout", b), this.once("layoutComplete", b)
        }, j.resize = function () {
            this.isResizeBound && this.needsResizeLayout() && (this.options.shiftPercentResize ? this.resizeShiftPercentLayout() : this.layout())
        }, j.needsResizeLayout = function () {
            var b = a(this.element), c = this._getOption("horizontal") ? "innerHeight" : "innerWidth";
            return b[c] != this.size[c]
        }, j.resizeShiftPercentLayout = function () {
            var b = this._getItemsForLayout(this.items), c = this._getOption("horizontal"), d = c ? "y" : "x",
                e = c ? "height" : "width", f = c ? "rowHeight" : "columnWidth", g = c ? "innerHeight" : "innerWidth",
                h = this[f];
            if (h = h && h + this.gutter) {
                this._getMeasurements();
                var i = this[f] + this.gutter;
                b.forEach(function (a) {
                    var b = Math.round(a.rect[d] / h);
                    a.rect[d] = b * i
                })
            } else {
                var j = a(this.element)[g] + this.gutter, k = this.packer[e];
                b.forEach(function (a) {
                    a.rect[d] = a.rect[d] / k * j
                })
            }
            this.shiftLayout()
        }, j.itemDragStart = function (a) {
            if (this.isEnabled) {
                this.stamp(a);
                var b = this.getItem(a);
                b && (b.enablePlacing(), b.showDropPlaceholder(), this.dragItemCount++, this.updateShiftTargets(b))
            }
        }, j.updateShiftTargets = function (a) {
            this.shiftPacker.reset(), this._getBoundingRect();
            var b = this._getOption("originLeft"), d = this._getOption("originTop");
            this.stamps.forEach(function (a) {
                var e = this.getItem(a);
                if (!e || !e.isPlacing) {
                    var f = this._getElementOffset(a), g = new c({x: b ? f.left : f.right, y: d ? f.top : f.bottom});
                    this._setRectSize(a, g), this.shiftPacker.placed(g)
                }
            }, this);
            var e = this._getOption("horizontal"), f = e ? "rowHeight" : "columnWidth", g = e ? "height" : "width";
            this.shiftTargetKeys = [], this.shiftTargets = [];
            var h, i = this[f];
            if (i = i && i + this.gutter) {
                var j = Math.ceil(a.rect[g] / i), k = Math.floor((this.shiftPacker[g] + this.gutter) / i);
                h = (k - j) * i;
                for (var l = 0; k > l; l++) this._addShiftTarget(l * i, 0, h)
            } else h = this.shiftPacker[g] + this.gutter - a.rect[g], this._addShiftTarget(0, 0, h);
            var m = this._getItemsForLayout(this.items), n = this._getPackMethod();
            m.forEach(function (a) {
                var b = a.rect;
                this._setRectSize(a.element, b), this.shiftPacker[n](b), this._addShiftTarget(b.x, b.y, h);
                var c = e ? b.x + b.width : b.x, d = e ? b.y : b.y + b.height;
                if (this._addShiftTarget(c, d, h), i) for (var f = Math.round(b[g] / i), j = 1; f > j; j++) {
                    var k = e ? c : b.x + i * j, l = e ? b.y + i * j : d;
                    this._addShiftTarget(k, l, h)
                }
            }, this)
        }, j._addShiftTarget = function (a, b, c) {
            var d = this._getOption("horizontal") ? b : a;
            if (!(0 !== d && d > c)) {
                var e = a + "," + b, f = -1 != this.shiftTargetKeys.indexOf(e);
                f || (this.shiftTargetKeys.push(e), this.shiftTargets.push({x: a, y: b}))
            }
        }, j.shift = function (a, b, c) {
            var d, e = 1 / 0, f = {x: b, y: c};
            this.shiftTargets.forEach(function (a) {
                var b = h(a, f);
                e > b && (d = a, e = b)
            }), a.rect.x = d.x, a.rect.y = d.y
        };
        var k = 120;
        j.itemDragMove = function (a, b, c) {
            function d() {
                f.shift(e, b, c), e.positionDropPlaceholder(), f.layout()
            }

            var e = this.isEnabled && this.getItem(a);
            if (e) {
                b -= this.size.paddingLeft, c -= this.size.paddingTop;
                var f = this, g = new Date;
                this._itemDragTime && g - this._itemDragTime < k ? (clearTimeout(this.dragTimeout), this.dragTimeout = setTimeout(d, k)) : (d(), this._itemDragTime = g)
            }
        }, j.itemDragEnd = function (a) {
            function b() {
                d++, 2 == d && (c.element.classList.remove("is-positioning-post-drag"), c.hideDropPlaceholder(), e.dispatchEvent("dragItemPositioned", null, [c]))
            }

            var c = this.isEnabled && this.getItem(a);
            if (c) {
                clearTimeout(this.dragTimeout), c.element.classList.add("is-positioning-post-drag");
                var d = 0, e = this;
                c.once("layout", b), this.once("layoutComplete", b), c.moveTo(c.rect.x, c.rect.y), this.layout(), this.dragItemCount = Math.max(0, this.dragItemCount - 1), this.sortItemsByPosition(), c.disablePlacing(), this.unstamp(c.element)
            }
        }, j.bindDraggabillyEvents = function (a) {
            this._bindDraggabillyEvents(a, "on")
        }, j.unbindDraggabillyEvents = function (a) {
            this._bindDraggabillyEvents(a, "off")
        }, j._bindDraggabillyEvents = function (a, b) {
            var c = this.handleDraggabilly;
            a[b]("dragStart", c.dragStart), a[b]("dragMove", c.dragMove), a[b]("dragEnd", c.dragEnd)
        }, j.bindUIDraggableEvents = function (a) {
            this._bindUIDraggableEvents(a, "on")
        }, j.unbindUIDraggableEvents = function (a) {
            this._bindUIDraggableEvents(a, "off")
        }, j._bindUIDraggableEvents = function (a, b) {
            var c = this.handleUIDraggable;
            a[b]("dragstart", c.start)[b]("drag", c.drag)[b]("dragstop", c.stop)
        };
        var l = j.destroy;
        return j.destroy = function () {
            l.apply(this, arguments), this.isEnabled = !1
        }, i.Rect = c, i.Packer = d, i
    }), function (a, b) {
        "function" == typeof define && define.amd ? define(["isotope-layout/js/layout-mode", "packery/js/packery"], b) : "object" == typeof module && module.exports ? module.exports = b(require("isotope-layout/js/layout-mode"), require("packery")) : b(a.Smashotope.LayoutMode, a.Packery)
    }(window, function (a, b) {
        var c = a.create("packery"), d = c.prototype, e = {_getElementOffset: !0, _getMeasurement: !0};
        for (var f in b.prototype) e[f] || (d[f] = b.prototype[f]);
        var g = d._resetLayout;
        d._resetLayout = function () {
            this.packer = this.packer || new b.Packer, this.shiftPacker = this.shiftPacker || new b.Packer, g.apply(this, arguments)
        };
        var h = d._getItemLayoutPosition;
        d._getItemLayoutPosition = function (a) {
            return a.rect = a.rect || new b.Rect, h.call(this, a)
        };
        var i = d.needsResizeLayout;
        d.needsResizeLayout = function () {
            return this._getOption("horizontal") ? this.needsVerticalResizeLayout() : i.call(this)
        };
        var j = d._getOption;
        return d._getOption = function (a) {
            return "horizontal" == a ? void 0 !== this.options.isHorizontal ? this.options.isHorizontal : this.options.horizontal : j.apply(this.smashotope, arguments)
        }, c
    });

    function sbyAddImgLiquid() {
        /*! imgLiquid v0.9.944 / 03-05-2013 https://github.com/karacas/imgLiquid */
        var sbsw_imgLiquid = sbsw_imgLiquid || {VER: "0.9.944"};
        sbsw_imgLiquid.bgs_Available = !1, sbsw_imgLiquid.bgs_CheckRunned = !1, function (i) {
            function t() {
                if (!sbsw_imgLiquid.bgs_CheckRunned) {
                    sbsw_imgLiquid.bgs_CheckRunned = !0;
                    var t = i('<span style="background-size:cover" />');
                    i("body").append(t), !function () {
                        var i = t[0];
                        if (i && window.getComputedStyle) {
                            var e = window.getComputedStyle(i, null);
                            e && e.backgroundSize && (sbsw_imgLiquid.bgs_Available = "cover" === e.backgroundSize)
                        }
                    }(), t.remove()
                }
            }

            i.fn.extend({
                sbsw_imgLiquid: function (e) {
                    this.defaults = {
                        fill: !0,
                        verticalAlign: "center",
                        horizontalAlign: "center",
                        useBackgroundSize: !0,
                        useDataHtmlAttr: !0,
                        responsive: !0,
                        delay: 0,
                        fadeInTime: 0,
                        removeBoxBackground: !0,
                        hardPixels: !0,
                        responsiveCheckTime: 500,
                        timecheckvisibility: 500,
                        onStart: null,
                        onFinish: null,
                        onItemStart: null,
                        onItemFinish: null,
                        onItemError: null
                    }, t();
                    var a = this;
                    return this.options = e, this.settings = i.extend({}, this.defaults, this.options), this.settings.onStart && this.settings.onStart(), this.each(function (t) {
                        function e() {
                            -1 === u.css("background-image").indexOf(encodeURI(c.attr("src"))) && u.css({"background-image": 'url("' + encodeURI(c.attr("src")) + '")'}), u.css({
                                "background-size": g.fill ? "cover" : "contain",
                                "background-position": (g.horizontalAlign + " " + g.verticalAlign).toLowerCase(),
                                "background-repeat": "no-repeat"
                            }), i("a:first", u).css({
                                display: "block",
                                width: "100%",
                                height: "100%"
                            }), i("img", u).css({display: "none"}), g.onItemFinish && g.onItemFinish(t, u, c), u.addClass("sbsw_imgLiquid_bgSize"), u.addClass("sbsw_imgLiquid_ready"), l()
                        }

                        function o() {
                            function e() {
                                c.data("sbsw_imgLiquid_error") || c.data("sbsw_imgLiquid_loaded") || c.data("sbsw_imgLiquid_oldProcessed") || (u.is(":visible") && c[0].complete && c[0].width > 0 && c[0].height > 0 ? (c.data("sbsw_imgLiquid_loaded", !0), setTimeout(r, t * g.delay)) : setTimeout(e, g.timecheckvisibility))
                            }

                            if (c.data("oldSrc") && c.data("oldSrc") !== c.attr("src")) {
                                var a = c.clone().removeAttr("style");
                                return a.data("sbsw_imgLiquid_settings", c.data("sbsw_imgLiquid_settings")), c.parent().prepend(a), c.remove(), c = a, c[0].width = 0, void setTimeout(o, 10)
                            }
                            return c.data("sbsw_imgLiquid_oldProcessed") ? void r() : (c.data("sbsw_imgLiquid_oldProcessed", !1), c.data("oldSrc", c.attr("src")), i("img:not(:first)", u).css("display", "none"), u.css({overflow: "hidden"}), c.fadeTo(0, 0).removeAttr("width").removeAttr("height").css({
                                visibility: "visible",
                                "max-width": "none",
                                "max-height": "none",
                                width: "auto",
                                height: "auto",
                                display: "block"
                            }), c.on("error", n), c[0].onerror = n, e(), void d())
                        }

                        function d() {
                            (g.responsive || c.data("sbsw_imgLiquid_oldProcessed")) && c.data("sbsw_imgLiquid_settings") && (g = c.data("sbsw_imgLiquid_settings"), u.actualSize = u.get(0).offsetWidth + u.get(0).offsetHeight / 1e4, u.sizeOld && u.actualSize !== u.sizeOld && r(), u.sizeOld = u.actualSize, setTimeout(d, g.responsiveCheckTime))
                        }

                        function n() {
                            c.data("sbsw_imgLiquid_error", !0), u.addClass("sbsw_imgLiquid_error"), g.onItemError && g.onItemError(t, u, c), l()
                        }

                        function s() {
                            var i = {};
                            if (a.settings.useDataHtmlAttr) {
                                var t = u.attr("data-sbsw_imgLiquid-fill"),
                                    e = u.attr("data-sbsw_imgLiquid-horizontalAlign"),
                                    o = u.attr("data-sbsw_imgLiquid-verticalAlign");
                                ("true" === t || "false" === t) && (i.fill = Boolean("true" === t)), void 0 === e || "left" !== e && "center" !== e && "right" !== e && -1 === e.indexOf("%") || (i.horizontalAlign = e), void 0 === o || "top" !== o && "bottom" !== o && "center" !== o && -1 === o.indexOf("%") || (i.verticalAlign = o)
                            }
                            return sbsw_imgLiquid.isIE && a.settings.ieFadeInDisabled && (i.fadeInTime = 0), i
                        }

                        function r() {
                            var i, e, a, o, d, n, s, r, m = 0, h = 0, f = u.width(), v = u.height();
                            void 0 === c.data("owidth") && c.data("owidth", c[0].width), void 0 === c.data("oheight") && c.data("oheight", c[0].height), g.fill === f / v >= c.data("owidth") / c.data("oheight") ? (i = "100%", e = "auto", a = Math.floor(f), o = Math.floor(f * (c.data("oheight") / c.data("owidth")))) : (i = "auto", e = "100%", a = Math.floor(v * (c.data("owidth") / c.data("oheight"))), o = Math.floor(v)), d = g.horizontalAlign.toLowerCase(), s = f - a, "left" === d && (h = 0), "center" === d && (h = .5 * s), "right" === d && (h = s), -1 !== d.indexOf("%") && (d = parseInt(d.replace("%", ""), 10), d > 0 && (h = s * d * .01)), n = g.verticalAlign.toLowerCase(), r = v - o, "left" === n && (m = 0), "center" === n && (m = .5 * r), "bottom" === n && (m = r), -1 !== n.indexOf("%") && (n = parseInt(n.replace("%", ""), 10), n > 0 && (m = r * n * .01)), g.hardPixels && (i = a, e = o), c.css({
                                width: i,
                                height: e,
                                "margin-left": Math.floor(h),
                                "margin-top": Math.floor(m)
                            }), c.data("sbsw_imgLiquid_oldProcessed") || (c.fadeTo(g.fadeInTime, 1), c.data("sbsw_imgLiquid_oldProcessed", !0), g.removeBoxBackground && u.css("background-image", "none"), u.addClass("sbsw_imgLiquid_nobgSize"), u.addClass("sbsw_imgLiquid_ready")), g.onItemFinish && g.onItemFinish(t, u, c), l()
                        }

                        function l() {
                            t === a.length - 1 && a.settings.onFinish && a.settings.onFinish()
                        }

                        var g = a.settings, u = i(this), c = i("img:first", u);
                        return c.length ? (c.data("sbsw_imgLiquid_settings") ? (u.removeClass("sbsw_imgLiquid_error").removeClass("sbsw_imgLiquid_ready"), g = i.extend({}, c.data("sbsw_imgLiquid_settings"), a.options)) : g = i.extend({}, a.settings, s()), c.data("sbsw_imgLiquid_settings", g), g.onItemStart && g.onItemStart(t, u, c), void (sbsw_imgLiquid.bgs_Available && g.useBackgroundSize ? e() : o())) : void n()
                    })
                }
            })
        }(jQuery);

        // Use imagefill to set the images as backgrounds so they can be square
        !function () {
            var css = sbsw_imgLiquid.injectCss,
                head = document.getElementsByTagName('head')[0],
                style = document.createElement('style');
            style.type = 'text/css';
            if (style.styleSheet) {
                style.styleSheet.cssText = css;
            } else {
                style.appendChild(document.createTextNode(css));
            }
            head.appendChild(style);
        }();
    }

    /* JavaScript Linkify - v0.3 - 6/27/2009 - http://benalman.com/projects/javascript-linkify/ */
    window.sbyLinkify = (function () {
        var k = "[a-z\\d.-]+://",
            h = "(?:(?:[0-9]|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])\\.){3}(?:[0-9]|[1-9]\\d|1\\d{2}|2[0-4]\\d|25[0-5])",
            c = "(?:(?:[^\\s!@#$%^&*()_=+[\\]{}\\\\|;:'\",.<>/?]+)\\.)+",
            n = "(?:ac|ad|aero|ae|af|ag|ai|al|am|an|ao|aq|arpa|ar|asia|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|biz|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|cat|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|coop|com|co|cr|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|info|int|in|io|iq|ir|is|it|je|jm|jobs|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mil|mk|ml|mm|mn|mobi|mo|mp|mq|mr|ms|mt|museum|mu|mv|mw|mx|my|mz|name|na|nc|net|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pro|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tel|tf|tg|th|tj|tk|tl|tm|tn|to|tp|travel|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|xn--0zwm56d|xn--11b5bs3a9aj6g|xn--80akhbyknj4f|xn--9t4b11yi5a|xn--deba0ad|xn--g6w251d|xn--hgbk6aj7f53bba|xn--hlcj6aya9esc7a|xn--jxalpdlp|xn--kgbechtv|xn--zckzah|ye|yt|yu|za|zm|zw)",
            f = "(?:" + c + n + "|" + h + ")", o = "(?:[;/][^#?<>\\s]*)?", e = "(?:\\?[^#<>\\s]*)?(?:#[^<>\\s]*)?",
            d = "\\b" + k + "[^<>\\s]+", a = "\\b" + f + o + e + "(?!\\w)", m = "mailto:",
            j = "(?:" + m + ")?[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@" + f + e + "(?!\\w)",
            l = new RegExp("(?:" + d + "|" + a + "|" + j + ")", "ig"), g = new RegExp("^" + k, "i"),
            b = {"'": "`", ">": "<", ")": "(", "]": "[", "}": "{", "B;": "B+", "b:": "b9"}, i = {
                callback: function (q, p) {
                    return p ? '<a href="' + p + '" title="' + p + '" target="_blank" rel="noopener">' + q + "</a>" : q
                }, punct_regexp: /(?:[!?.,:;'"]|(?:&|&amp;)(?:lt|gt|quot|apos|raquo|laquo|rsaquo|lsaquo);)$/
            };
        return function (u, z) {
            z = z || {};
            var w, v, A, p, x = "", t = [], s, E, C, y, q, D, B, r;
            for (v in i) {
                if (z[v] === undefined) {
                    z[v] = i[v]
                }
            }
            while (w = l.exec(u)) {
                A = w[0];
                E = l.lastIndex;
                C = E - A.length;
                if (/[\/:]/.test(u.charAt(C - 1))) {
                    continue
                }
                do {
                    y = A;
                    r = A.substr(-1);
                    B = b[r];
                    if (B) {
                        q = A.match(new RegExp("\\" + B + "(?!$)", "g"));
                        D = A.match(new RegExp("\\" + r, "g"));
                        if ((q ? q.length : 0) < (D ? D.length : 0)) {
                            A = A.substr(0, A.length - 1);
                            E--
                        }
                    }
                    if (z.punct_regexp) {
                        A = A.replace(z.punct_regexp, function (F) {
                            E -= F.length;
                            return ""
                        })
                    }
                } while (A.length && A !== y);
                p = A;
                if (!g.test(p)) {
                    p = (p.indexOf("@") !== -1 ? (!p.indexOf(m) ? "" : m) : !p.indexOf("irc.") ? "irc://" : !p.indexOf("ftp.") ? "ftp://" : "http://") + p
                }
                if (s != C) {
                    t.push([u.slice(s, C)]);
                    s = E
                }
                t.push([A, p])
            }
            t.push([u.substr(s)]);
            for (v = 0; v < t.length; v++) {
                x += z.callback.apply(window, t[v])
            }
            return x || u
        }
    })();

    //Checks whether browser support HTML5 video element
    function sbsw_supports_video() {
        return !!document.createElement('video').canPlayType;
    }

    // Carousel
    !function (a, b, c, d) {
        function e(b, c) {
            this.settings = null, this.options = a.extend({}, e.Defaults, c), this.$element = a(b), this._handlers = {}, this._plugins = {}, this._supress = {}, this._current = null, this._speed = null, this._coordinates = [], this._breakpoint = null, this._width = null, this._items = [], this._clones = [], this._mergers = [], this._widths = [], this._invalidated = {}, this._pipe = [], this._drag = {
                time: null,
                target: null,
                pointer: null,
                stage: {start: null, current: null},
                direction: null
            }, this._states = {
                current: {},
                tags: {initializing: ["busy"], animating: ["busy"], dragging: ["interacting"]}
            }, a.each(["onResize", "onThrottledResize"], a.proxy(function (b, c) {
                this._handlers[c] = a.proxy(this[c], this)
            }, this)), a.each(e.Plugins, a.proxy(function (a, b) {
                this._plugins[a.charAt(0).toLowerCase() + a.slice(1)] = new b(this)
            }, this)), a.each(e.Workers, a.proxy(function (b, c) {
                this._pipe.push({filter: c.filter, run: a.proxy(c.run, this)})
            }, this)), this.setup(), this.initialize()
        }

        e.Defaults = {
            items: 3,
            loop: !1,
            center: !1,
            rewind: !1,
            mouseDrag: !0,
            touchDrag: !0,
            pullDrag: !0,
            freeDrag: !1,
            margin: 0,
            stagePadding: 0,
            merge: !1,
            mergeFit: !0,
            autoWidth: !1,
            startPosition: 0,
            rtl: !1,
            smartSpeed: 250,
            fluidSpeed: !1,
            dragEndSpeed: !1,
            responsive: {},
            responsiveRefreshRate: 200,
            responsiveBaseElement: b,
            fallbackEasing: "swing",
            info: !1,
            nestedItemSelector: !1,
            itemElement: "div",
            stageElement: "div",
            refreshClass: "sbsw-owl-refresh",
            loadedClass: "sbsw-owl-loaded",
            loadingClass: "sbsw-owl-loading",
            rtlClass: "sbsw-owl-rtl",
            responsiveClass: "sbsw-owl-responsive",
            dragClass: "sbsw-owl-drag",
            itemClass: "sbsw-owl-item",
            stageClass: "sbsw-owl-stage",
            stageOuterClass: "sbsw-owl-stage-outer",
            grabClass: "sbsw-owl-grab"
        }, e.Width = {Default: "default", Inner: "inner", Outer: "outer"}, e.Type = {
            Event: "event",
            State: "state"
        }, e.Plugins = {}, e.Workers = [{
            filter: ["width", "settings"], run: function () {
                this._width = this.$element.width()
            }
        }, {
            filter: ["width", "items", "settings"], run: function (a) {
                a.current = this._items && this._items[this.relative(this._current)]
            }
        }, {
            filter: ["items", "settings"], run: function () {
                this.$stage.children(".cloned").remove()
            }
        }, {
            filter: ["width", "items", "settings"], run: function (a) {
                var b = this.settings.margin || "", c = !this.settings.autoWidth, d = this.settings.rtl,
                    e = {width: "auto", "margin-left": d ? b : "", "margin-right": d ? "" : b};
                !c && this.$stage.children().css(e), a.css = e
            }
        }, {
            filter: ["width", "items", "settings"], run: function (a) {
                var b = (this.width() / this.settings.items).toFixed(3) - this.settings.margin, c = null,
                    d = this._items.length, e = !this.settings.autoWidth, f = [];
                for (a.items = {
                    merge: !1,
                    width: b
                }; d--;) c = this._mergers[d], c = this.settings.mergeFit && Math.min(c, this.settings.items) || c, a.items.merge = c > 1 || a.items.merge, f[d] = e ? b * c : this._items[d].width();
                this._widths = f
            }
        }, {
            filter: ["items", "settings"], run: function () {
                var b = [], c = this._items, d = this.settings, e = Math.max(2 * d.items, 4),
                    f = 2 * Math.ceil(c.length / 2), g = d.loop && c.length ? d.rewind ? e : Math.max(e, f) : 0, h = "",
                    i = "";
                for (g /= 2; g--;) b.push(this.normalize(b.length / 2, !0)), h += c[b[b.length - 1]][0].outerHTML, b.push(this.normalize(c.length - 1 - (b.length - 1) / 2, !0)), i = c[b[b.length - 1]][0].outerHTML + i;
                this._clones = b, a(h).addClass("cloned").appendTo(this.$stage), a(i).addClass("cloned").prependTo(this.$stage)
            }
        }, {
            filter: ["width", "items", "settings"], run: function () {
                for (var a = this.settings.rtl ? 1 : -1, b = this._clones.length + this._items.length, c = -1, d = 0, e = 0, f = []; ++c < b;) d = f[c - 1] || 0, e = this._widths[this.relative(c)] + this.settings.margin, f.push(d + e * a);
                this._coordinates = f
            }
        }, {
            filter: ["width", "items", "settings"], run: function () {
                var a = this.settings.stagePadding, b = this._coordinates, c = {
                    width: Math.ceil(Math.abs(b[b.length - 1])) + 2 * a,
                    "padding-left": a || "",
                    "padding-right": a || ""
                };
                this.$stage.css(c)
            }
        }, {
            filter: ["width", "items", "settings"], run: function (a) {
                var b = this._coordinates.length, c = !this.settings.autoWidth, d = this.$stage.children();
                if (c && a.items.merge) for (; b--;) a.css.width = this._widths[this.relative(b)], d.eq(b).css(a.css); else c && (a.css.width = a.items.width, d.css(a.css))
            }
        }, {
            filter: ["items"], run: function () {
                this._coordinates.length < 1 && this.$stage.removeAttr("style")
            }
        }, {
            filter: ["width", "items", "settings"], run: function (a) {
                a.current = a.current ? this.$stage.children().index(a.current) : 0, a.current = Math.max(this.minimum(), Math.min(this.maximum(), a.current)), this.reset(a.current)
            }
        }, {
            filter: ["position"], run: function () {
                this.animate(this.coordinates(this._current))
            }
        }, {
            filter: ["width", "position", "items", "settings"], run: function () {
                var a, b, c, d, e = this.settings.rtl ? 1 : -1, f = 2 * this.settings.stagePadding,
                    g = this.coordinates(this.current()) + f, h = g + this.width() * e, i = [];
                for (c = 0, d = this._coordinates.length; c < d; c++) a = this._coordinates[c - 1] || 0, b = Math.abs(this._coordinates[c]) + f * e, (this.op(a, "<=", g) && this.op(a, ">", h) || this.op(b, "<", g) && this.op(b, ">", h)) && i.push(c);
                this.$stage.children(".active").removeClass("active"), this.$stage.children(":eq(" + i.join("), :eq(") + ")").addClass("active"), this.settings.center && (this.$stage.children(".center").removeClass("center"), this.$stage.children().eq(this.current()).addClass("center"))
            }
        }], e.prototype.initialize = function () {
            if (this.enter("initializing"), this.trigger("initialize"), this.$element.toggleClass(this.settings.rtlClass, this.settings.rtl), this.settings.autoWidth && !this.is("pre-loading")) {
                var b, c, e;
                b = this.$element.find("img"), c = this.settings.nestedItemSelector ? "." + this.settings.nestedItemSelector : d, e = this.$element.children(c).width(), b.length && e <= 0 && this.preloadAutoWidthImages(b)
            }
            this.$element.addClass(this.options.loadingClass), this.$stage = a("<" + this.settings.stageElement + ' class="' + this.settings.stageClass + '"/>').wrap('<div class="' + this.settings.stageOuterClass + '"/>'), this.$element.append(this.$stage.parent()), this.replace(this.$element.children().not(this.$stage.parent())), this.$element.is(":visible") ? this.refresh() : this.invalidate("width"), this.$element.removeClass(this.options.loadingClass).addClass(this.options.loadedClass), this.registerEventHandlers(), this.leave("initializing"), this.trigger("initialized")
        }, e.prototype.setup = function () {
            var b = this.viewport(), c = this.options.responsive, d = -1, e = null;
            c ? (a.each(c, function (a) {
                a <= b && a > d && (d = Number(a))
            }), e = a.extend({}, this.options, c[d]), "function" == typeof e.stagePadding && (e.stagePadding = e.stagePadding()), delete e.responsive, e.responsiveClass && this.$element.attr("class", this.$element.attr("class").replace(new RegExp("(" + this.options.responsiveClass + "-)\\S+\\s", "g"), "$1" + d))) : e = a.extend({}, this.options), this.trigger("change", {
                property: {
                    name: "settings",
                    value: e
                }
            }), this._breakpoint = d, this.settings = e, this.invalidate("settings"), this.trigger("changed", {
                property: {
                    name: "settings",
                    value: this.settings
                }
            })
        }, e.prototype.optionsLogic = function () {
            this.settings.autoWidth && (this.settings.stagePadding = !1, this.settings.merge = !1)
        }, e.prototype.prepare = function (b) {
            var c = this.trigger("prepare", {content: b});
            return c.data || (c.data = a("<" + this.settings.itemElement + "/>").addClass(this.options.itemClass).append(b)), this.trigger("prepared", {content: c.data}), c.data
        }, e.prototype.update = function () {
            for (var b = 0, c = this._pipe.length, d = a.proxy(function (a) {
                return this[a]
            }, this._invalidated), e = {}; b < c;) (this._invalidated.all || a.grep(this._pipe[b].filter, d).length > 0) && this._pipe[b].run(e), b++;
            this._invalidated = {}, !this.is("valid") && this.enter("valid")
        }, e.prototype.width = function (a) {
            switch (a = a || e.Width.Default) {
                case e.Width.Inner:
                case e.Width.Outer:
                    return this._width;
                default:
                    return this._width - 2 * this.settings.stagePadding + this.settings.margin
            }
        }, e.prototype.refresh = function () {
            this.enter("refreshing"), this.trigger("refresh"), this.setup(), this.optionsLogic(), this.$element.addClass(this.options.refreshClass), this.update(), this.$element.removeClass(this.options.refreshClass), this.leave("refreshing"), this.trigger("refreshed")
        }, e.prototype.onThrottledResize = function () {
            b.clearTimeout(this.resizeTimer), this.resizeTimer = b.setTimeout(this._handlers.onResize, this.settings.responsiveRefreshRate)
        }, e.prototype.onResize = function () {
            return !!this._items.length && (this._width !== this.$element.width() && (!!this.$element.is(":visible") && (this.enter("resizing"), this.trigger("resize").isDefaultPrevented() ? (this.leave("resizing"), !1) : (this.invalidate("width"), this.refresh(), this.leave("resizing"), void this.trigger("resized")))))
        }, e.prototype.registerEventHandlers = function () {
            a.support.transition && this.$stage.on(a.support.transition.end + ".owl.core", a.proxy(this.onTransitionEnd, this)), this.settings.responsive !== !1 && this.on(b, "resize", this._handlers.onThrottledResize), this.settings.mouseDrag && (this.$element.addClass(this.options.dragClass), this.$stage.on("mousedown.owl.core", a.proxy(this.onDragStart, this)), this.$stage.on("dragstart.owl.core selectstart.owl.core", function () {
                return !1
            })), this.settings.touchDrag && (this.$stage.on("touchstart.owl.core", a.proxy(this.onDragStart, this)), this.$stage.on("touchcancel.owl.core", a.proxy(this.onDragEnd, this)))
        }, e.prototype.onDragStart = function (b) {
            var d = null;
            3 !== b.which && (a.support.transform ? (d = this.$stage.css("transform").replace(/.*\(|\)| /g, "").split(","), d = {
                x: d[16 === d.length ? 12 : 4],
                y: d[16 === d.length ? 13 : 5]
            }) : (d = this.$stage.position(), d = {
                x: this.settings.rtl ? d.left + this.$stage.width() - this.width() + this.settings.margin : d.left,
                y: d.top
            }), this.is("animating") && (a.support.transform ? this.animate(d.x) : this.$stage.stop(), this.invalidate("position")), this.$element.toggleClass(this.options.grabClass, "mousedown" === b.type), this.speed(0), this._drag.time = (new Date).getTime(), this._drag.target = a(b.target), this._drag.stage.start = d, this._drag.stage.current = d, this._drag.pointer = this.pointer(b), a(c).on("mouseup.owl.core touchend.owl.core", a.proxy(this.onDragEnd, this)), a(c).one("mousemove.owl.core touchmove.owl.core", a.proxy(function (b) {
                var d = this.difference(this._drag.pointer, this.pointer(b));
                a(c).on("mousemove.owl.core touchmove.owl.core", a.proxy(this.onDragMove, this)), Math.abs(d.x) < Math.abs(d.y) && this.is("valid") || (b.preventDefault(), this.enter("dragging"), this.trigger("drag"))
            }, this)))
        }, e.prototype.onDragMove = function (a) {
            var b = null, c = null, d = null, e = this.difference(this._drag.pointer, this.pointer(a)),
                f = this.difference(this._drag.stage.start, e);
            this.is("dragging") && (a.preventDefault(), this.settings.loop ? (b = this.coordinates(this.minimum()), c = this.coordinates(this.maximum() + 1) - b, f.x = ((f.x - b) % c + c) % c + b) : (b = this.settings.rtl ? this.coordinates(this.maximum()) : this.coordinates(this.minimum()), c = this.settings.rtl ? this.coordinates(this.minimum()) : this.coordinates(this.maximum()), d = this.settings.pullDrag ? -1 * e.x / 5 : 0, f.x = Math.max(Math.min(f.x, b + d), c + d)), this._drag.stage.current = f, this.animate(f.x))
        }, e.prototype.onDragEnd = function (b) {
            var d = this.difference(this._drag.pointer, this.pointer(b)), e = this._drag.stage.current,
                f = d.x > 0 ^ this.settings.rtl ? "left" : "right";
            a(c).off(".owl.core"), this.$element.removeClass(this.options.grabClass), (0 !== d.x && this.is("dragging") || !this.is("valid")) && (this.speed(this.settings.dragEndSpeed || this.settings.smartSpeed), this.current(this.closest(e.x, 0 !== d.x ? f : this._drag.direction)), this.invalidate("position"), this.update(), this._drag.direction = f, (Math.abs(d.x) > 3 || (new Date).getTime() - this._drag.time > 300) && this._drag.target.one("click.owl.core", function () {
                return !1
            })), this.is("dragging") && (this.leave("dragging"), this.trigger("dragged"))
        }, e.prototype.closest = function (b, c) {
            var d = -1, e = 30, f = this.width(), g = this.coordinates();
            return this.settings.freeDrag || a.each(g, a.proxy(function (a, h) {
                return "left" === c && b > h - e && b < h + e ? d = a : "right" === c && b > h - f - e && b < h - f + e ? d = a + 1 : this.op(b, "<", h) && this.op(b, ">", g[a + 1] || h - f) && (d = "left" === c ? a + 1 : a), d === -1
            }, this)), this.settings.loop || (this.op(b, ">", g[this.minimum()]) ? d = b = this.minimum() : this.op(b, "<", g[this.maximum()]) && (d = b = this.maximum())), d
        }, e.prototype.animate = function (b) {
            var c = this.speed() > 0;
            this.is("animating") && this.onTransitionEnd(), c && (this.enter("animating"), this.trigger("translate")), a.support.transform3d && a.support.transition ? this.$stage.css({
                transform: "translate3d(" + b + "px,0px,0px)",
                transition: this.speed() / 1e3 + "s"
            }) : c ? this.$stage.animate({left: b + "px"}, this.speed(), this.settings.fallbackEasing, a.proxy(this.onTransitionEnd, this)) : this.$stage.css({left: b + "px"})
        }, e.prototype.is = function (a) {
            return this._states.current[a] && this._states.current[a] > 0
        }, e.prototype.current = function (a) {
            if (a === d) return this._current;
            if (0 === this._items.length) return d;
            if (a = this.normalize(a), this._current !== a) {
                var b = this.trigger("change", {property: {name: "position", value: a}});
                b.data !== d && (a = this.normalize(b.data)), this._current = a, this.invalidate("position"), this.trigger("changed", {
                    property: {
                        name: "position",
                        value: this._current
                    }
                })
            }
            return this._current
        }, e.prototype.invalidate = function (b) {
            return "string" === a.type(b) && (this._invalidated[b] = !0, this.is("valid") && this.leave("valid")), a.map(this._invalidated, function (a, b) {
                return b
            })
        }, e.prototype.reset = function (a) {
            a = this.normalize(a), a !== d && (this._speed = 0, this._current = a, this.suppress(["translate", "translated"]), this.animate(this.coordinates(a)), this.release(["translate", "translated"]))
        }, e.prototype.normalize = function (a, b) {
            var c = this._items.length, e = b ? 0 : this._clones.length;
            return !this.isNumeric(a) || c < 1 ? a = d : (a < 0 || a >= c + e) && (a = ((a - e / 2) % c + c) % c + e / 2), a
        }, e.prototype.relative = function (a) {
            return a -= this._clones.length / 2, this.normalize(a, !0)
        }, e.prototype.maximum = function (a) {
            var b, c, d, e = this.settings, f = this._coordinates.length;
            if (e.loop) f = this._clones.length / 2 + this._items.length - 1; else if (e.autoWidth || e.merge) {
                for (b = this._items.length, c = this._items[--b].width(), d = this.$element.width(); b-- && (c += this._items[b].width() + this.settings.margin, !(c > d));) ;
                f = b + 1
            } else f = e.center ? this._items.length - 1 : this._items.length - e.items;
            return a && (f -= this._clones.length / 2), Math.max(f, 0)
        }, e.prototype.minimum = function (a) {
            return a ? 0 : this._clones.length / 2
        }, e.prototype.items = function (a) {
            return a === d ? this._items.slice() : (a = this.normalize(a, !0), this._items[a])
        }, e.prototype.mergers = function (a) {
            return a === d ? this._mergers.slice() : (a = this.normalize(a, !0), this._mergers[a])
        }, e.prototype.clones = function (b) {
            var c = this._clones.length / 2, e = c + this._items.length, f = function (a) {
                return a % 2 === 0 ? e + a / 2 : c - (a + 1) / 2
            };
            return b === d ? a.map(this._clones, function (a, b) {
                return f(b)
            }) : a.map(this._clones, function (a, c) {
                return a === b ? f(c) : null
            })
        }, e.prototype.speed = function (a) {
            return a !== d && (this._speed = a), this._speed
        }, e.prototype.coordinates = function (b) {
            var c, e = 1, f = b - 1;
            return b === d ? a.map(this._coordinates, a.proxy(function (a, b) {
                return this.coordinates(b)
            }, this)) : (this.settings.center ? (this.settings.rtl && (e = -1, f = b + 1), c = this._coordinates[b], c += (this.width() - c + (this._coordinates[f] || 0)) / 2 * e) : c = this._coordinates[f] || 0, c = Math.ceil(c))
        }, e.prototype.duration = function (a, b, c) {
            return 0 === c ? 0 : Math.min(Math.max(Math.abs(b - a), 1), 6) * Math.abs(c || this.settings.smartSpeed)
        }, e.prototype.to = function (a, b) {
            var c = this.current(), d = null, e = a - this.relative(c), f = (e > 0) - (e < 0), g = this._items.length,
                h = this.minimum(), i = this.maximum();
            this.settings.loop ? (!this.settings.rewind && Math.abs(e) > g / 2 && (e += f * -1 * g), a = c + e, d = ((a - h) % g + g) % g + h, d !== a && d - e <= i && d - e > 0 && (c = d - e, a = d, this.reset(c))) : this.settings.rewind ? (i += 1, a = (a % i + i) % i) : a = Math.max(h, Math.min(i, a)), this.speed(this.duration(c, a, b)), this.current(a), this.$element.is(":visible") && this.update()
        }, e.prototype.next = function (a) {
            a = a || !1, this.to(this.relative(this.current()) + 1, a)
        }, e.prototype.prev = function (a) {
            a = a || !1, this.to(this.relative(this.current()) - 1, a)
        }, e.prototype.onTransitionEnd = function (a) {
            if (a !== d && (a.stopPropagation(), (a.target || a.srcElement || a.originalTarget) !== this.$stage.get(0))) return !1;
            this.leave("animating"), this.trigger("translated")
        }, e.prototype.viewport = function () {
            var d;
            return this.options.responsiveBaseElement !== b ? d = a(this.options.responsiveBaseElement).width() : b.innerWidth ? d = b.innerWidth : c.documentElement && c.documentElement.clientWidth ? d = c.documentElement.clientWidth : console.warn("Can not detect viewport width."), d
        }, e.prototype.replace = function (b) {
            this.$stage.empty(), this._items = [], b && (b = b instanceof jQuery ? b : a(b)), this.settings.nestedItemSelector && (b = b.find("." + this.settings.nestedItemSelector)), b.filter(function () {
                return 1 === this.nodeType
            }).each(a.proxy(function (a, b) {
                b = this.prepare(b), this.$stage.append(b), this._items.push(b), this._mergers.push(1 * b.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1)
            }, this)), this.reset(this.isNumeric(this.settings.startPosition) ? this.settings.startPosition : 0), this.invalidate("items")
        }, e.prototype.add = function (b, c) {
            var e = this.relative(this._current);
            c = c === d ? this._items.length : this.normalize(c, !0), b = b instanceof jQuery ? b : a(b), this.trigger("add", {
                content: b,
                position: c
            }), b = this.prepare(b), 0 === this._items.length || c === this._items.length ? (0 === this._items.length && this.$stage.append(b), 0 !== this._items.length && this._items[c - 1].after(b), this._items.push(b), this._mergers.push(1 * b.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1)) : (this._items[c].before(b), this._items.splice(c, 0, b), this._mergers.splice(c, 0, 1 * b.find("[data-merge]").addBack("[data-merge]").attr("data-merge") || 1)), this._items[e] && this.reset(this._items[e].index()), this.invalidate("items"), this.trigger("added", {
                content: b,
                position: c
            })
        }, e.prototype.remove = function (a) {
            a = this.normalize(a, !0), a !== d && (this.trigger("remove", {
                content: this._items[a],
                position: a
            }), this._items[a].remove(), this._items.splice(a, 1), this._mergers.splice(a, 1), this.invalidate("items"), this.trigger("removed", {
                content: null,
                position: a
            }))
        }, e.prototype.preloadAutoWidthImages = function (b) {
            b.each(a.proxy(function (b, c) {
                this.enter("pre-loading"), c = a(c), a(new Image).one("load", a.proxy(function (a) {
                    c.attr("src", a.target.src), c.css("opacity", 1), this.leave("pre-loading"), !this.is("pre-loading") && !this.is("initializing") && this.refresh()
                }, this)).attr("src", c.attr("src") || c.attr("data-src") || c.attr("data-src-retina"))
            }, this))
        }, e.prototype.destroy = function () {
            this.$element.off(".owl.core"), this.$stage.off(".owl.core"), a(c).off(".owl.core"), this.settings.responsive !== !1 && (b.clearTimeout(this.resizeTimer), this.off(b, "resize", this._handlers.onThrottledResize));
            for (var d in this._plugins) this._plugins[d].destroy();
            this.$stage.children(".cloned").remove(), this.$stage.unwrap(), this.$stage.children().contents().unwrap(), this.$stage.children().unwrap(), this.$element.removeClass(this.options.refreshClass).removeClass(this.options.loadingClass).removeClass(this.options.loadedClass).removeClass(this.options.rtlClass).removeClass(this.options.dragClass).removeClass(this.options.grabClass).attr("class", this.$element.attr("class").replace(new RegExp(this.options.responsiveClass + "-\\S+\\s", "g"), "")).removeData("owl.carousel")
        }, e.prototype.op = function (a, b, c) {
            var d = this.settings.rtl;
            switch (b) {
                case"<":
                    return d ? a > c : a < c;
                case">":
                    return d ? a < c : a > c;
                case">=":
                    return d ? a <= c : a >= c;
                case"<=":
                    return d ? a >= c : a <= c
            }
        }, e.prototype.on = function (a, b, c, d) {
            a.addEventListener ? a.addEventListener(b, c, d) : a.attachEvent && a.attachEvent("on" + b, c)
        }, e.prototype.off = function (a, b, c, d) {
            a.removeEventListener ? a.removeEventListener(b, c, d) : a.detachEvent && a.detachEvent("on" + b, c)
        }, e.prototype.trigger = function (b, c, d, f, g) {
            var h = {item: {count: this._items.length, index: this.current()}},
                i = a.camelCase(a.grep(["on", b, d], function (a) {
                    return a
                }).join("-").toLowerCase()),
                j = a.Event([b, "owl", d || "carousel"].join(".").toLowerCase(), a.extend({relatedTarget: this}, h, c));
            return this._supress[b] || (a.each(this._plugins, function (a, b) {
                b.onTrigger && b.onTrigger(j)
            }), this.register({
                type: e.Type.Event,
                name: b
            }), this.$element.trigger(j), this.settings && "function" == typeof this.settings[i] && this.settings[i].call(this, j)), j
        }, e.prototype.enter = function (b) {
            a.each([b].concat(this._states.tags[b] || []), a.proxy(function (a, b) {
                this._states.current[b] === d && (this._states.current[b] = 0), this._states.current[b]++
            }, this))
        }, e.prototype.leave = function (b) {
            a.each([b].concat(this._states.tags[b] || []), a.proxy(function (a, b) {
                this._states.current[b]--
            }, this))
        }, e.prototype.register = function (b) {
            if (b.type === e.Type.Event) {
                if (a.event.special[b.name] || (a.event.special[b.name] = {}), !a.event.special[b.name].owl) {
                    var c = a.event.special[b.name]._default;
                    a.event.special[b.name]._default = function (a) {
                        return !c || !c.apply || a.namespace && a.namespace.indexOf("owl") !== -1 ? a.namespace && a.namespace.indexOf("owl") > -1 : c.apply(this, arguments)
                    }, a.event.special[b.name].owl = !0
                }
            } else b.type === e.Type.State && (this._states.tags[b.name] ? this._states.tags[b.name] = this._states.tags[b.name].concat(b.tags) : this._states.tags[b.name] = b.tags, this._states.tags[b.name] = a.grep(this._states.tags[b.name], a.proxy(function (c, d) {
                return a.inArray(c, this._states.tags[b.name]) === d
            }, this)))
        }, e.prototype.suppress = function (b) {
            a.each(b, a.proxy(function (a, b) {
                this._supress[b] = !0
            }, this))
        }, e.prototype.release = function (b) {
            a.each(b, a.proxy(function (a, b) {
                delete this._supress[b]
            }, this))
        }, e.prototype.pointer = function (a) {
            var c = {x: null, y: null};
            return a = a.originalEvent || a || b.event, a = a.touches && a.touches.length ? a.touches[0] : a.changedTouches && a.changedTouches.length ? a.changedTouches[0] : a, a.pageX ? (c.x = a.pageX, c.y = a.pageY) : (c.x = a.clientX, c.y = a.clientY), c
        }, e.prototype.isNumeric = function (a) {
            return !isNaN(parseFloat(a))
        }, e.prototype.difference = function (a, b) {
            return {x: a.x - b.x, y: a.y - b.y}
        }, a.fn.sbswOwlCarousel = function (b) {
            var c = Array.prototype.slice.call(arguments, 1);
            return this.each(function () {
                var d = a(this), f = d.data("owl.carousel");
                f || (f = new e(this, "object" == typeof b && b), d.data("owl.carousel", f), a.each(["next", "prev", "to", "destroy", "refresh", "replace", "add", "remove"], function (b, c) {
                    f.register({
                        type: e.Type.Event,
                        name: c
                    }), f.$element.on(c + ".owl.carousel.core", a.proxy(function (a) {
                        a.namespace && a.relatedTarget !== this && (this.suppress([c]), f[c].apply(this, [].slice.call(arguments, 1)), this.release([c]))
                    }, f))
                })), "string" == typeof b && "_" !== b.charAt(0) && f[b].apply(f, c)
            })
        }, a.fn.sbswOwlCarousel.Constructor = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this._core = b, this._interval = null, this._visible = null, this._handlers = {
                "initialized.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.autoRefresh && this.watch()
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers)
        };
        e.Defaults = {autoRefresh: !0, autoRefreshInterval: 500}, e.prototype.watch = function () {
            this._interval || (this._visible = this._core.$element.is(":visible"), this._interval = b.setInterval(a.proxy(this.refresh, this), this._core.settings.autoRefreshInterval))
        }, e.prototype.refresh = function () {
            this._core.$element.is(":visible") !== this._visible && (this._visible = !this._visible, this._core.$element.toggleClass("sbsw-owl-hidden", !this._visible), this._visible && this._core.invalidate("width") && this._core.refresh())
        }, e.prototype.destroy = function () {
            var a, c;
            b.clearInterval(this._interval);
            for (a in this._handlers) this._core.$element.off(a, this._handlers[a]);
            for (c in Object.getOwnPropertyNames(this)) "function" != typeof this[c] && (this[c] = null)
        }, a.fn.sbswOwlCarousel.Constructor.Plugins.AutoRefresh = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this._core = b, this._loaded = [], this._handlers = {
                "initialized.owl.carousel change.owl.carousel resized.owl.carousel": a.proxy(function (b) {
                    if (b.namespace && this._core.settings && this._core.settings.lazyLoad && (b.property && "position" == b.property.name || "initialized" == b.type)) for (var c = this._core.settings, e = c.center && Math.ceil(c.items / 2) || c.items, f = c.center && e * -1 || 0, g = (b.property && b.property.value !== d ? b.property.value : this._core.current()) + f, h = this._core.clones().length, i = a.proxy(function (a, b) {
                        this.load(b)
                    }, this); f++ < e;) this.load(h / 2 + this._core.relative(g)), h && a.each(this._core.clones(this._core.relative(g)), i), g++
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers)
        };
        e.Defaults = {lazyLoad: !1}, e.prototype.load = function (c) {
            var d = this._core.$stage.children().eq(c), e = d && d.find(".sbsw-owl-lazy");
            !e || a.inArray(d.get(0), this._loaded) > -1 || (e.each(a.proxy(function (c, d) {
                var e, f = a(d), g = b.devicePixelRatio > 1 && f.attr("data-src-retina") || f.attr("data-src");
                this._core.trigger("load", {
                    element: f,
                    url: g
                }, "lazy"), f.is("img") ? f.one("load.owl.lazy", a.proxy(function () {
                    f.css("opacity", 1), this._core.trigger("loaded", {element: f, url: g}, "lazy")
                }, this)).attr("src", g) : (e = new Image, e.onload = a.proxy(function () {
                    f.css({
                        "background-image": 'url("' + g + '")',
                        opacity: "1"
                    }), this._core.trigger("loaded", {element: f, url: g}, "lazy")
                }, this), e.src = g)
            }, this)), this._loaded.push(d.get(0)))
        }, e.prototype.destroy = function () {
            var a, b;
            for (a in this.handlers) this._core.$element.off(a, this.handlers[a]);
            for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
        }, a.fn.sbswOwlCarousel.Constructor.Plugins.Lazy = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this._core = b, this._handlers = {
                "initialized.owl.carousel refreshed.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.autoHeight && this.update()
                }, this), "changed.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.autoHeight && "position" == a.property.name && this.update()
                }, this), "loaded.owl.lazy": a.proxy(function (a) {
                    a.namespace && this._core.settings.autoHeight && a.element.closest("." + this._core.settings.itemClass).index() === this._core.current() && this.update()
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers)
        };
        e.Defaults = {autoHeight: !1, autoHeightClass: "sbsw-owl-height"}, e.prototype.update = function () {
            var b = this._core._current, c = b + this._core.settings.items,
                d = this._core.$stage.children().toArray().slice(b, c), e = [], f = 0;
            a.each(d, function (b, c) {
                e.push(a(c).height())
            }), f = Math.max.apply(null, e), this._core.$stage.parent().height(f).addClass(this._core.settings.autoHeightClass)
        }, e.prototype.destroy = function () {
            var a, b;
            for (a in this._handlers) this._core.$element.off(a, this._handlers[a]);
            for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
        }, a.fn.sbswOwlCarousel.Constructor.Plugins.AutoHeight = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this._core = b, this._videos = {}, this._playing = null, this._handlers = {
                "initialized.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.register({type: "state", name: "playing", tags: ["interacting"]})
                }, this), "resize.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.video && this.isInFullScreen() && a.preventDefault()
                }, this), "refreshed.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.is("resizing") && this._core.$stage.find(".cloned .sbsw-owl-video-frame").remove()
                }, this), "changed.owl.carousel": a.proxy(function (a) {
                    a.namespace && "position" === a.property.name && this._playing && this.stop()
                }, this), "prepared.owl.carousel": a.proxy(function (b) {
                    if (b.namespace) {
                        var c = a(b.content).find(".sbsw-owl-video");
                        c.length && (c.css("display", "none"), this.fetch(c, a(b.content)))
                    }
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this._core.$element.on(this._handlers), this._core.$element.on("click.owl.video", ".sbsw-owl-video-play-icon", a.proxy(function (a) {
                this.play(a)
            }, this))
        };
        e.Defaults = {video: !1, videoHeight: !1, videoWidth: !1}, e.prototype.fetch = function (a, b) {
            var c = function () {
                    return a.attr("data-vimeo-id") ? "vimeo" : a.attr("data-vzaar-id") ? "vzaar" : "youtube"
                }(), d = a.attr("data-vimeo-id") || a.attr("data-youtube-id") || a.attr("data-vzaar-id"),
                e = a.attr("data-width") || this._core.settings.videoWidth,
                f = a.attr("data-height") || this._core.settings.videoHeight, g = a.attr("href");
            if (!g) throw new Error("Missing video URL.");
            if (d = g.match(/(http:|https:|)\/\/(player.|www.|app.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com)|vzaar\.com)\/(video\/|videos\/|embed\/|channels\/.+\/|groups\/.+\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/), d[3].indexOf("youtu") > -1) c = "youtube"; else if (d[3].indexOf("vimeo") > -1) c = "vimeo"; else {
                if (!(d[3].indexOf("vzaar") > -1)) throw new Error("Video URL not supported.");
                c = "vzaar"
            }
            d = d[6], this._videos[g] = {
                type: c,
                id: d,
                width: e,
                height: f
            }, b.attr("data-video", g), this.thumbnail(a, this._videos[g])
        }, e.prototype.thumbnail = function (b, c) {
            var d, e, f, g = c.width && c.height ? 'style="width:' + c.width + "px;height:" + c.height + 'px;"' : "",
                h = b.find("img"), i = "src", j = "", k = this._core.settings, l = function (a) {
                    e = '<div class="sbsw-owl-video-play-icon"></div>', d = k.lazyLoad ? '<div class="sbsw-owl-video-tn ' + j + '" ' + i + '="' + a + '"></div>' : '<div class="sbsw-owl-video-tn" style="opacity:1;background-image:url(' + a + ')"></div>', b.after(d), b.after(e)
                };
            if (b.wrap('<div class="sbsw-owl-video-wrapper"' + g + "></div>"), this._core.settings.lazyLoad && (i = "data-src", j = "sbsw-owl-lazy"), h.length) return l(h.attr(i)), h.remove(), !1;
            "youtube" === c.type ? (f = "//img.youtube.com/vi/" + c.id + "/hqdefault.jpg", l(f)) : "vimeo" === c.type ? a.ajax({
                type: "GET",
                url: "//vimeo.com/api/v2/video/" + c.id + ".json",
                jsonp: "callback",
                dataType: "jsonp",
                success: function (a) {
                    f = a[0].thumbnail_large, l(f)
                }
            }) : "vzaar" === c.type && a.ajax({
                type: "GET",
                url: "//vzaar.com/api/videos/" + c.id + ".json",
                jsonp: "callback",
                dataType: "jsonp",
                success: function (a) {
                    f = a.framegrab_url, l(f)
                }
            })
        }, e.prototype.stop = function () {
            this._core.trigger("stop", null, "video"), this._playing.find(".sbsw-owl-video-frame").remove(), this._playing.removeClass("sbsw-owl-video-playing"), this._playing = null, this._core.leave("playing"), this._core.trigger("stopped", null, "video")
        }, e.prototype.play = function (b) {
            var c, d = a(b.target), e = d.closest("." + this._core.settings.itemClass),
                f = this._videos[e.attr("data-video")], g = f.width || "100%",
                h = f.height || this._core.$stage.height();
            this._playing || (this._core.enter("playing"), this._core.trigger("play", null, "video"), e = this._core.items(this._core.relative(e.index())), this._core.reset(e.index()), "youtube" === f.type ? c = '<iframe width="' + g + '" height="' + h + '" src="//www.youtube.com/embed/' + f.id + "?autoplay=1&rel=0&v=" + f.id + '" frameborder="0" allowfullscreen></iframe>' : "vimeo" === f.type ? c = '<iframe src="//player.vimeo.com/video/' + f.id + '?autoplay=1" width="' + g + '" height="' + h + '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>' : "vzaar" === f.type && (c = '<iframe frameborder="0"height="' + h + '"width="' + g + '" allowfullscreen mozallowfullscreen webkitAllowFullScreen src="//view.vzaar.com/' + f.id + '/player?autoplay=true"></iframe>'), a('<div class="sbsw-owl-video-frame">' + c + "</div>").insertAfter(e.find(".sbsw-owl-video")), this._playing = e.addClass("sbsw-owl-video-playing"))
        }, e.prototype.isInFullScreen = function () {
            var b = c.fullscreenElement || c.mozFullScreenElement || c.webkitFullscreenElement;
            return b && a(b).parent().hasClass("sbsw-owl-video-frame")
        }, e.prototype.destroy = function () {
            var a, b;
            this._core.$element.off("click.owl.video");
            for (a in this._handlers) this._core.$element.off(a, this._handlers[a]);
            for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
        }, a.fn.sbswOwlCarousel.Constructor.Plugins.Video = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this.core = b, this.core.options = a.extend({}, e.Defaults, this.core.options), this.swapping = !0, this.previous = d, this.next = d, this.handlers = {
                "change.owl.carousel": a.proxy(function (a) {
                    a.namespace && "position" == a.property.name && (this.previous = this.core.current(), this.next = a.property.value)
                }, this), "drag.owl.carousel dragged.owl.carousel translated.owl.carousel": a.proxy(function (a) {
                    a.namespace && (this.swapping = "translated" == a.type)
                }, this), "translate.owl.carousel": a.proxy(function (a) {
                    a.namespace && this.swapping && (this.core.options.animateOut || this.core.options.animateIn) && this.swap()
                }, this)
            }, this.core.$element.on(this.handlers)
        };
        e.Defaults = {animateOut: !1, animateIn: !1}, e.prototype.swap = function () {
            if (1 === this.core.settings.items && a.support.animation && a.support.transition) {
                this.core.speed(0);
                var b, c = a.proxy(this.clear, this), d = this.core.$stage.children().eq(this.previous),
                    e = this.core.$stage.children().eq(this.next), f = this.core.settings.animateIn,
                    g = this.core.settings.animateOut;
                this.core.current() !== this.previous && (g && (b = this.core.coordinates(this.previous) - this.core.coordinates(this.next), d.one(a.support.animation.end, c).css({left: b + "px"}).addClass("animated sbsw-owl-animated-out").addClass(g)), f && e.one(a.support.animation.end, c).addClass("animated sbsw-owl-animated-in").addClass(f))
            }
        }, e.prototype.clear = function (b) {
            a(b.target).css({left: ""}).removeClass("animated sbsw-owl-animated-out sbsw-owl-animated-in").removeClass(this.core.settings.animateIn).removeClass(this.core.settings.animateOut), this.core.onTransitionEnd()
        }, e.prototype.destroy = function () {
            var a, b;
            for (a in this.handlers) this.core.$element.off(a, this.handlers[a]);
            for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
        },
            a.fn.sbswOwlCarousel.Constructor.Plugins.Animate = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        var e = function (b) {
            this._core = b, this._timeout = null, this._paused = !1, this._handlers = {
                "changed.owl.carousel": a.proxy(function (a) {
                    a.namespace && "settings" === a.property.name ? this._core.settings.autoplay ? this.play() : this.stop() : a.namespace && "position" === a.property.name && this._core.settings.autoplay && this._setAutoPlayInterval()
                }, this), "initialized.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.autoplay && this.play()
                }, this), "play.owl.autoplay": a.proxy(function (a, b, c) {
                    a.namespace && this.play(b, c)
                }, this), "stop.owl.autoplay": a.proxy(function (a) {
                    a.namespace && this.stop()
                }, this), "mouseover.owl.autoplay": a.proxy(function () {
                    this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.pause()
                }, this), "mouseleave.owl.autoplay": a.proxy(function () {
                    this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.play()
                }, this), "touchstart.owl.core": a.proxy(function () {
                    this._core.settings.autoplayHoverPause && this._core.is("rotating") && this.pause()
                }, this), "touchend.owl.core": a.proxy(function () {
                    this._core.settings.autoplayHoverPause && this.play()
                }, this)
            }, this._core.$element.on(this._handlers), this._core.options = a.extend({}, e.Defaults, this._core.options)
        };
        e.Defaults = {
            autoplay: !1,
            autoplayTimeout: 5e3,
            autoplayHoverPause: !1,
            autoplaySpeed: !1
        }, e.prototype.play = function (a, b) {
            this._paused = !1, this._core.is("rotating") || (this._core.enter("rotating"), this._setAutoPlayInterval())
        }, e.prototype._getNextTimeout = function (d, e) {
            return this._timeout && b.clearTimeout(this._timeout), b.setTimeout(a.proxy(function () {
                this._paused || this._core.is("busy") || this._core.is("interacting") || c.hidden || this._core.next(e || this._core.settings.autoplaySpeed)
            }, this), d || this._core.settings.autoplayTimeout)
        }, e.prototype._setAutoPlayInterval = function () {
            this._timeout = this._getNextTimeout()
        }, e.prototype.stop = function () {
            this._core.is("rotating") && (b.clearTimeout(this._timeout), this._core.leave("rotating"))
        }, e.prototype.pause = function () {
            this._core.is("rotating") && (this._paused = !0)
        }, e.prototype.destroy = function () {
            var a, b;
            this.stop();
            for (a in this._handlers) this._core.$element.off(a, this._handlers[a]);
            for (b in Object.getOwnPropertyNames(this)) "function" != typeof this[b] && (this[b] = null)
        }, a.fn.sbswOwlCarousel.Constructor.Plugins.autoplay = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        "use strict";
        var e = function (b) {
            this._core = b, this._initialized = !1, this._pages = [], this._controls = {}, this._templates = [], this.$element = this._core.$element, this._overrides = {
                next: this._core.next,
                prev: this._core.prev,
                to: this._core.to
            }, this._handlers = {
                "prepared.owl.carousel": a.proxy(function (b) {
                    b.namespace && this._core.settings.dotsData && this._templates.push('<div class="' + this._core.settings.dotClass + '">' + a(b.content).find("[data-dot]").addBack("[data-dot]").attr("data-dot") + "</div>")
                }, this), "added.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.dotsData && this._templates.splice(a.position, 0, this._templates.pop())
                }, this), "remove.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._core.settings.dotsData && this._templates.splice(a.position, 1)
                }, this), "changed.owl.carousel": a.proxy(function (a) {
                    a.namespace && "position" == a.property.name && this.draw()
                }, this), "initialized.owl.carousel": a.proxy(function (a) {
                    a.namespace && !this._initialized && (this._core.trigger("initialize", null, "navigation"), this.initialize(), this.update(), this.draw(), this._initialized = !0, this._core.trigger("initialized", null, "navigation"))
                }, this), "refreshed.owl.carousel": a.proxy(function (a) {
                    a.namespace && this._initialized && (this._core.trigger("refresh", null, "navigation"), this.update(), this.draw(), this._core.trigger("refreshed", null, "navigation"))
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this.$element.on(this._handlers)
        };
        e.Defaults = {
            nav: !1,
            navText: ["prev", "next"],
            navSpeed: !1,
            navElement: "div",
            navContainer: !1,
            navContainerClass: "sbsw-owl-nav",
            navClass: ["sbsw-owl-prev", "sbsw-owl-next"],
            slideBy: 1,
            dotClass: "sbsw-owl-dot",
            dotsClass: "sbsw-owl-dots",
            dots: !0,
            dotsEach: !1,
            dotsData: !1,
            dotsSpeed: !1,
            dotsContainer: !1
        }, e.prototype.initialize = function () {
            var b, c = this._core.settings;
            this._controls.$relative = (c.navContainer ? a(c.navContainer) : a("<div>").addClass(c.navContainerClass).appendTo(this.$element)).addClass("disabled"), this._controls.$previous = a("<" + c.navElement + ">").addClass(c.navClass[0]).html(c.navText[0]).prependTo(this._controls.$relative).on("click", a.proxy(function (a) {
                this.prev(c.navSpeed)
            }, this)), this._controls.$next = a("<" + c.navElement + ">").addClass(c.navClass[1]).html(c.navText[1]).appendTo(this._controls.$relative).on("click", a.proxy(function (a) {
                this.next(c.navSpeed)
            }, this)), c.dotsData || (this._templates = [a("<div>").addClass(c.dotClass).append(a("<span>")).prop("outerHTML")]), this._controls.$absolute = (c.dotsContainer ? a(c.dotsContainer) : a("<div>").addClass(c.dotsClass).appendTo(this.$element)).addClass("disabled"), this._controls.$absolute.on("click", "div", a.proxy(function (b) {
                var d = a(b.target).parent().is(this._controls.$absolute) ? a(b.target).index() : a(b.target).parent().index();
                b.preventDefault(), this.to(d, c.dotsSpeed)
            }, this));
            for (b in this._overrides) this._core[b] = a.proxy(this[b], this)
        }, e.prototype.destroy = function () {
            var a, b, c, d;
            for (a in this._handlers) this.$element.off(a, this._handlers[a]);
            for (b in this._controls) this._controls[b].remove();
            for (d in this.overides) this._core[d] = this._overrides[d];
            for (c in Object.getOwnPropertyNames(this)) "function" != typeof this[c] && (this[c] = null)
        }, e.prototype.update = function () {
            var a, b, c, d = this._core.clones().length / 2, e = d + this._core.items().length,
                f = this._core.maximum(!0), g = this._core.settings,
                h = g.center || g.autoWidth || g.dotsData ? 1 : g.dotsEach || g.items;
            if ("page" !== g.slideBy && (g.slideBy = Math.min(g.slideBy, g.items)), g.dots || "page" == g.slideBy) for (this._pages = [], a = d, b = 0, c = 0; a < e; a++) {
                if (b >= h || 0 === b) {
                    if (this._pages.push({
                        start: Math.min(f, a - d),
                        end: a - d + h - 1
                    }), Math.min(f, a - d) === f) break;
                    b = 0, ++c
                }
                b += this._core.mergers(this._core.relative(a))
            }
        }, e.prototype.draw = function () {
            var b, c = this._core.settings, d = this._core.items().length <= c.items,
                e = this._core.relative(this._core.current()), f = c.loop || c.rewind;
            this._controls.$relative.toggleClass("disabled", !c.nav || d), c.nav && (this._controls.$previous.toggleClass("disabled", !f && e <= this._core.minimum(!0)), this._controls.$next.toggleClass("disabled", !f && e >= this._core.maximum(!0))), this._controls.$absolute.toggleClass("disabled", !c.dots || d), c.dots && (b = this._pages.length - this._controls.$absolute.children().length, c.dotsData && 0 !== b ? this._controls.$absolute.html(this._templates.join("")) : b > 0 ? this._controls.$absolute.append(new Array(b + 1).join(this._templates[0])) : b < 0 && this._controls.$absolute.children().slice(b).remove(), this._controls.$absolute.find(".active").removeClass("active"), this._controls.$absolute.children().eq(a.inArray(this.current(), this._pages)).addClass("active"))
        }, e.prototype.onTrigger = function (b) {
            var c = this._core.settings;
            b.page = {
                index: a.inArray(this.current(), this._pages),
                count: this._pages.length,
                size: c && (c.center || c.autoWidth || c.dotsData ? 1 : c.dotsEach || c.items)
            }
        }, e.prototype.current = function () {
            var b = this._core.relative(this._core.current());
            return a.grep(this._pages, a.proxy(function (a, c) {
                return a.start <= b && a.end >= b
            }, this)).pop()
        }, e.prototype.getPosition = function (b) {
            var c, d, e = this._core.settings;
            return "page" == e.slideBy ? (c = a.inArray(this.current(), this._pages), d = this._pages.length, b ? ++c : --c, c = this._pages[(c % d + d) % d].start) : (c = this._core.relative(this._core.current()), d = this._core.items().length, b ? c += e.slideBy : c -= e.slideBy), c
        }, e.prototype.next = function (b) {
            a.proxy(this._overrides.to, this._core)(this.getPosition(!0), b)
        }, e.prototype.prev = function (b) {
            a.proxy(this._overrides.to, this._core)(this.getPosition(!1), b)
        }, e.prototype.to = function (b, c, d) {
            var e;
            !d && this._pages.length ? (e = this._pages.length, a.proxy(this._overrides.to, this._core)(this._pages[(b % e + e) % e].start, c)) : a.proxy(this._overrides.to, this._core)(b, c)
        }, a.fn.sbswOwlCarousel.Constructor.Plugins.Navigation = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        "use strict";
        var e = function (c) {
            this._core = c, this._hashes = {}, this.$element = this._core.$element, this._handlers = {
                "initialized.owl.carousel": a.proxy(function (c) {
                    c.namespace && "URLHash" === this._core.settings.startPosition && a(b).trigger("hashchange.owl.navigation")
                }, this), "prepared.owl.carousel": a.proxy(function (b) {
                    if (b.namespace) {
                        var c = a(b.content).find("[data-hash]").addBack("[data-hash]").attr("data-hash");
                        if (!c) return;
                        this._hashes[c] = b.content
                    }
                }, this), "changed.owl.carousel": a.proxy(function (c) {
                    if (c.namespace && "position" === c.property.name) {
                        var d = this._core.items(this._core.relative(this._core.current())),
                            e = a.map(this._hashes, function (a, b) {
                                return a === d ? b : null
                            }).join();
                        if (!e || b.location.hash.slice(1) === e) return;
                        b.location.hash = e
                    }
                }, this)
            }, this._core.options = a.extend({}, e.Defaults, this._core.options), this.$element.on(this._handlers), a(b).on("hashchange.owl.navigation", a.proxy(function (a) {
                var c = b.location.hash.substring(1), e = this._core.$stage.children(),
                    f = this._hashes[c] && e.index(this._hashes[c]);
                f !== d && f !== this._core.current() && this._core.to(this._core.relative(f), !1, !0)
            }, this))
        };
        e.Defaults = {URLhashListener: !1}, e.prototype.destroy = function () {
            var c, d;
            a(b).off("hashchange.owl.navigation");
            for (c in this._handlers) this._core.$element.off(c, this._handlers[c]);
            for (d in Object.getOwnPropertyNames(this)) "function" != typeof this[d] && (this[d] = null)
        }, a.fn.sbswOwlCarousel.Constructor.Plugins.Hash = e
    }(window.Zepto || window.jQuery, window, document), function (a, b, c, d) {
        function e(b, c) {
            var e = !1, f = b.charAt(0).toUpperCase() + b.slice(1);
            return a.each((b + " " + h.join(f + " ") + f).split(" "), function (a, b) {
                if (g[b] !== d) return e = !c || b, !1
            }), e
        }

        function f(a) {
            return e(a, !0)
        }

        var g = a("<support>").get(0).style, h = "Webkit Moz O ms".split(" "), i = {
            transition: {
                end: {
                    WebkitTransition: "webkitTransitionEnd",
                    MozTransition: "transitionend",
                    OTransition: "oTransitionEnd",
                    transition: "transitionend"
                }
            },
            animation: {
                end: {
                    WebkitAnimation: "webkitAnimationEnd",
                    MozAnimation: "animationend",
                    OAnimation: "oAnimationEnd",
                    animation: "animationend"
                }
            }
        }, j = {
            csstransforms: function () {
                return !!e("transform")
            }, csstransforms3d: function () {
                return !!e("perspective")
            }, csstransitions: function () {
                return !!e("transition")
            }, cssanimations: function () {
                return !!e("animation")
            }
        };
        j.csstransitions() && (a.support.transition = new String(f("transition")), a.support.transition.end = i.transition.end[a.support.transition]), j.cssanimations() && (a.support.animation = new String(f("animation")), a.support.animation.end = i.animation.end[a.support.animation]), j.csstransforms() && (a.support.transform = new String(f("transform")), a.support.transform3d = j.csstransforms3d())
    }(window.Zepto || window.jQuery, window, document);

    // Two Row Carousel
    ;(function ($, window, document, undefined) {
        Owl2row = function (scope) {
            this.owl = scope;
            this.owl.options = $.extend({}, Owl2row.Defaults, this.owl.options);
            //link callback events with owl carousel here

            this.handlers = {
                'initialize.owl.carousel': $.proxy(function (e) {
                    if (this.owl.settings.owl2row) {
                        this.build2row(this);
                    }
                }, this)
            };

            this.owl.$element.on(this.handlers);
        };

        Owl2row.Defaults = {
            owl2row: false,
            owl2rowTarget: 'sbsw-item',
            owl2rowContainer: 'sbsw_owl2row-item',
            owl2rowDirection: 'utd' // ltr
        };

        //mehtods:
        Owl2row.prototype.build2row = function (thisScope) {

            var carousel = $(thisScope.owl.$element);
            var carouselItems = carousel.find('.' + thisScope.owl.options.owl2rowTarget);

            var aEvenElements = [];
            var aOddElements = [];

            $.each(carouselItems, function (index, item) {
                if (index % 2 === 0) {
                    aEvenElements.push(item);
                } else {
                    aOddElements.push(item);
                }
            });

            //carousel.empty();

            switch (thisScope.owl.options.owl2rowDirection) {
                case 'ltr':
                    thisScope.leftToright(thisScope, carousel, carouselItems);
                    break;

                default :
                    thisScope.upTodown(thisScope, aEvenElements, aOddElements, carousel);
            }

        };

        Owl2row.prototype.leftToright = function (thisScope, carousel, carouselItems) {

            var o2wContainerClass = thisScope.owl.options.owl2rowContainer;
            var owlMargin = thisScope.owl.options.margin;
            var carouselItemsLength = carouselItems.length;
            var firsArr = [];
            var secondArr = [];

            if (carouselItemsLength % 2 === 1) {
                carouselItemsLength = ((carouselItemsLength - 1) / 2) + 1;
            } else {
                carouselItemsLength = carouselItemsLength / 2;
            }

            $.each(carouselItems, function (index, item) {


                if (index < carouselItemsLength) {
                    firsArr.push(item);
                } else {
                    secondArr.push(item);
                }
            });

            $.each(firsArr, function (index, item) {
                var rowContainer = $('<div class="' + o2wContainerClass + '"/>');

                var firstRowElement = firsArr[index];
                firstRowElement.style.marginBottom = owlMargin + 'px';

                rowContainer
                    .append(firstRowElement)
                    .append(secondArr[index]);

                carousel.append(rowContainer);
            });

        };

        Owl2row.prototype.upTodown = function (thisScope, aEvenElements, aOddElements, carousel) {

            var o2wContainerClass = thisScope.owl.options.owl2rowContainer;
            var owlMargin = thisScope.owl.options.margin;

            $.each(aEvenElements, function (index, item) {

                var rowContainer = $('<div class="' + o2wContainerClass + '"/>');
                var evenElement = aEvenElements[index];

                evenElement.style.marginBottom = owlMargin + 'px';

                rowContainer
                    .append(evenElement)
                    .append(aOddElements[index]);

                carousel.append(rowContainer);
            });
        };

        /**
         * Destroys the plugin.
         */
        Owl2row.prototype.destroy = function () {
            var handler, property;
        };

        $.fn.sbswOwlCarousel.Constructor.Plugins['owl2row'] = Owl2row;
    })(window.Zepto || window.jQuery, window, document);


    /*! Hammer.JS - v2.0.8 - 2016-04-23
        * http://hammerjs.github.io/
        *
        * Copyright (c) 2016 Jorik Tangelder;
        * Licensed under the MIT license
        * */
    if (typeof sbswOptions.no_mob_swipe === 'undefined') {
        !function (a, b, c, d) {
            "use strict";

            function e(a, b, c) {
                return setTimeout(j(a, c), b)
            }

            function f(a, b, c) {
                return Array.isArray(a) ? (g(a, c[b], c), !0) : !1
            }

            function g(a, b, c) {
                var e;
                if (a) if (a.forEach) a.forEach(b, c); else if (a.length !== d) for (e = 0; e < a.length;) b.call(c, a[e], e, a), e++; else for (e in a) a.hasOwnProperty(e) && b.call(c, a[e], e, a)
            }

            function h(b, c, d) {
                var e = "DEPRECATED METHOD: " + c + "\n" + d + " AT \n";
                return function () {
                    var c = new Error("get-stack-trace"),
                        d = c && c.stack ? c.stack.replace(/^[^\(]+?[\n$]/gm, "").replace(/^\s+at\s+/gm, "").replace(/^Object.<anonymous>\s*\(/gm, "{anonymous}()@") : "Unknown Stack Trace",
                        f = a.console && (a.console.warn || a.console.log);
                    return f && f.call(a.console, e, d), b.apply(this, arguments)
                }
            }

            function i(a, b, c) {
                var d, e = b.prototype;
                d = a.prototype = Object.create(e), d.constructor = a, d._super = e, c && la(d, c)
            }

            function j(a, b) {
                return function () {
                    return a.apply(b, arguments)
                }
            }

            function k(a, b) {
                return typeof a == oa ? a.apply(b ? b[0] || d : d, b) : a
            }

            function l(a, b) {
                return a === d ? b : a
            }

            function m(a, b, c) {
                g(q(b), function (b) {
                    a.addEventListener(b, c, !1)
                })
            }

            function n(a, b, c) {
                g(q(b), function (b) {
                    a.removeEventListener(b, c, !1)
                })
            }

            function o(a, b) {
                for (; a;) {
                    if (a == b) return !0;
                    a = a.parentNode
                }
                return !1
            }

            function p(a, b) {
                return a.indexOf(b) > -1
            }

            function q(a) {
                return a.trim().split(/\s+/g)
            }

            function r(a, b, c) {
                if (a.indexOf && !c) return a.indexOf(b);
                for (var d = 0; d < a.length;) {
                    if (c && a[d][c] == b || !c && a[d] === b) return d;
                    d++
                }
                return -1
            }

            function s(a) {
                return Array.prototype.slice.call(a, 0)
            }

            function t(a, b, c) {
                for (var d = [], e = [], f = 0; f < a.length;) {
                    var g = b ? a[f][b] : a[f];
                    r(e, g) < 0 && d.push(a[f]), e[f] = g, f++
                }
                return c && (d = b ? d.sort(function (a, c) {
                    return a[b] > c[b]
                }) : d.sort()), d
            }

            function u(a, b) {
                for (var c, e, f = b[0].toUpperCase() + b.slice(1), g = 0; g < ma.length;) {
                    if (c = ma[g], e = c ? c + f : b, e in a) return e;
                    g++
                }
                return d
            }

            function v() {
                return ua++
            }

            function w(b) {
                var c = b.ownerDocument || b;
                return c.defaultView || c.parentWindow || a
            }

            function x(a, b) {
                var c = this;
                this.manager = a, this.callback = b, this.element = a.element, this.target = a.options.inputTarget, this.domHandler = function (b) {
                    k(a.options.enable, [a]) && c.handler(b)
                }, this.init()
            }

            function y(a) {
                var b, c = a.options.inputClass;
                return new (b = c ? c : xa ? M : ya ? P : wa ? R : L)(a, z)
            }

            function z(a, b, c) {
                var d = c.pointers.length, e = c.changedPointers.length, f = b & Ea && d - e === 0,
                    g = b & (Ga | Ha) && d - e === 0;
                c.isFirst = !!f, c.isFinal = !!g, f && (a.session = {}), c.eventType = b, A(a, c), a.emit("hammer.input", c), a.recognize(c), a.session.prevInput = c
            }

            function A(a, b) {
                var c = a.session, d = b.pointers, e = d.length;
                c.firstInput || (c.firstInput = D(b)), e > 1 && !c.firstMultiple ? c.firstMultiple = D(b) : 1 === e && (c.firstMultiple = !1);
                var f = c.firstInput, g = c.firstMultiple, h = g ? g.center : f.center, i = b.center = E(d);
                b.timeStamp = ra(), b.deltaTime = b.timeStamp - f.timeStamp, b.angle = I(h, i), b.distance = H(h, i), B(c, b), b.offsetDirection = G(b.deltaX, b.deltaY);
                var j = F(b.deltaTime, b.deltaX, b.deltaY);
                b.overallVelocityX = j.x, b.overallVelocityY = j.y, b.overallVelocity = qa(j.x) > qa(j.y) ? j.x : j.y, b.scale = g ? K(g.pointers, d) : 1, b.rotation = g ? J(g.pointers, d) : 0, b.maxPointers = c.prevInput ? b.pointers.length > c.prevInput.maxPointers ? b.pointers.length : c.prevInput.maxPointers : b.pointers.length, C(c, b);
                var k = a.element;
                o(b.srcEvent.target, k) && (k = b.srcEvent.target), b.target = k
            }

            function B(a, b) {
                var c = b.center, d = a.offsetDelta || {}, e = a.prevDelta || {}, f = a.prevInput || {};
                b.eventType !== Ea && f.eventType !== Ga || (e = a.prevDelta = {
                    x: f.deltaX || 0,
                    y: f.deltaY || 0
                }, d = a.offsetDelta = {x: c.x, y: c.y}), b.deltaX = e.x + (c.x - d.x), b.deltaY = e.y + (c.y - d.y)
            }

            function C(a, b) {
                var c, e, f, g, h = a.lastInterval || b, i = b.timeStamp - h.timeStamp;
                if (b.eventType != Ha && (i > Da || h.velocity === d)) {
                    var j = b.deltaX - h.deltaX, k = b.deltaY - h.deltaY, l = F(i, j, k);
                    e = l.x, f = l.y, c = qa(l.x) > qa(l.y) ? l.x : l.y, g = G(j, k), a.lastInterval = b
                } else c = h.velocity, e = h.velocityX, f = h.velocityY, g = h.direction;
                b.velocity = c, b.velocityX = e, b.velocityY = f, b.direction = g
            }

            function D(a) {
                for (var b = [], c = 0; c < a.pointers.length;) b[c] = {
                    clientX: pa(a.pointers[c].clientX),
                    clientY: pa(a.pointers[c].clientY)
                }, c++;
                return {timeStamp: ra(), pointers: b, center: E(b), deltaX: a.deltaX, deltaY: a.deltaY}
            }

            function E(a) {
                var b = a.length;
                if (1 === b) return {x: pa(a[0].clientX), y: pa(a[0].clientY)};
                for (var c = 0, d = 0, e = 0; b > e;) c += a[e].clientX, d += a[e].clientY, e++;
                return {x: pa(c / b), y: pa(d / b)}
            }

            function F(a, b, c) {
                return {x: b / a || 0, y: c / a || 0}
            }

            function G(a, b) {
                return a === b ? Ia : qa(a) >= qa(b) ? 0 > a ? Ja : Ka : 0 > b ? La : Ma
            }

            function H(a, b, c) {
                c || (c = Qa);
                var d = b[c[0]] - a[c[0]], e = b[c[1]] - a[c[1]];
                return Math.sqrt(d * d + e * e)
            }

            function I(a, b, c) {
                c || (c = Qa);
                var d = b[c[0]] - a[c[0]], e = b[c[1]] - a[c[1]];
                return 180 * Math.atan2(e, d) / Math.PI
            }

            function J(a, b) {
                return I(b[1], b[0], Ra) + I(a[1], a[0], Ra)
            }

            function K(a, b) {
                return H(b[0], b[1], Ra) / H(a[0], a[1], Ra)
            }

            function L() {
                this.evEl = Ta, this.evWin = Ua, this.pressed = !1, x.apply(this, arguments)
            }

            function M() {
                this.evEl = Xa, this.evWin = Ya, x.apply(this, arguments), this.store = this.manager.session.pointerEvents = []
            }

            function N() {
                this.evTarget = $a, this.evWin = _a, this.started = !1, x.apply(this, arguments)
            }

            function O(a, b) {
                var c = s(a.touches), d = s(a.changedTouches);
                return b & (Ga | Ha) && (c = t(c.concat(d), "identifier", !0)), [c, d]
            }

            function P() {
                this.evTarget = bb, this.targetIds = {}, x.apply(this, arguments)
            }

            function Q(a, b) {
                var c = s(a.touches), d = this.targetIds;
                if (b & (Ea | Fa) && 1 === c.length) return d[c[0].identifier] = !0, [c, c];
                var e, f, g = s(a.changedTouches), h = [], i = this.target;
                if (f = c.filter(function (a) {
                    return o(a.target, i)
                }), b === Ea) for (e = 0; e < f.length;) d[f[e].identifier] = !0, e++;
                for (e = 0; e < g.length;) d[g[e].identifier] && h.push(g[e]), b & (Ga | Ha) && delete d[g[e].identifier], e++;
                return h.length ? [t(f.concat(h), "identifier", !0), h] : void 0
            }

            function R() {
                x.apply(this, arguments);
                var a = j(this.handler, this);
                this.touch = new P(this.manager, a), this.mouse = new L(this.manager, a), this.primaryTouch = null, this.lastTouches = []
            }

            function S(a, b) {
                a & Ea ? (this.primaryTouch = b.changedPointers[0].identifier, T.call(this, b)) : a & (Ga | Ha) && T.call(this, b)
            }

            function T(a) {
                var b = a.changedPointers[0];
                if (b.identifier === this.primaryTouch) {
                    var c = {x: b.clientX, y: b.clientY};
                    this.lastTouches.push(c);
                    var d = this.lastTouches, e = function () {
                        var a = d.indexOf(c);
                        a > -1 && d.splice(a, 1)
                    };
                    setTimeout(e, cb)
                }
            }

            function U(a) {
                for (var b = a.srcEvent.clientX, c = a.srcEvent.clientY, d = 0; d < this.lastTouches.length; d++) {
                    var e = this.lastTouches[d], f = Math.abs(b - e.x), g = Math.abs(c - e.y);
                    if (db >= f && db >= g) return !0
                }
                return !1
            }

            function V(a, b) {
                this.manager = a, this.set(b)
            }

            function W(a) {
                if (p(a, jb)) return jb;
                var b = p(a, kb), c = p(a, lb);
                return b && c ? jb : b || c ? b ? kb : lb : p(a, ib) ? ib : hb
            }

            function X() {
                if (!fb) return !1;
                var b = {}, c = a.CSS && a.CSS.supports;
                return ["auto", "manipulation", "pan-y", "pan-x", "pan-x pan-y", "none"].forEach(function (d) {
                    b[d] = c ? a.CSS.supports("touch-action", d) : !0
                }), b
            }

            function Y(a) {
                this.options = la({}, this.defaults, a || {}), this.id = v(), this.manager = null, this.options.enable = l(this.options.enable, !0), this.state = nb, this.simultaneous = {}, this.requireFail = []
            }

            function Z(a) {
                return a & sb ? "cancel" : a & qb ? "end" : a & pb ? "move" : a & ob ? "start" : ""
            }

            function $(a) {
                return a == Ma ? "down" : a == La ? "up" : a == Ja ? "left" : a == Ka ? "right" : ""
            }

            function _(a, b) {
                var c = b.manager;
                return c ? c.get(a) : a
            }

            function aa() {
                Y.apply(this, arguments)
            }

            function ba() {
                aa.apply(this, arguments), this.pX = null, this.pY = null
            }

            function ca() {
                aa.apply(this, arguments)
            }

            function da() {
                Y.apply(this, arguments), this._timer = null, this._input = null
            }

            function ea() {
                aa.apply(this, arguments)
            }

            function fa() {
                aa.apply(this, arguments)
            }

            function ga() {
                Y.apply(this, arguments), this.pTime = !1, this.pCenter = !1, this._timer = null, this._input = null, this.count = 0
            }

            function ha(a, b) {
                return b = b || {}, b.recognizers = l(b.recognizers, ha.defaults.preset), new ia(a, b)
            }

            function ia(a, b) {
                this.options = la({}, ha.defaults, b || {}), this.options.inputTarget = this.options.inputTarget || a, this.handlers = {}, this.session = {}, this.recognizers = [], this.oldCssProps = {}, this.element = a, this.input = y(this), this.touchAction = new V(this, this.options.touchAction), ja(this, !0), g(this.options.recognizers, function (a) {
                    var b = this.add(new a[0](a[1]));
                    a[2] && b.recognizeWith(a[2]), a[3] && b.requireFailure(a[3])
                }, this)
            }

            function ja(a, b) {
                var c = a.element;
                if (c.style) {
                    var d;
                    g(a.options.cssProps, function (e, f) {
                        d = u(c.style, f), b ? (a.oldCssProps[d] = c.style[d], c.style[d] = e) : c.style[d] = a.oldCssProps[d] || ""
                    }), b || (a.oldCssProps = {})
                }
            }

            function ka(a, c) {
                var d = b.createEvent("Event");
                d.initEvent(a, !0, !0), d.gesture = c, c.target.dispatchEvent(d)
            }

            var la, ma = ["", "webkit", "Moz", "MS", "ms", "o"], na = b.createElement("div"), oa = "function",
                pa = Math.round, qa = Math.abs, ra = Date.now;
            la = "function" != typeof Object.assign ? function (a) {
                if (a === d || null === a) throw new TypeError("Cannot convert undefined or null to object");
                for (var b = Object(a), c = 1; c < arguments.length; c++) {
                    var e = arguments[c];
                    if (e !== d && null !== e) for (var f in e) e.hasOwnProperty(f) && (b[f] = e[f])
                }
                return b
            } : Object.assign;
            var sa = h(function (a, b, c) {
                    for (var e = Object.keys(b), f = 0; f < e.length;) (!c || c && a[e[f]] === d) && (a[e[f]] = b[e[f]]), f++;
                    return a
                }, "extend", "Use `assign`."), ta = h(function (a, b) {
                    return sa(a, b, !0)
                }, "merge", "Use `assign`."), ua = 1, va = /mobile|tablet|ip(ad|hone|od)|android/i,
                wa = "ontouchstart" in a, xa = u(a, "PointerEvent") !== d, ya = wa && va.test(navigator.userAgent),
                za = "touch", Aa = "pen", Ba = "mouse", Ca = "kinect", Da = 25, Ea = 1, Fa = 2, Ga = 4, Ha = 8, Ia = 1,
                Ja = 2, Ka = 4, La = 8, Ma = 16, Na = Ja | Ka, Oa = La | Ma, Pa = Na | Oa, Qa = ["x", "y"],
                Ra = ["clientX", "clientY"];
            x.prototype = {
                handler: function () {
                }, init: function () {
                    this.evEl && m(this.element, this.evEl, this.domHandler), this.evTarget && m(this.target, this.evTarget, this.domHandler), this.evWin && m(w(this.element), this.evWin, this.domHandler)
                }, destroy: function () {
                    this.evEl && n(this.element, this.evEl, this.domHandler), this.evTarget && n(this.target, this.evTarget, this.domHandler), this.evWin && n(w(this.element), this.evWin, this.domHandler)
                }
            };
            var Sa = {mousedown: Ea, mousemove: Fa, mouseup: Ga}, Ta = "mousedown", Ua = "mousemove mouseup";
            i(L, x, {
                handler: function (a) {
                    var b = Sa[a.type];
                    b & Ea && 0 === a.button && (this.pressed = !0), b & Fa && 1 !== a.which && (b = Ga), this.pressed && (b & Ga && (this.pressed = !1), this.callback(this.manager, b, {
                        pointers: [a],
                        changedPointers: [a],
                        pointerType: Ba,
                        srcEvent: a
                    }))
                }
            });
            var Va = {pointerdown: Ea, pointermove: Fa, pointerup: Ga, pointercancel: Ha, pointerout: Ha},
                Wa = {2: za, 3: Aa, 4: Ba, 5: Ca}, Xa = "pointerdown", Ya = "pointermove pointerup pointercancel";
            a.MSPointerEvent && !a.PointerEvent && (Xa = "MSPointerDown", Ya = "MSPointerMove MSPointerUp MSPointerCancel"), i(M, x, {
                handler: function (a) {
                    var b = this.store, c = !1, d = a.type.toLowerCase().replace("ms", ""), e = Va[d],
                        f = Wa[a.pointerType] || a.pointerType, g = f == za, h = r(b, a.pointerId, "pointerId");
                    e & Ea && (0 === a.button || g) ? 0 > h && (b.push(a), h = b.length - 1) : e & (Ga | Ha) && (c = !0), 0 > h || (b[h] = a, this.callback(this.manager, e, {
                        pointers: b,
                        changedPointers: [a],
                        pointerType: f,
                        srcEvent: a
                    }), c && b.splice(h, 1))
                }
            });
            var Za = {touchstart: Ea, touchmove: Fa, touchend: Ga, touchcancel: Ha}, $a = "touchstart",
                _a = "touchstart touchmove touchend touchcancel";
            i(N, x, {
                handler: function (a) {
                    var b = Za[a.type];
                    if (b === Ea && (this.started = !0), this.started) {
                        var c = O.call(this, a, b);
                        b & (Ga | Ha) && c[0].length - c[1].length === 0 && (this.started = !1), this.callback(this.manager, b, {
                            pointers: c[0],
                            changedPointers: c[1],
                            pointerType: za,
                            srcEvent: a
                        })
                    }
                }
            });
            var ab = {touchstart: Ea, touchmove: Fa, touchend: Ga, touchcancel: Ha},
                bb = "touchstart touchmove touchend touchcancel";
            i(P, x, {
                handler: function (a) {
                    var b = ab[a.type], c = Q.call(this, a, b);
                    c && this.callback(this.manager, b, {
                        pointers: c[0],
                        changedPointers: c[1],
                        pointerType: za,
                        srcEvent: a
                    })
                }
            });
            var cb = 2500, db = 25;
            i(R, x, {
                handler: function (a, b, c) {
                    var d = c.pointerType == za, e = c.pointerType == Ba;
                    if (!(e && c.sourceCapabilities && c.sourceCapabilities.firesTouchEvents)) {
                        if (d) S.call(this, b, c); else if (e && U.call(this, c)) return;
                        this.callback(a, b, c)
                    }
                }, destroy: function () {
                    this.touch.destroy(), this.mouse.destroy()
                }
            });
            var eb = u(na.style, "touchAction"), fb = eb !== d, gb = "compute", hb = "auto", ib = "manipulation",
                jb = "none", kb = "pan-x", lb = "pan-y", mb = X();
            V.prototype = {
                set: function (a) {
                    a == gb && (a = this.compute()), fb && this.manager.element.style && mb[a] && (this.manager.element.style[eb] = a), this.actions = a.toLowerCase().trim()
                }, update: function () {
                    this.set(this.manager.options.touchAction)
                }, compute: function () {
                    var a = [];
                    return g(this.manager.recognizers, function (b) {
                        k(b.options.enable, [b]) && (a = a.concat(b.getTouchAction()))
                    }), W(a.join(" "))
                }, preventDefaults: function (a) {
                    var b = a.srcEvent, c = a.offsetDirection;
                    if (this.manager.session.prevented) return void b.preventDefault();
                    var d = this.actions, e = p(d, jb) && !mb[jb], f = p(d, lb) && !mb[lb], g = p(d, kb) && !mb[kb];
                    if (e) {
                        var h = 1 === a.pointers.length, i = a.distance < 2, j = a.deltaTime < 250;
                        if (h && i && j) return
                    }
                    return g && f ? void 0 : e || f && c & Na || g && c & Oa ? this.preventSrc(b) : void 0
                }, preventSrc: function (a) {
                    this.manager.session.prevented = !0, a.preventDefault()
                }
            };
            var nb = 1, ob = 2, pb = 4, qb = 8, rb = qb, sb = 16, tb = 32;
            Y.prototype = {
                defaults: {}, set: function (a) {
                    return la(this.options, a), this.manager && this.manager.touchAction.update(), this
                }, recognizeWith: function (a) {
                    if (f(a, "recognizeWith", this)) return this;
                    var b = this.simultaneous;
                    return a = _(a, this), b[a.id] || (b[a.id] = a, a.recognizeWith(this)), this
                }, dropRecognizeWith: function (a) {
                    return f(a, "dropRecognizeWith", this) ? this : (a = _(a, this), delete this.simultaneous[a.id], this)
                }, requireFailure: function (a) {
                    if (f(a, "requireFailure", this)) return this;
                    var b = this.requireFail;
                    return a = _(a, this), -1 === r(b, a) && (b.push(a), a.requireFailure(this)), this
                }, dropRequireFailure: function (a) {
                    if (f(a, "dropRequireFailure", this)) return this;
                    a = _(a, this);
                    var b = r(this.requireFail, a);
                    return b > -1 && this.requireFail.splice(b, 1), this
                }, hasRequireFailures: function () {
                    return this.requireFail.length > 0
                }, canRecognizeWith: function (a) {
                    return !!this.simultaneous[a.id]
                }, emit: function (a) {
                    function b(b) {
                        c.manager.emit(b, a)
                    }

                    var c = this, d = this.state;
                    qb > d && b(c.options.event + Z(d)), b(c.options.event), a.additionalEvent && b(a.additionalEvent), d >= qb && b(c.options.event + Z(d))
                }, tryEmit: function (a) {
                    return this.canEmit() ? this.emit(a) : void (this.state = tb)
                }, canEmit: function () {
                    for (var a = 0; a < this.requireFail.length;) {
                        if (!(this.requireFail[a].state & (tb | nb))) return !1;
                        a++
                    }
                    return !0
                }, recognize: function (a) {
                    var b = la({}, a);
                    return k(this.options.enable, [this, b]) ? (this.state & (rb | sb | tb) && (this.state = nb), this.state = this.process(b), void (this.state & (ob | pb | qb | sb) && this.tryEmit(b))) : (this.reset(), void (this.state = tb))
                }, process: function (a) {
                }, getTouchAction: function () {
                }, reset: function () {
                }
            }, i(aa, Y, {
                defaults: {pointers: 1}, attrTest: function (a) {
                    var b = this.options.pointers;
                    return 0 === b || a.pointers.length === b
                }, process: function (a) {
                    var b = this.state, c = a.eventType, d = b & (ob | pb), e = this.attrTest(a);
                    return d && (c & Ha || !e) ? b | sb : d || e ? c & Ga ? b | qb : b & ob ? b | pb : ob : tb
                }
            }), i(ba, aa, {
                defaults: {event: "pan", threshold: 10, pointers: 1, direction: Pa},
                getTouchAction: function () {
                    var a = this.options.direction, b = [];
                    return a & Na && b.push(lb), a & Oa && b.push(kb), b
                },
                directionTest: function (a) {
                    var b = this.options, c = !0, d = a.distance, e = a.direction, f = a.deltaX, g = a.deltaY;
                    return e & b.direction || (b.direction & Na ? (e = 0 === f ? Ia : 0 > f ? Ja : Ka, c = f != this.pX, d = Math.abs(a.deltaX)) : (e = 0 === g ? Ia : 0 > g ? La : Ma, c = g != this.pY, d = Math.abs(a.deltaY))), a.direction = e, c && d > b.threshold && e & b.direction
                },
                attrTest: function (a) {
                    return aa.prototype.attrTest.call(this, a) && (this.state & ob || !(this.state & ob) && this.directionTest(a))
                },
                emit: function (a) {
                    this.pX = a.deltaX, this.pY = a.deltaY;
                    var b = $(a.direction);
                    b && (a.additionalEvent = this.options.event + b), this._super.emit.call(this, a)
                }
            }), i(ca, aa, {
                defaults: {event: "pinch", threshold: 0, pointers: 2}, getTouchAction: function () {
                    return [jb]
                }, attrTest: function (a) {
                    return this._super.attrTest.call(this, a) && (Math.abs(a.scale - 1) > this.options.threshold || this.state & ob)
                }, emit: function (a) {
                    if (1 !== a.scale) {
                        var b = a.scale < 1 ? "in" : "out";
                        a.additionalEvent = this.options.event + b
                    }
                    this._super.emit.call(this, a)
                }
            }), i(da, Y, {
                defaults: {event: "press", pointers: 1, time: 251, threshold: 9},
                getTouchAction: function () {
                    return [hb]
                },
                process: function (a) {
                    var b = this.options, c = a.pointers.length === b.pointers, d = a.distance < b.threshold,
                        f = a.deltaTime > b.time;
                    if (this._input = a, !d || !c || a.eventType & (Ga | Ha) && !f) this.reset(); else if (a.eventType & Ea) this.reset(), this._timer = e(function () {
                        this.state = rb, this.tryEmit()
                    }, b.time, this); else if (a.eventType & Ga) return rb;
                    return tb
                },
                reset: function () {
                    clearTimeout(this._timer)
                },
                emit: function (a) {
                    this.state === rb && (a && a.eventType & Ga ? this.manager.emit(this.options.event + "up", a) : (this._input.timeStamp = ra(), this.manager.emit(this.options.event, this._input)))
                }
            }), i(ea, aa, {
                defaults: {event: "rotate", threshold: 0, pointers: 2}, getTouchAction: function () {
                    return [jb]
                }, attrTest: function (a) {
                    return this._super.attrTest.call(this, a) && (Math.abs(a.rotation) > this.options.threshold || this.state & ob)
                }
            }), i(fa, aa, {
                defaults: {event: "swipe", threshold: 10, velocity: .3, direction: Na | Oa, pointers: 1},
                getTouchAction: function () {
                    return ba.prototype.getTouchAction.call(this)
                },
                attrTest: function (a) {
                    var b, c = this.options.direction;
                    return c & (Na | Oa) ? b = a.overallVelocity : c & Na ? b = a.overallVelocityX : c & Oa && (b = a.overallVelocityY), this._super.attrTest.call(this, a) && c & a.offsetDirection && a.distance > this.options.threshold && a.maxPointers == this.options.pointers && qa(b) > this.options.velocity && a.eventType & Ga
                },
                emit: function (a) {
                    var b = $(a.offsetDirection);
                    b && this.manager.emit(this.options.event + b, a), this.manager.emit(this.options.event, a)
                }
            }), i(ga, Y, {
                defaults: {
                    event: "tap",
                    pointers: 1,
                    taps: 1,
                    interval: 300,
                    time: 250,
                    threshold: 9,
                    posThreshold: 10
                }, getTouchAction: function () {
                    return [ib]
                }, process: function (a) {
                    var b = this.options, c = a.pointers.length === b.pointers, d = a.distance < b.threshold,
                        f = a.deltaTime < b.time;
                    if (this.reset(), a.eventType & Ea && 0 === this.count) return this.failTimeout();
                    if (d && f && c) {
                        if (a.eventType != Ga) return this.failTimeout();
                        var g = this.pTime ? a.timeStamp - this.pTime < b.interval : !0,
                            h = !this.pCenter || H(this.pCenter, a.center) < b.posThreshold;
                        this.pTime = a.timeStamp, this.pCenter = a.center, h && g ? this.count += 1 : this.count = 1, this._input = a;
                        var i = this.count % b.taps;
                        if (0 === i) return this.hasRequireFailures() ? (this._timer = e(function () {
                            this.state = rb, this.tryEmit()
                        }, b.interval, this), ob) : rb
                    }
                    return tb
                }, failTimeout: function () {
                    return this._timer = e(function () {
                        this.state = tb
                    }, this.options.interval, this), tb
                }, reset: function () {
                    clearTimeout(this._timer)
                }, emit: function () {
                    this.state == rb && (this._input.tapCount = this.count, this.manager.emit(this.options.event, this._input))
                }
            }), ha.VERSION = "2.0.8", ha.defaults = {
                domEvents: !1,
                touchAction: gb,
                enable: !0,
                inputTarget: null,
                inputClass: null,
                preset: [[ea, {enable: !1}], [ca, {enable: !1}, ["rotate"]], [fa, {direction: Na}], [ba, {direction: Na}, ["swipe"]], [ga], [ga, {
                    event: "doubletap",
                    taps: 2
                }, ["tap"]], [da]],
                cssProps: {
                    userSelect: "none",
                    touchSelect: "none",
                    touchCallout: "none",
                    contentZooming: "none",
                    userDrag: "none",
                    tapHighlightColor: "rgba(0,0,0,0)"
                }
            };
            var ub = 1, vb = 2;
            ia.prototype = {
                set: function (a) {
                    return la(this.options, a), a.touchAction && this.touchAction.update(), a.inputTarget && (this.input.destroy(), this.input.target = a.inputTarget, this.input.init()), this
                }, stop: function (a) {
                    this.session.stopped = a ? vb : ub
                }, recognize: function (a) {
                    var b = this.session;
                    if (!b.stopped) {
                        this.touchAction.preventDefaults(a);
                        var c, d = this.recognizers, e = b.curRecognizer;
                        (!e || e && e.state & rb) && (e = b.curRecognizer = null);
                        for (var f = 0; f < d.length;) c = d[f], b.stopped === vb || e && c != e && !c.canRecognizeWith(e) ? c.reset() : c.recognize(a), !e && c.state & (ob | pb | qb) && (e = b.curRecognizer = c), f++
                    }
                }, get: function (a) {
                    if (a instanceof Y) return a;
                    for (var b = this.recognizers, c = 0; c < b.length; c++) if (b[c].options.event == a) return b[c];
                    return null
                }, add: function (a) {
                    if (f(a, "add", this)) return this;
                    var b = this.get(a.options.event);
                    return b && this.remove(b), this.recognizers.push(a), a.manager = this, this.touchAction.update(), a
                }, remove: function (a) {
                    if (f(a, "remove", this)) return this;
                    if (a = this.get(a)) {
                        var b = this.recognizers, c = r(b, a);
                        -1 !== c && (b.splice(c, 1), this.touchAction.update())
                    }
                    return this
                }, on: function (a, b) {
                    if (a !== d && b !== d) {
                        var c = this.handlers;
                        return g(q(a), function (a) {
                            c[a] = c[a] || [], c[a].push(b)
                        }), this
                    }
                }, off: function (a, b) {
                    if (a !== d) {
                        var c = this.handlers;
                        return g(q(a), function (a) {
                            b ? c[a] && c[a].splice(r(c[a], b), 1) : delete c[a]
                        }), this
                    }
                }, emit: function (a, b) {
                    this.options.domEvents && ka(a, b);
                    var c = this.handlers[a] && this.handlers[a].slice();
                    if (c && c.length) {
                        b.type = a, b.preventDefault = function () {
                            b.srcEvent.preventDefault()
                        };
                        for (var d = 0; d < c.length;) c[d](b), d++
                    }
                }, destroy: function () {
                    this.element && ja(this, !1), this.handlers = {}, this.session = {}, this.input.destroy(), this.element = null
                }
            }, la(ha, {
                INPUT_START: Ea,
                INPUT_MOVE: Fa,
                INPUT_END: Ga,
                INPUT_CANCEL: Ha,
                STATE_POSSIBLE: nb,
                STATE_BEGAN: ob,
                STATE_CHANGED: pb,
                STATE_ENDED: qb,
                STATE_RECOGNIZED: rb,
                STATE_CANCELLED: sb,
                STATE_FAILED: tb,
                DIRECTION_NONE: Ia,
                DIRECTION_LEFT: Ja,
                DIRECTION_RIGHT: Ka,
                DIRECTION_UP: La,
                DIRECTION_DOWN: Ma,
                DIRECTION_HORIZONTAL: Na,
                DIRECTION_VERTICAL: Oa,
                DIRECTION_ALL: Pa,
                Manager: ia,
                Input: x,
                TouchAction: V,
                TouchInput: P,
                MouseInput: L,
                PointerEventInput: M,
                TouchMouseInput: R,
                SingleTouchInput: N,
                Recognizer: Y,
                AttrRecognizer: aa,
                Tap: ga,
                Pan: ba,
                Swipe: fa,
                Pinch: ca,
                Rotate: ea,
                Press: da,
                on: m,
                off: n,
                each: g,
                merge: ta,
                extend: sa,
                assign: la,
                inherit: i,
                bindFn: j,
                prefixed: u
            });
            var wb = "undefined" != typeof a ? a : "undefined" != typeof self ? self : {};
            wb.Hammer = ha, "function" == typeof define && define.amd ? define(function () {
                return ha
            }) : "undefined" != typeof module && module.exports ? module.exports = ha : a[c] = ha
        }(window, document, "Hammer");
        (function (factory) {
            if (typeof define === 'function' && define.amd) {
                define(['jquery', 'hammerjs'], factory);
            } else if (typeof exports === 'object') {
                factory(require('jquery'), require('hammerjs'));
            } else {
                factory(jQuery, Hammer);
            }
        }(function (jQuery, Hammer) {
            function hammerify(el, options) {
                var $el = jQuery(el);
                if (!$el.data("hammer")) {
                    $el.data("hammer", new Hammer($el[0], options));
                }
            }

            jQuery.fn.hammer = function (options) {
                return this.each(function () {
                    hammerify(this, options);
                });
            };

            // extend the emit method to also trigger jQuery events
            Hammer.Manager.prototype.emit = (function (originalEmit) {
                return function (type, data) {
                    originalEmit.call(this, type, data);
                    jQuery(this.element).trigger({
                        type: type,
                        gesture: data
                    });
                };
            })(Hammer.Manager.prototype.emit);
        }));

    } else {
        var Hammer = {
            Manager: {
                prototype: {}
            }
        };
        (function (factory) {
            if (typeof define === 'function' && define.amd) {
                define(['jquery', 'hammerjs'], factory);
            } else if (typeof exports === 'object') {
                factory(require('jquery'), require('hammerjs'));
            } else {
                factory(jQuery, Hammer);
            }
        }(function (jQuery, Hammer) {
            function hammerify(el, options) {
            }

            jQuery.fn.hammer = function (options) {
                return this.each(function () {
                    hammerify(this, options);
                });
            };
            // extend the emit method to also trigger jQuery events
            Hammer.Manager.prototype.emit = (function (originalEmit) {
                return function (type, data) {
                    originalEmit.call(this, type, data);
                    jQuery(this.element).trigger({
                        type: type,
                        gesture: data
                    });
                };
            })(Hammer.Manager.prototype.emit);
        }));
    }

    (function($){

        function sbyAddVisibilityListener() {
            /* Detect when element becomes visible. Used for when the feed is initially hidden, in a tab for example. https://github.com/shaunbowe/jquery.visibilityChanged */
            !function (i) {
                var n = {
                    callback: function () {
                    }, runOnLoad: !0, frequency: 100, sbyPreviousVisibility: null
                }, c = {};
                c.sbyCheckVisibility = function (i, n) {
                    if (jQuery.contains(document, i[0])) {
                        var e = n.sbyPreviousVisibility, t = i.is(":visible");
                        n.sbyPreviousVisibility = t, null == e ? n.runOnLoad && n.callback(i, t) : e !== t && n.callback(i, t), setTimeout(function () {
                            c.sbyCheckVisibility(i, n)
                        }, n.frequency)
                    }
                }, i.fn.sbyVisibilityChanged = function (e) {
                    var t = i.extend({}, n, e);
                    return this.each(function () {
                        c.sbyCheckVisibility(i(this), t)
                    })
                }
            }(jQuery);
        }

        function Sbsw() {
            this.feeds = {};
            this.options = sbswOptions;
            this.isTouch = sbswIsTourch();
        }

        Sbsw.prototype = {
            createPage: function (createFeeds, createFeedsArgs) {
                if (typeof window.sbswajaxurl === 'undefined' || window.sbswajaxurl.indexOf(window.location.hostname) === -1) {
                    window.sbswajaxurl = window.location.hostname + '/wp-admin/admin-ajax.php';
                }

                $('.sbsw_no_js_error_message').remove();
                $('.sbsw_no_js').removeClass('sbsw_no_js');
                var youtubeScriptId = "sbsw-youtube-api";
                var youtubeScript = document.getElementById(youtubeScriptId);

                if (youtubeScript === null) {
                    var tag = document.createElement("script");
                    var firstScript = document.getElementsByTagName("script")[0];

                    tag.src = "https://www.youtube.com/iframe_api";
                    tag.id = youtubeScriptId;
                    firstScript.parentNode.insertBefore(tag, firstScript);

                }
                createFeeds(createFeedsArgs);
            },
            createLightbox: function() {
                var lbBuilder = sbswGetlightboxBuilder();
                var sbsw_lb_delay = (function () {
                    var sbsw_timer = 0;
                    return function (sbsw_callback, sbsw_ms) {
                        clearTimeout(sbsw_timer);
                        sbsw_timer = setTimeout(sbsw_callback, sbsw_ms);
                    };
                })();
                jQuery(window).resize(function () {
                    sbsw_lb_delay(function () {
                        lbBuilder.afterResize();
                    }, 200);
                });
                /* Lightbox v2.7.1 by Lokesh Dhakar - http://lokeshdhakar.com/projects/lightbox2/ - Heavily modified specifically for this plugin */
                (function() {
                    var a = jQuery,
                        b = function() {
                            function a() {
                                this.fadeDuration = 500, this.fitImagesInViewport = !0, this.resizeDuration = 700, this.positionFromTop = 50, this.showImageNumberLabel = !0, this.alwaysShowNavOnTouchDevices = !1, this.wrapAround = !1
                            }
                            return a.prototype.albumLabel = function(a, b) {
                                return a + " / " + b
                            }, a
                        }(),
                        c = function() {
                            function b(a) {
                                this.options = a, this.album = [], this.currentImageIndex = void 0, this.init()
                            }
                            return b.prototype.init = function() {
                                this.enable(), this.build()
                            }, b.prototype.enable = function() {
                                var b = this;
                                a("body").on("click", "a[data-sbsw-lightbox]", function(c) {
                                    return b.start(a(c.currentTarget)), !1
                                })
                            }, b.prototype.build = function() {
                                var b = this;
                                if (typeof a("#sbsw-lightbox").find(".sbsw-lb-container").hammer !== 'function') {
                                    var Hammer = {
                                        Manager: {
                                            prototype: {}
                                        }
                                    };
                                    (function (factory) {
                                        if (typeof define === 'function' && define.amd) {
                                            define(['jquery', 'hammerjs'], factory);
                                        } else if (typeof exports === 'object') {
                                            factory(require('jquery'), require('hammerjs'));
                                        } else {
                                            factory(jQuery, Hammer);
                                        }
                                    }(function (jQuery, Hammer) {
                                        function hammerify(el, options) {
                                        }

                                        jQuery.fn.hammer = function (options) {
                                            return this.each(function () {
                                                hammerify(this, options);
                                            });
                                        };
                                        // extend the emit method to also trigger jQuery events
                                        Hammer.Manager.prototype.emit = (function (originalEmit) {
                                            return function (type, data) {
                                                originalEmit.call(this, type, data);
                                                jQuery(this.element).trigger({
                                                    type: type,
                                                    gesture: data
                                                });
                                            };
                                        })(Hammer.Manager.prototype.emit);
                                    }));
                                }
                                a(""+
                                    lbBuilder.template()).appendTo(a("body")), this.$lightbox = a("#sbsw-lightbox"), this.$overlay = a("#sbsw-lightboxOverlay"), this.$outerContainer = this.$lightbox.find(".sbsw-lb-outerContainer"), this.$container = this.$lightbox.find(".sbsw-lb-container"), this.containerTopPadding = parseInt(this.$container.css("padding-top"), 10), this.containerRightPadding = parseInt(this.$container.css("padding-right"), 10), this.containerBottomPadding = parseInt(this.$container.css("padding-bottom"), 10), this.containerLeftPadding = parseInt(this.$container.css("padding-left"), 10), this.$overlay.hide().on("click", function() {
                                    return b.end(), !1
                                }), jQuery(document).on('click', function(event, b, c) {
                                    //Fade out the lightbox if click anywhere outside of the two elements defined below
                                    if (!jQuery(event.target).closest('.sbsw-lb-outerContainer').length) {
                                        if (!jQuery(event.target).closest('.sbsw-lb-dataContainer').length) {
                                            //Fade out lightbox
                                            lbBuilder.pausePlayer();
                                            lbBuilder.afterSlideChange();

                                            jQuery('#sbsw-lightboxOverlay, #sbsw-lightbox').fadeOut();
                                        }
                                    }
                                }), this.$lightbox.hide(),
                                    jQuery('#sbsw-lightboxOverlay').on("click", function(c) {
                                        lbBuilder.pausePlayer();
                                        lbBuilder.afterSlideChange();

                                        return "sbsw-lightbox" === a(c.target).attr("id") && b.end(), !1
                                    }), this.$lightbox.find(".sbsw-lb-prev").on("click", function() {
                                    lbBuilder.pausePlayer();
                                    lbBuilder.afterSlideChange();

                                    return b.changeImage(0 === b.currentImageIndex ? b.album.length - 1 : b.currentImageIndex - 1), !1
                                }), this.$lightbox.find(".sbsw-lb-container").hammer().on("swiperight", function() {
                                    lbBuilder.pausePlayer();
                                    lbBuilder.afterSlideChange();

                                    return b.changeImage(0 === b.currentImageIndex ? b.album.length - 1 : b.currentImageIndex - 1), !1
                                }), this.$lightbox.find(".sbsw-lb-next").on("click", function() {
                                    lbBuilder.pausePlayer();
                                    lbBuilder.afterSlideChange();

                                    return b.changeImage(b.currentImageIndex === b.album.length - 1 ? 0 : b.currentImageIndex + 1), !1
                                }), this.$lightbox.find(".sbsw-lb-container").hammer().on("swipeleft", function() {
                                    lbBuilder.pausePlayer();
                                    lbBuilder.afterSlideChange();

                                    return b.changeImage(b.currentImageIndex === b.album.length - 1 ? 0 : b.currentImageIndex + 1), !1
                                }), this.$lightbox.find(".sbsw-lb-loader, .sbsw-lb-close").on("click", function() {

                                    lbBuilder.pausePlayer();
                                    lbBuilder.afterSlideChange();

                                    return b.end(), !1
                                })
                            }, b.prototype.start = function(b) {
                                function c(a) {
                                    d.album.push(lbBuilder.getData(a))
                                }
                                var d = this,
                                    e = a(window);
                                e.on("resize", a.proxy(this.sizeOverlay, this)), a("select, object, embed").css({
                                    visibility: "hidden"
                                }), this.sizeOverlay(), this.album = [];
                                var f, g = 0,
                                    h = b.attr("data-sbsw-lightbox");
                                if (h) {
                                    f = a(b.prop("tagName") + '[data-sbsw-lightbox="' + h + '"]');
                                    for (var i = 0; i < f.length; i = ++i) c(a(f[i])), f[i] === b[0] && (g = i)
                                } else if ("lightbox" === b.attr("rel")) c(b);
                                else {
                                    f = a(b.prop("tagName") + '[rel="' + b.attr("rel") + '"]');
                                    for (var j = 0; j < f.length; j = ++j) c(a(f[j])), f[j] === b[0] && (g = j)
                                }
                                var k = e.scrollTop() + this.options.positionFromTop,
                                    l = e.scrollLeft();
                                this.$lightbox.css({
                                    top: k + "px",
                                    left: l + "px"
                                }).fadeIn(this.options.fadeDuration), this.changeImage(g)
                            }, b.prototype.changeImage = function(b) {
                                var c = this;
                                this.disableKeyboardNav();
                                var d = this.$lightbox.find(".sbsw-lb-image");
                                this.$overlay.fadeIn(this.options.fadeDuration), a(".sbsw-lb-loader").fadeIn("slow"), this.$lightbox.find(".sbsw-lb-image, .sbsw-lb-nav, .sbsw-lb-prev, .sbsw-lb-next, .sbsw-lb-dataContainer, .sbsw-lb-numbers, .sbsw-lb-caption").hide(), this.$outerContainer.addClass("animating");
                                var e = new Image;
                                e.onload = function() {
                                    var f, g, h, i, j, k, l;
                                    var sbswArrowWidth = 0,
                                        sbswCommentsWidth = 0;
                                    if (window.innerWidth > 640) {
                                        sbswArrowWidth = 100;
                                        sbswCommentsWidth = 300;
                                    }
                                    d.attr("src", c.album[b].link), f = a(e), d.width(e.width), d.height(e.height), c.options.fitImagesInViewport && (l = a(window).width(), k = a(window).height(), j = l - c.containerLeftPadding - c.containerRightPadding - 20 - sbswCommentsWidth - sbswArrowWidth, i = k - c.containerTopPadding - c.containerBottomPadding - 150, (e.width > j || e.height > i) && (e.width / j > e.height / i ? (h = j, g = parseInt(e.height / (e.width / h), 10), d.width(h), d.height(g)) : (g = i, h = parseInt(e.width / (e.height / g), 10), d.width(h), d.height(g)))), c.sizeContainer(d.width(), d.height())
                                }, e.src = this.album[b].link, this.currentImageIndex = b
                            }, b.prototype.sizeOverlay = function() {
                                this.$overlay.width(a(window).width()).height(a(document).height())
                            }, b.prototype.sizeContainer = function(a, b) {
                                function c() {
                                    d.$lightbox.find(".sbsw-lb-dataContainer").width(g), d.$lightbox.find(".sbsw-lb-prevLink").height(h), d.$lightbox.find(".sbsw-lb-nextLink").height(h), d.showImage()
                                }
                                var d = this,
                                    e = this.$outerContainer.outerWidth(),
                                    f = this.$outerContainer.outerHeight(),
                                    g = a + this.containerLeftPadding + this.containerRightPadding,
                                    h = b + this.containerTopPadding + this.containerBottomPadding;
                                e !== g || f !== h ? this.$outerContainer.animate({
                                    width: g,
                                    height: h
                                }, this.options.resizeDuration, "swing", function() {
                                    c()
                                }) : c()
                            }, b.prototype.showImage = function() {
                                this.$lightbox.find(".sbsw-lb-loader").hide(), this.$lightbox.find(".sbsw-lb-image").fadeIn("slow"), this.updateNav(), this.updateDetails(), this.preloadNeighboringImages(), this.enableKeyboardNav()
                            }, b.prototype.updateNav = function() {
                                var a = !1;
                                try {
                                    document.createEvent("TouchEvent"), a = this.options.alwaysShowNavOnTouchDevices ? !0 : !1
                                } catch (b) {}
                                this.$lightbox.find(".sbsw-lb-nav").show(), this.album.length > 1 && (this.options.wrapAround ? (a && this.$lightbox.find(".sbsw-lb-prev, .sbsw-lb-next").css("opacity", "1"), this.$lightbox.find(".sbsw-lb-prev, .sbsw-lb-next").show()) : (this.currentImageIndex > 0 && (this.$lightbox.find(".sbsw-lb-prev").show(), a && this.$lightbox.find(".sbsw-lb-prev").css("opacity", "1")), this.currentImageIndex < this.album.length - 1 && (this.$lightbox.find(".sbsw-lb-next").show(), a && this.$lightbox.find(".sbsw-lb-next").css("opacity", "1"))))
                            }, b.prototype.updateDetails = function() {
                                var b = this;

                                /** NEW PHOTO ACTION **/
                                if (jQuery('iframe.sbsw-lb-player-loaded').length) {
                                    jQuery('.sbsw-lb-player-placeholder').replaceWith(jQuery('iframe.sbsw-lb-player-loaded'));
                                    jQuery('iframe.sbsw-lb-player-loaded').removeClass('sbsw-lb-player-loaded').show();
                                }
                                //Switch video when either a new popup or navigating to new one
                                var feed = window.sbsw.feeds[this.album[this.currentImageIndex].feedIndex];
                                lbBuilder.beforePlayerSetup(this.$lightbox,this.album[this.currentImageIndex],this.currentImageIndex,this.album,feed);

                                if( sbsw_supports_video() ){
                                    if (this.album[this.currentImageIndex].mediaType === 'youtube'){
                                        var playerID = 'sbsw-lb-player';
                                        jQuery('#sbsw-lightbox').addClass('sbsw-video-lightbox');

                                        var videoID = this.album[this.currentImageIndex].mediaURL;
                                        if (typeof window.sbswLightboxPlayer === 'undefined') {
                                            var args = {
                                                host: window.location.protocol + '//www.youtube-nocookie.com',
                                                videoId: videoID,
                                                playerVars: {
                                                    modestbranding: 1,
                                                    rel: 0,
                                                    autoplay: true
                                                }
                                            };
                                            window.sbswLightboxPlayer = new window.YT.Player(playerID, args);
                                        } else {
                                            window.sbswLightboxPlayer.loadVideoById(videoID);
                                        }

                                    } else if (this.album[this.currentImageIndex].mediaType === 'video') {
                                        if (!$('.sbsw-lb-media').find('video').length) {
                                            $('.sbsw-lb-media').append('<video class="sbsw-lb-video" src="'+this.album[this.currentImageIndex].mediaURL+'" poster="" controls></video>')
                                        } else {
                                            $('.sbsw-lb-media').find('.sbsw-lb-video').attr('src',this.album[this.currentImageIndex].mediaURL);
                                        }
                                        /*if( this.album[this.currentImageIndex].video.length ){
                                            jQuery('#ctf_lightbox').addClass('ctf_video_lightbox');
                                            jQuery('video.ctf_video').attr({
                                                'src' : this.album[this.currentImageIndex].video,
                                                'poster' : this.album[this.currentImageIndex].link,
                                                'autoplay' : 'true'
                                            });
                                        }*/
                                    } else if (this.album[this.currentImageIndex].mediaType === 'iframe') {
                                        if (!$('.sbsw-lb-media').find('iframe.sbsw-lb-iframe').length) {
                                            $('.sbsw-lb-media').append('<iframe class="sbsw-lb-iframe" title="Placeholder for videos in the lightbox" type="text/html" src="'+this.album[this.currentImageIndex].mediaURL+'" allowfullscreen webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>')
                                        } else {
                                            $('.sbsw-lb-media').find('.sbsw-lb-iframe').attr('src',this.album[this.currentImageIndex].mediaURL);
                                        }
                                        /*if( this.album[this.currentImageIndex].video.length ){
                                            jQuery('#ctf_lightbox').addClass('ctf_video_lightbox');
                                            jQuery('video.ctf_video').attr({
                                                'src' : this.album[this.currentImageIndex].video,
                                                'poster' : this.album[this.currentImageIndex].link,
                                                'autoplay' : 'true'
                                            });
                                        }*/
                                    }
                                }

                                if (this.album.length > 1 && this.options.showImageNumberLabel) {
                                    this.$lightbox.find(".sbsw-lb-number").text(this.options.albumLabel(this.currentImageIndex + 1, this.album.length)).fadeIn("fast");
                                } else {
                                    this.$lightbox.find(".sbsw-lb-number").hide();
                                }
                                this.$outerContainer.removeClass("animating");
                                this.$lightbox.find(".sbsw-lb-dataContainer").fadeIn(this.options.resizeDuration, function() {
                                    return b.sizeOverlay()
                                });

                                /*setTimeout(function() {
                                    $('#sbsw-lightbox .sbsw-lb-player').css({
                                        'height' : $('#sbsw-lightbox .sbsw-lb-outerContainer').height()+'px',
                                        'width' : $('#sbsw-lightbox .sbsw-lb-outerContainer').width()+'px',
                                        'top': 0
                                    });
                                },400);*/

                                lbBuilder.afterPlayerSetup(this.$lightbox,this.album[this.currentImageIndex],this.currentImageIndex,this.album);
                            }, b.prototype.preloadNeighboringImages = function() {
                                if (this.album.length > this.currentImageIndex + 1) {
                                    var a = new Image;
                                    a.src = this.album[this.currentImageIndex + 1].link
                                }
                                if (this.currentImageIndex > 0) {
                                    var b = new Image;
                                    b.src = this.album[this.currentImageIndex - 1].link
                                }
                            }, b.prototype.enableKeyboardNav = function() {
                                a(document).on("keyup.keyboard", a.proxy(this.keyboardAction, this))
                            }, b.prototype.disableKeyboardNav = function() {
                                a(document).off(".keyboard")
                            }, b.prototype.keyboardAction = function(a) {

                                var KEYCODE_ESC        = 27;
                                var KEYCODE_LEFTARROW  = 37;
                                var KEYCODE_RIGHTARROW = 39;

                                var keycode = event.keyCode;
                                var key     = String.fromCharCode(keycode).toLowerCase();
                                if (keycode === KEYCODE_ESC || key.match(/x|o|c/)) {
                                    if( sbsw_supports_video() ) $('#sbsw-lightbox video.sbsw_video')[0].pause();
                                    $('#sbsw-lightbox iframe').attr('src', '');
                                    this.end();
                                } else if (key === 'p' || keycode === KEYCODE_LEFTARROW) {
                                    if (this.currentImageIndex !== 0) {
                                        this.changeImage(this.currentImageIndex - 1);
                                    } else if (this.options.wrapAround && this.album.length > 1) {
                                        this.changeImage(this.album.length - 1);
                                    }

                                    if( sbsw_supports_video() ) $('#sbsw-lightbox video.sbsw-lb-video')[0].pause();
                                    $('#sbsw-lightbox iframe').attr('src', '');

                                } else if (key === 'n' || keycode === KEYCODE_RIGHTARROW) {
                                    if (this.currentImageIndex !== this.album.length - 1) {
                                        this.changeImage(this.currentImageIndex + 1);
                                    } else if (this.options.wrapAround && this.album.length > 1) {
                                        this.changeImage(0);
                                    }
                                    lbBuilder.pausePlayer();
                                    lbBuilder.afterSlideChange();
                                }

                            }, b.prototype.end = function() {
                                this.disableKeyboardNav(), a(window).off("resize", this.sizeOverlay), this.$lightbox.fadeOut(this.options.fadeDuration), this.$overlay.fadeOut(this.options.fadeDuration), a("select, object, embed").css({
                                    visibility: "visible"
                                })
                            }, b
                        }();
                    a(function() {
                        {
                            var a = new b;
                            new c(a)

                            //Lightbox hide photo function
                            $('.sbsw-lightbox_action a').unbind().bind('click', function(){
                                $(this).parent().find('.sbsw-lightbox_tooltip').toggle();
                            });
                        }
                    })
                }).call(this);
            },
            createFeeds: function (args) {
                window.sbsw.createLightbox();
                args.whenFeedsCreated(
                    $('.sb-wall').each(function (index) {
                        $(this).attr('data-sbsw-index', index + 1);
                        var $self = $(this),
                            flags = typeof $self.attr('data-sbsw-flags') !== 'undefined' ? $self.attr('data-sbsw-flags').split(',') : [],
                            general = typeof $self.attr('data-options') !== 'undefined' ? JSON.parse($self.attr('data-options')) : {};
                        var feedOptions = {
                            cols : general.cols,
                            colsmobile : general.colsmobile,
                            //num : $self.attr('data-num'),
                            //imgRes : $self.attr('data-res'),
                            feedID : $self.attr('data-feedid'),
                            shortCodeAtts : $self.attr('data-shortcode-atts'),
                            backgroundCheck : (flags.indexOf('background') > -1),
                            ifResizingEnabled : (flags.indexOf('ifResizeDisable') === -1),
                            fbResizingEnabled : (flags.indexOf('fbResizeDisable') === -1),
                            twResizingEnabled : (flags.indexOf('twResizeDisable') === -1),
                            //imageLoadEnabled : (flags.indexOf('imageLoadDisable') === -1),
                            //debugEnabled : (flags.indexOf('debug') > -1),
                            //favorLocal : (flags.indexOf('favorLocal') > -1),
                            //ajaxPostLoad : (flags.indexOf('ajaxPostLoad') > -1),
                            //checkWPPosts : (flags.indexOf('checkWPPosts') > -1),
                            //singleCheckPosts : (flags.indexOf('singleCheckPosts') > -1),
                            //narrowPlayer : (flags.indexOf('narrowPlayer') > -1),
                            lightboxEnabled : true,
                            autoMinRes : 1,
                            general : general
                        };

                        window.sbsw.feeds[index] = sbswGetNewFeed(this, index, feedOptions);
                        if (typeof window.sbswAPIReady !== 'undefined') {
                            window.sbsw.feeds[index].playerAPIReady = true;
                        }
                        window.sbsw.feeds[index].twitterCardHandler = new SbTwitterCardHandler('sbsw',$self);
                        window.sbsw.feeds[index].init();

                        window.sbsw.filterBars = {};
                        window.sbsw.filterBars[index] = new SbFilterBar(window.sbsw.feeds[index]);
                        window.sbsw.filterBars[index].init();

                        var evt = jQuery.Event('sbyafterfeedcreate');
                        evt.feed = window.sbsw.feeds[index];
                        jQuery(window).trigger(evt);

                    })
                );
            },
            afterFeedsCreated: function () {
                if (window.sbswAPIReady) {
                    var evt = jQuery.Event('sbyfeedandytready');
                    jQuery(window).trigger(evt);
                }

            },
            encodeHTML: function(raw) {
                // make sure passed variable is defined
                if (typeof raw === 'undefined') {
                    return '';
                }
                // replace greater than and less than symbols with html entity to disallow html in comments
                var encoded = raw.replace(/(>)/g,'&gt;'),
                    encoded = encoded.replace(/(<)/g,'&lt;');
                encoded = encoded.replace(/(&lt;br\/&gt;)/g,'<br>');
                encoded = encoded.replace(/(&lt;br&gt;)/g,'<br>');

                return encoded;
            },
            urlDetect: function(text) {
                var urlRegex = /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g;
                return text.match(urlRegex);
            },
            numberFormat: function(text) {
                return text.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
            }
        };

        function SbswFeed(el, index, settings) {
            this.el = el;
            this.index = index;
            this.settings = settings;
            this.placeholderURL = window.sbsw.options.placeholder;
            this.consentGiven = true;
            this.players = {};
            this.minImageWidth = 0;
            this.imageResolution = 150;
            this.resizedImages = {};
            this.needsResizing = {instagram:[],facebook:[],twitter:[]};
            this.outOfPages = false;
            this.isInitialized = false;
            this.itemsToCheck = {
                'youtube' : [],
                'instagram' : [],
                'twitter' : {cards:[],resize:[]},
                'facebook' : []
            }
            this.page = 1;
            this.imagesWaiting = 0;
        }

        SbswFeed.prototype = {
            init: function() {
                var feed = this;
                if ($(this.el).find('#sbsw_mod_error').length) {
                    $(this.el).prepend($(this.el).find('#sbsw_mod_error'));
                }
                feed.setResizedImages();
                if (this.settings.ajaxPostLoad) {
                    this.getNewPostSet();
                } else {
                    this.afterInitialImagesLoaded();
                }
                var sbsw_delay = (function () {
                    var sbsw_timer = 0;
                    return function (sbsw_callback, sbsw_ms) {
                        clearTimeout(sbsw_timer);
                        sbsw_timer = setTimeout(sbsw_callback, sbsw_ms);
                    };
                })();
                jQuery(window).resize(function () {
                    sbsw_delay(function () {
                        feed.afterResize();
                    }, 500);
                });

                $(this.el).find('.sbsw-item').each(function() {
                    feed.lazyLoadCheck($(this));
                });
            },
            initLayout: function() {
                var $self = $(this.el),
                    feed = this,
                    cols = feed.settings.general.cols,
                    colsmobile = feed.settings.general.colsmobile;

                var layout = 'list';
                if (typeof feed.settings.general.masonry !== 'undefined') {
                    layout = 'masonry';
                } else if (typeof feed.settings.general.carousel !== 'undefined') {
                    layout = 'carousel';
                }
                this.layout = layout;

                if (layout === 'masonry') {
                    //Disable mobile layout
                    if ($self.hasClass('sbsw-mobcol-auto')) {
                        itemsTabletSmall = 2;
                        if (parseInt(cols) != 2) itemsMobile = 1;
                        if (parseInt(cols) == 2) itemsMobile = 2; //If the cols are set to 2 then don't change to 1 col on mobile
                    } else {
                        itemsMobile = colsmobile;
                    }
                    $self.addClass('sbsw-masonry');
                    this.settings.autoMinRes = 320;
                    if ($self.find('.sb-wall-items-wrap').data('smashotope')) {
                        $self.find('.sb-wall-items-wrap').smashotope('layout');
                    }
                    this.isotopeArgs = {
                        itemSelector: '.sbsw-item',
                        layoutMode: 'packery',
                        transitionDuration: 0,
                        // options...
                        resizable: false // disable normal resizing
                    }
                } else if (layout === 'carousel') {
                    var cols = this.settings.cols,
                        colsmobile = this.settings.colsmobile;

                    $self.find('.sb-wall-items-wrap').addClass('sbsw-carousel');
                    $self.find('.sby_load_btn').remove();
                    $self.find('.sbsw-item').css({
                        'padding-top' : feed.settings.general.itemspacing,
                        'padding-right' : feed.settings.general.itemspacing,
                        'padding-bottom' : feed.settings.general.itemspacing,
                        'padding-left' : feed.settings.general.itemspacing
                    });
                    $self.find('.sbsw-item').each(function() {
                        $(this).attr('style',$(this).attr('style').replace('padding: '+$self.find('.sb-wall-items-wrap').css('padding-top'),'padding: '+$self.find('.sb-wall-items-wrap').css('padding-top') + ' !important'));
                    });

                    var arrows = feed.settings.general.carousel[0],
                        pagination = feed.settings.general.carousel[1],
                        autoplay = feed.settings.general.carousel[2],
                        time = feed.settings.general.carousel[3],
                        loop = feed.settings.general.carousel[4],
                        rows = feed.settings.general.carousel[5];
                    //Initiate carousel
                    if (!autoplay) time = false;
                    //Set defaults for responsive breakpoints
                    var itemsTabletSmall = cols,
                        itemsMobile = cols,
                        arrows = arrows ? 'onhover' : 'hide',
                        autoplay = time !== false,
                        has2rows = (rows == 2),
                        loop = (!loop),
                        onChange = function() {
                            setTimeout(function(){
                                feed.afterResize();
                            }, 250);
                        },
                        afterInit = function() {
                            var $self = jQuery(feed.el);
                            $self.find('.sb-wall-items-wrap.sbsw-carousel').fadeIn();
                            setTimeout(function(){
                                $self.find('.sb-wall-items-wrap.sbsw-carousel .sbsw-info, .sbsw-owl2row-item,.sb-wall-items-wrap.sbsw-carousel').fadeIn();

                            }, 50);
                            setTimeout(function(){
                                var $navElementsWrapper = $self.find('.sbsw-owl-nav');
                                if (arrows === 'onhover') {

                                } else if (arrows === 'below') {
                                    var $dots = $self.find('.sbsw-owl-dots'),
                                        $prev = $self.find('.sbsw-owl-prev'),
                                        $next = $self.find('.sbsw-owl-next'),
                                        $nav = $self.find('.sbsw-owl-nav'),
                                        $dot = $self.find('.sbsw-owl-dot'),
                                        widthDots = $dot.length * $dot.innerWidth(),
                                        maxWidth = $self.innerWidth();

                                    $prev.after($dots);

                                    $nav.css('position', 'relative');
                                    $next.css('position', 'absolute').css('top', '-6px').css('right', Math.max((.5 * $nav.innerWidth() - .5 * (widthDots) - $next.innerWidth() - 6), 0));
                                    $prev.css('position', 'absolute').css('top', '-6px').css('left', Math.max((.5 * $nav.innerWidth() - .5 * (widthDots) - $prev.innerWidth() - 6), 0));
                                } else if (arrows === 'hide') {
                                    $navElementsWrapper.addClass('hide').hide();
                                }
                            }, 100);
                        };
                    //Disable mobile layout
                    if( $self.hasClass('sbsw-mob-col-auto') ) {
                        itemsTabletSmall = 2;
                        if( parseInt(cols) != 2 ) itemsMobile = 1;
                        if( parseInt(cols) == 2 ) itemsMobile = 2; //If the cols are set to 2 then don't change to 1 col on mobile
                    } else {
                        itemsMobile = colsmobile;
                    }

                    this.carouselArgs = {
                        items: cols,
                        loop: loop,
                        rewind: !loop,
                        autoplay: autoplay,
                        autoplayTimeout: Math.max(time,2000),
                        autoplayHoverPause: true,
                        nav: true,
                        navText: ['<svg class="svg-inline--fa fa-chevron-left fa-w-10" aria-hidden="true" data-fa-processed="" data-prefix="fa" data-icon="chevron-left" role="presentation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M34.52 239.03L228.87 44.69c9.37-9.37 24.57-9.37 33.94 0l22.67 22.67c9.36 9.36 9.37 24.52.04 33.9L131.49 256l154.02 154.75c9.34 9.38 9.32 24.54-.04 33.9l-22.67 22.67c-9.37 9.37-24.57 9.37-33.94 0L34.52 272.97c-9.37-9.37-9.37-24.57 0-33.94z"></path></svg>', '<svg class="svg-inline--fa fa-chevron-right fa-w-10" aria-hidden="true" data-fa-processed="" data-prefix="fa" data-icon="chevron-right" role="presentation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path fill="currentColor" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"></path></svg>'],
                        dots: pagination,
                        owl2row: has2rows,
                        responsive: {
                            0: {
                                items: itemsMobile
                            },
                            480: {
                                items: itemsTabletSmall
                            },
                            640: {
                                items: cols
                            }
                        },
                        onChange: onChange,
                        onInitialize: afterInit
                    };
                }

                var evt = jQuery.Event('sbswafterlayoutinit');
                evt.feed = this;
                jQuery(window).trigger(evt);

                if (layout === 'masonry') {
                    feed.setMasonry();
                    feed.smashotopeInit();
                    setTimeout(function(){ feed.setMasonry(); feed.smashotopeInit(); }, 500);
                }
            },
            setResizedImages: function() {
                if ($(this.el).find('.sbsw-resized-image-data').length
                    && typeof $(this.el).find('.sbsw-resized-image-data').attr('data-resized') !== 'undefined'
                    && ($(this.el).find('.sbsw-resized-image-data').attr('data-resized').indexOf('{"') === 0 || $(this.el).find('.sbsw-resized-image-data').attr('data-resized').indexOf('[') === 0)) {
                    var rawResized = JSON.parse($(this.el).find('.sbsw-resized-image-data').attr('data-resized')),
                        organizedResized = {
                            facebook: {},
                            instagram: {},
                            twitter: {},
                        };

                    if (typeof rawResized.facebook !== 'undefined') {
                        $.each(rawResized.facebook,function(index,value) {
                            organizedResized.facebook[value.facebook_id] = {
                                id: value.media_id,
                                sizes: JSON.parse(value.sizes),
                            }
                        });
                    }
                    if (typeof rawResized.instagram !== 'undefined') {
                        organizedResized.instagram = rawResized.instagram
                    }
                    if (typeof rawResized.twitter !== 'undefined') {
                        $.each(rawResized.twitter,function(index,value) {
                            organizedResized.twitter[value.twitter_id] = {
                                id: value.media_id,
                                sizes: JSON.parse(value.sizes),
                            }
                        });
                    }
                    this.resizedImages = organizedResized;
                    //window.sbsw.feeds[0]
                } else {
                    this.resizedImages = {
                        facebook: {},
                        instagram: {}
                    };
                }
                $(this.el).find('.cff_resized_image_data').remove();
            },
            afterInitialImagesLoaded: function() {
                this.initLayout();
                this.loadMoreButtonInit();
                this.hideExtraItemsForWidth();
                this.beforeNewImagesRevealed();
                this.revealNewImages();
                this.afterNewImagesRevealed();
                this.afterFeedSet();
                this.sizeItems();
            },
            afterResize: function() {
                this.setImageHeight();
                this.setImageResolution();
                this.maybeRaiseImageResolution();
                this.twitterCardHandler.maybeRaiseResolution();
                this.setImageSizeClass();
                this.sizeItems();
            },
            afterLoadMoreClicked: function($button) {
                $button.addClass('sbsw-loading').append('<div class="sbsw-loader"></div>');
                $button.find('.sbsw-loader').css('background-color', $button.css('color'));
                $button.closest('.sb-wall').find('.sbsw-num-diff-hide').addClass('sbsw-transition').removeClass('sbsw-num-diff-hide');
            },
            afterNewImagesLoaded: function() {
                var $self = $(this.el),
                    feed = this;
                this.beforeNewImagesRevealed();
                this.revealNewImages();
                this.afterNewImagesRevealed();
                this.sizeItems();
                setTimeout(function () {
                    //Hide the loader in the load more button
                    $self.find('.sbsw_loader').addClass('sbsw_hidden');
                    $self.find('.sbsw_btn_text').removeClass('sbsw_hidden');
                    feed.maybeRaiseImageResolution();
                    feed.twitterCardHandler.maybeRaiseResolution();
                    feed.setImageHeight();
                }, 500);
            },
            beforeNewImagesRevealed: function() {
                this.maybeRaiseImageResolution(true);
                this.twitterCardHandler.maybeRaiseResolution();
                this.setImageSizeClass();
            },
            afterFeedSet: function() {
                if (typeof this.carouselArgs !== 'undefined' ) {
                    $(this.el).find('.sbsw-carousel').sbswOwlCarousel(this.carouselArgs);
                    if (parseInt(this.settings.general.carousel[5]) === 2) {
                        $(this.el).addClass('sbsw-carousel-2-row');
                    }
                }
            },
            processNewImage: function($item) {
                var feed = this;
                $item.find('img').each(function() {
                    if (!$(this).prop('complete')) {
                        feed.imagesWaiting++;
                        $(this).addClass('sbsw-image-loading').on('load',function() {
                            $(this).removeClass('sbsw-image-loading');
                            feed.imagesWaiting--;
                            if (feed.imagesWaiting < 1) {
                                feed.afterResize();
                            }
                        });
                        $(this).on('error',function() {
                            $(this).removeClass('sbsw-image-loading');
                            feed.imagesWaiting--;
                            if (feed.imagesWaiting < 1) {
                                feed.afterResize();
                            }
                        });
                    }
                });
            },
            sizeItems: function() {
                var $self = $(this.el),
                    feed = this;

                $self.find('.sbsw-youtube-item .sbsw-item-media').each(function() {
                    var thumbWidth = $(this).innerWidth(),
                        newThumbHeight = Math.floor(thumbWidth * 9 / 16);
                    $(this).css('height',newThumbHeight+'px')
                    if (!$(this).hasClass('sbsw_imgLiquid_ready')) {
                        $(this).sbsw_imgLiquid({fill: true});
                    }
                });
            },
            initShowMore: function($self) {
                var feed = this;

                if ($self.find('.sbsw-more').length === 0) {
                    return;
                }
                if ($self.find('.sbsw-remaining').html().trim() === '') {
                    $self.find('.sbsw-more, .sbsw-remaining').remove();
                }
                $self.find('.sbsw-more').click(function(event) {
                    event.preventDefault();
                    $(this).hide();
                    $self.find('.sbsw-remaining').show();
                    feed.afterResize();
                });
            },
            revealNewImages: function() {
                var $self = $(this.el),
                    feed = this;

                this.applyImageLiquid();
                // Call Custom JS if it exists
                if (typeof sbswCustomJS == 'function') setTimeout(function(){ sbswCustomJS(); }, 100);
                $self.find('.sbsw-screenreader').find('img').remove();
                $self.find('.sbsw-item.sbsw-new').each(function (index) {
                    var $self = $(this);

                    if ($(this).hasClass('sbsw-youtube-item')) {
                        feed.itemsToCheck.youtube.push($(this).attr('id').replace('sbsw-',''));
                    } else if ($(this).hasClass('sbsw-twitter-item')) {
                        if ($self.find('.ctf-twitter-card-placeholder').length) {
                            var id = $self.find('.ctf-twitter-card-placeholder').attr('data-tweet-id'),
                                url = $self.find('.ctf-twitter-card-placeholder').attr('data-tc-url');
                            feed.itemsToCheck.twitter.cards.push({id:id,url:url});
                        }
                    }
                    if ($self.find('.sbsw-content-text').length) {
                        $self.find('.sbsw-content-text').find('.emoji').each(function() {
                            $(this).replaceWith($(this).attr('alt'));
                        });
                        $self.find('.sbsw-content-text').html(sbyLinkify($self.find('.sbsw-content-text').html()));
                    }

                    if ($self.find('.sbsw-quoted-tweet-text').length) {
                        $self.find('.sbsw-quoted-tweet-text').find('.emoji').each(function() {
                            $(this).replaceWith($(this).attr('alt'));
                        });
                        $self.find('.sbsw-quoted-tweet-text').html(sbyLinkify($self.find('.sbsw-quoted-tweet-text').html()));
                    }
                    feed.initShowMore($self);

                    feed.twitterCardHandler.handleImageError($self);

                    // lightbox
                    if (feed.settings.lightboxEnabled) {
                        $self.find('.sbsw-lightbox-hover').attr('data-sbsw-lightbox',feed.index);
                        if ($self.hasClass('sbsw-youtube-item')) {
                            $self.find('.sbsw-lightbox-hover').attr('href',sbsw.options.lightboxPlaceholder);
                        }
                    }
                    feed.afterItemRevealed($self);

                    $self.find('.sbsw-item-share a').click(function() {

                        var $shareLink = $(this);

                        if( $shareLink.hasClass('sbsw-open') ){
                            $self.find('.sbsw-share-popup').remove();
                            $shareLink.removeClass('sbsw-open');
                        } else {
                            $(this).before(sbswShareHTML($self));
                            $shareLink.addClass('sbsw-open');

                            var time = 0;
                            $self.find('.sbsw-share-popup').find('a').each(function() {
                                var $cffShareIcon = jQuery(this);
                                setTimeout( function(){
                                    $cffShareIcon.addClass('sbsw-show');
                                }, time);
                                time += 20;
                            });

                        }
                    });
                    //Close share tooltip if click anywhere on page
                    $(document).click(function() {
                        jQuery('.sbsw-share-popup').remove();
                        jQuery('.sbsw-item-share a').removeClass('sbsw-open');
                    });
                    //Stop propagation on share link itself otherwise it won't open
                    $(".sbsw-item-share a").click(function(e) {
                        e.stopPropagation();
                        return false;
                    });
                }); //End .sbsw-item each

                //Remove the new class after 500ms, once the sorting is done
                setTimeout(function () {
                    $self.find('.sbsw-item.sbsw-new').removeClass('sbsw-new');
                    //Loop through items and remove class to reveal them
                    var time = 10,
                        num = $self.find('.sbsw-transition').length;
                    $self.find('.sbsw-transition').each(function(index) {
                        var $sbsw_item_transition_el = jQuery(this);

                        setTimeout( function(){
                            $sbsw_item_transition_el.removeClass('sbsw-transition');
                        }, time);
                        time += 10;
                    });
                }, 500);
            },
            afterItemRevealed: function() {
            },
            setItemsToCheck: function() {
                if (this.settings.ifResizingEnabled && this.needsResizing.instagram.length > 0) {
                    this.itemsToCheck.instagram = this.needsResizing.instagram;
                }
                if (this.settings.fbResizingEnabled && this.needsResizing.facebook.length > 0) {
                    this.itemsToCheck.facebook = this.needsResizing.facebook;
                }
                if (this.settings.twResizingEnabled && this.needsResizing.twitter.length > 0) {
                    this.itemsToCheck.twitter.resize = this.needsResizing.twitter;
                }
            },
            getMaxResImage: function(imgSrcSet) {
                var largest = 0,
                    largestImage = false;
                $.each(imgSrcSet, function (index, value) {
                    if (index > largest) {
                        largest = index;
                        largestImage = value;
                    }
                });

                return largestImage;
            },
            lazyLoadCheck: function($item){
                var feed = this;
                if ($item.find('.sbsw-item-media').length && !$item.closest('.sb-wall').hasClass('sbsw-no-ll-check')) {
                    var imgSrcSet = feed.getImageUrls($item),
                        maxResImage = feed.getMaxResImage(imgSrcSet);

                    $item.find('.sbsw-item-media img').each(function() {
                        if (maxResImage && typeof $(this).attr('data-src') !== 'undefined') {
                            $(this).attr('data-src',maxResImage);
                        }
                        if (maxResImage && typeof $(this).attr('data-orig-src') !== 'undefined') {
                            $(this).attr('data-orig-src',maxResImage);
                        }
                        $(this).on('load',function() {
                            if (!$(this).hasClass('sbsw-replaced')
                                && $(this).attr('src').indexOf('placeholder') > -1) {
                                $(this).closest('.sbsw-item-media').attr('data-current',1000);
                                $(this).addClass('sbsw-replaced');
                                if (maxResImage) {
                                    $(this).attr('src',maxResImage);
                                    if ($(this).closest('.imgLiquid_bgSize').length) {
                                        $(this).closest('.imgLiquid_bgSize').css('background-image', 'url(' + maxResImage + ')');
                                    }
                                }
                            }
                        });
                    });
                }
            },
            afterNewImagesRevealed: function() {
                this.listenForVisibilityChange();
                if (!this.settings.imageLoadEnabled) {
                    $('.sbsw_no_resraise').removeClass('sbsw_no_resraise');
                }
                this.setItemsToCheck();
                this.sendDataForBackgroundProcesses();

                var evt = $.Event('sbyafterimagesloaded');
                evt.el = $(this.el);
                $(window).trigger(evt);
            },
            sendDataForBackgroundProcesses: function() {
                var feed = this;

                feed.settings.fbResizingEnabled = false;
                feed.settings.ifResizingEnabled = false;
                feed.settings.twResizingEnabled = false;
                if (feed.settings.backgroundCheck
                    || feed.itemsToCheck.twitter.resize.length > 0
                    || feed.itemsToCheck.twitter.cards.length > 0
                    || feed.itemsToCheck.instagram.length > 0
                    || feed.itemsToCheck.facebook.length > 0
                    || $(feed.el).find('.sbsw-need-counts').length) {
                    var feedID = typeof feed.settings.feedID !== 'undefined' ? feed.settings.feedID : 'sbsw_single',
                        posts = feed.itemsToCheck
                    var submitData = {
                        action: 'sbsw_background_processing',
                        feed_id: feedID,
                        atts: feed.settings.shortCodeAtts,
                        offset: $(this.el).find('.sbsw-item').length,
                        posts: posts
                    };
                    var onSuccess = function(data) {
                        if (data.trim().indexOf('{') === 0) {
                            var response = JSON.parse(data);
                            if (feed.settings.debugEnabled) {
                                console.log(response);
                            }
                            feed.afterBackgroundProcessing(response);

                        }
                    };
                    sbswAjax(submitData,onSuccess);
                }
            },
            afterBackgroundProcessing: function(response) {
                var feed = this,
                    $feedEl = $(this.el);
                $feedEl.find('.sbsw-youtube-item').each(function() {
                    var vidId = $(this).attr('id').replace('sbsw-','');
                    if (typeof response.youtube[vidId] !== 'undefined') {
                        $(this).find('.sbsw-item-stats span').each(function() {
                            var $svg = $(this).find('svg'),
                                stat = response.youtube[vidId].sby_view_count;
                            if (stat.indexOf(' ') > 0) {
                                stat = stat.split(' ')[0];
                            }
                            if ($(this).hasClass('sbsw-sby-likes')) {
                                stat = response.youtube[vidId].sby_like_count;
                            } else if ($(this).hasClass('sbsw-sby-comments')) {
                                stat = response.youtube[vidId].sby_comment_count;
                            }

                            $(this).html(stat).prepend($svg);
                        });
                    }
                });

                $feedEl.find('.sbsw-twitter-item').each(function() {
                    var $self = $(this),
                        $placeholder = $(this).find('.ctf-twitter-card-placeholder'),
                        id = $placeholder.attr('data-tweet-id'),
                        url = $placeholder.attr('data-tc-url');
                    if (typeof response.twitter !== 'undefined'
                        && typeof response.twitter[id] !== 'undefined') {
                        var cardHTML = '';
                        if (typeof response.twitter[id].cards !== 'undefined') {
                            cardHTML = response.twitter[id].cards.html;
                        } else {
                            cardHTML = response.twitter[id].html;
                        }
                        if (cardHTML !== '') {
                            $self.find('.sbsw-item-bottom-content').find('a[href^="'+url+'"]').remove();
                            $self.find('.sbsw-item-bottom-content').html($self.find('.sbsw-item-bottom-content').html().replace(url,''));
                            $self.find('.sbsw-item-bottom-content').append(cardHTML);
                            feed.initShowMore($self);
                        }
                        $placeholder.remove();
                    }
                    feed.processNewImage($self);
                });
                feed.afterResize();

                feed.itemsToCheck.twitter = {cards:[],resize:[]};
                feed.itemsToCheck.youtube = [];
                feed.itemsToCheck.facebook = [];
                feed.itemsToCheck.instagram = [];
            },
            loadMoreButtonInit: function () {
                var $self = $(this.el),
                    feed = this;
                $self.find('.sb-wall-footer .sb-wall-load-btn').off().on('click', function () {
                    feed.afterLoadMoreClicked($(this));
                    if ($('.sbsw-items-wrap-current').hasClass('sbsw-items-wrap-all')) {
                        feed.getNewPostSet();
                    } else {
                        window.sbsw.filterBars[ feed.index ].addRemoteFilteredPosts();
                    }
                });
            },
            getNewPostSet: function () {
                var $self = $(this.el),
                    feed = this;
                var itemOffset = $self.find('.sbsw-item').length,
                    submitData = {
                        action: 'sbsw_load_more_clicked',
                        offset: itemOffset,
                        feed_id: feed.settings.feedID,
                        atts: feed.settings.shortCodeAtts,
                        current_resolution: feed.imageResolution,
                        page: feed.page
                    };
                var onSuccess = function (data) {
                    $self.find('.sb-wall-footer .sb-wall-load-btn').removeClass('sbsw-loading');
                    $self.find('.sbsw-loader').remove();
                    if (data.trim().indexOf('{') === 0) {
                        var response = JSON.parse(data);
                        if (feed.settings.debugEnabled) {
                            console.log(response);
                        }
                        feed.page ++;
                        feed.appendNewPosts(response.html,'all');
                        if (feed.settings.ajaxPostLoad) {
                            feed.settings.ajaxPostLoad = false;
                            feed.afterInitialImagesLoaded();
                        } else {
                            feed.afterNewImagesLoaded();
                        }
                        $('.sbsw_no_js').removeClass('sbsw_no_js');
                        if (response.feedStatus.shouldPaginate === false) {
                            feed.outOfPages = true;
                            $self.find('.sb-wall-load-btn').hide();
                        }
                    }

                };
                sbswAjax(submitData, onSuccess);
            },
            appendNewPosts: function (newPostsHtml,plugin) {
                var $self = $(this.el),
                    feed = this;
                if ($self.find('.sbsw-items-wrap-'+plugin+' .sbsw-item').length) {
                    $self.find('.sbsw-items-wrap-'+plugin+' .sbsw-item').last().after(newPostsHtml);
                } else {
                    $self.find('.sbsw-items-wrap-'+plugin+'').append(newPostsHtml);
                }
                if (feed.layout === 'masonry') {
                    feed.appendSmashotope();
                }
            },
            maybeRaiseSingleImageResolution: function ($item, index, forceChange) {
                var feed = this,
                    $mediaItem = $item.find('.sbsw-item-media'),
                    imgSrcSet = feed.getImageUrls($item),
                    currentUrl = $mediaItem.find('img').attr('src'),
                    currentRes = typeof $mediaItem.attr('data-current') === 'undefined' ? 0 : parseInt( $mediaItem.attr('data-current') ),
                    neededRes = $mediaItem.innerWidth(),
                    forceChange = typeof forceChange !== 'undefined' ? forceChange : false,
                    changeToRes = false;

                if ($item.hasClass('sbsw-no-resraise')) {
                    return;
                }

                if (currentRes < neededRes) {
                    var foundBestRes = false;
                    $.each(imgSrcSet, function (index, value) {
                        if (! foundBestRes && index > currentRes) {
                            changeToRes = index;
                            if (index >= neededRes) {
                                foundBestRes = true;
                            }
                        }
                    });
                }

                if (changeToRes) {
                    if (feed.settings.debugEnabled) {
                        var reason = currentUrl === feed.placeholderURL ? 'was placeholder' : 'too small';
                        console.log('rais res for ' + currentUrl, reason);
                    }
                    $mediaItem.attr('data-current',changeToRes);

                    var newUrl = imgSrcSet[changeToRes];
                    if (newUrl !== currentUrl) {
                        $mediaItem.find('img').attr('src', newUrl);
                        if ($mediaItem.hasClass('sbsw_imgLiquid_ready')) {
                            $mediaItem.css('background-image', 'url("' + newUrl + '")');
                        }

                        feed.processNewImage($mediaItem);
                    }
                }

                $mediaItem.find('img').on('error', function () {
                    if (!$(this).hasClass('sbs-img-error')) {
                        $(this).addClass('sbsw-img-error');;
                        setTimeout(function() {
                            feed.afterResize();
                        }, 1500)
                    } else {
                        console.log('unfixed error ' + $(this).attr('src'));
                    }
                });
            },
            maybeRaiseImageResolution: function (justNew) {
                var feed = this,
                    itemsSelector = typeof justNew !== 'undefined' && justNew === true ? '.sbsw-item.sbsw-new' : '.sbsw-item',
                    forceChange = !feed.isInitialized ? true : false;
                $(feed.el).find(itemsSelector).each(function (index) {
                    if (!$(this).hasClass('sbsw-num-diff-hide')
                        && $(this).find('.sbsw-item-media').length
                        && typeof $(this).find('.sbsw-item-media').attr('data-available-images') !== 'undefined') {
                        feed.maybeRaiseSingleImageResolution($(this),index,forceChange);
                    }
                });
                feed.isInitialized = true;
            },
            getBestResolutionForAuto: function(colWidth, aspectRatio, $item) {
                if (isNaN(aspectRatio) || aspectRatio < 1) {
                    aspectRatio = 1;
                }
                var bestWidth = colWidth * aspectRatio,
                    bestWidthRounded = Math.ceil(bestWidth / 10) * 10,
                    customSizes = [120, 320, 480, 640];

                if ($item.hasClass('sbsw_highlighted')) {
                    bestWidthRounded = bestWidthRounded *2;
                }

                if (customSizes.indexOf(parseInt(bestWidthRounded)) === -1) {
                    var done = false;
                    $.each(customSizes, function (index, item) {
                        if (item > parseInt(bestWidthRounded) && !done) {
                            bestWidthRounded = item;
                            done = true;
                        }
                    });
                }

                return bestWidthRounded;
            },
            addToNeedsResizing: function($item,id) {
                var plugin = false;
                if ($item.hasClass('sbsw-facebook-item')) {
                    plugin = 'facebook';
                } else if ($item.hasClass('sbsw-instagram-item')) {
                    plugin = 'instagram';
                } else if ($item.hasClass('sbsw-twitter-item')) {
                    plugin = 'twitter';
                }
                if (plugin && this.needsResizing[plugin].indexOf(id) === -1) {
                    this.needsResizing[plugin].push(id);
                }
            },
            hideExtraItemsForWidth: function() {
                if (this.layout === 'carousel') {
                    return;
                }
                var $self = $(this.el),
                    num = typeof $self.attr('data-num') !== 'undefined' && $self.attr('data-num') !== '' ? parseInt($self.attr('data-num')) : 1,
                    nummobile = typeof $self.attr('data-nummobile') !== 'undefined' && $self.attr('data-nummobile') !== '' ? parseInt($self.attr('data-nummobile')) : num;

                if (!$self.hasClass('.sbsw_layout_carousel')) {
                    if ($(window).width() < 480) {
                        if (nummobile < $self.find('.sbsw-item').length) {
                            $self.find('.sbsw-item').slice(nummobile - $self.find('.sbsw-item').length).addClass('sbsw_num_diff_hide');
                        }
                    } else {
                        if (num < $self.find('.sbsw-item').length) {
                            $self.find('.sbsw-item').slice(num - $self.find('.sbsw-item').length).addClass('sbsw_num_diff_hide');
                        }
                    }
                }

            },
            setImageHeight: function() {
                if (this.layout === 'masonry') {
                    this.setMasonry();
                    this.smashotopeInit();
                    feed = this;
                    setTimeout(function(){ feed.setMasonry(); feed.smashotopeInit(); }, 500);
                }
            },
            setImageSizeClass: function () {
                var $self = $(this.el);
                $self.removeClass('sbsw-small sbsw-medium');
                var feedWidth = $self.innerWidth(),
                    photoPadding = parseInt(($self.find('.sb-wall-items-wrap').outerWidth() - $self.find('.sb-wall-items-wrap').width())) / 2,
                    cols = this.getColumnCount(),
                    feedWidthSansPadding = feedWidth - (photoPadding * (cols+2)),
                    colWidth = (feedWidthSansPadding / cols);

                if (colWidth > 225 && colWidth < 300) {
                    $self.addClass('sbsw-medium');
                } else if (colWidth <= 225) {
                    $self.addClass('sbsw-medium sbsw-small');
                }
                this.twitterCardHandler.setSize(colWidth);
            },
            setMinImageWidth: function () {
                if ($(this.el).find('.sbsw-item .sbsw_video_thumbnail').first().length) {
                    this.minImageWidth = $(this.el).find('.sbsw-item .sbsw_video_thumbnail').first().innerWidth();
                } else {
                    this.minImageWidth = 150;
                }
            },
            setImageResolution: function () {
                if (this.settings.imgRes === 'auto') {
                    this.imageResolution = 'auto';
                } else {
                    switch (this.settings.imgRes) {
                        case 'thumb':
                            this.imageResolution = 150;
                            break;
                        case 'medium':
                            this.imageResolution = 320;
                            break;
                        default:
                            this.imageResolution = 640;
                    }
                }
            },
            getImageUrls: function ($item) {
                var feed = this,
                    srcSet = JSON.parse($item.find('.sbsw-item-media').attr('data-available-images').replace(/\\\//g, '/')),
                    srcSet = typeof srcSet[0] !== 'undefined' ? srcSet[0] : srcSet,
                    id = $item.attr('id').replace('sbsw-','');

                var returnSrcSet = srcSet;

                if ($item.hasClass('sbsw-facebook-item')) {
                    if (typeof feed.resizedImages.facebook[id] !== 'undefined'
                        && feed.resizedImages.facebook[id].id !== 'pending'
                        && feed.resizedImages.facebook[id].id !== 'error') {
                        $.each(feed.resizedImages.facebook[id].sizes,function(index2,value2) {
                            returnSrcSet[value2] = sbswOptions.cffResizeUrl+feed.resizedImages.facebook[id].id+'-0-'+value2+'.jpg';
                        });
                    } else {
                        if (typeof this.resizedImages.facebook[id] === 'undefined'
                            || (typeof this.resizedImages.facebook[id] !== 'undefined' && typeof this.resizedImages.facebook[id]['id'] !== 'undefined' && this.resizedImages.facebook[id]['id'] !== 'pending' && this.resizedImages.facebook[id]['id'] !== 'error')) {
                            var hasMediumSize = false;

                            if (typeof returnSrcSet[0] !== "string") {
                                $.each(returnSrcSet[0],function(resolution) {
                                    if (parseInt(resolution) > 150 && parseInt(resolution) < 500) {
                                        hasMediumSize = true;
                                    }
                                });
                            } else {
                                hasMediumSize = true;
                            }

                            if (!hasMediumSize) {
                                this.addToNeedsResizing($item,id);
                            }
                        }
                    }
                } else if ($item.hasClass('sbsw-instagram-item')) {
                    if (typeof this.resizedImages.instagram[id] !== 'undefined'
                        && this.resizedImages.instagram[id] !== 'video'
                        && this.resizedImages.instagram[id] !== 'pending'
                        && this.resizedImages.instagram[id].id !== 'error'
                        && this.resizedImages.instagram[id].id !== 'video'
                        && this.resizedImages.instagram[id].id !== 'pending') {

                        if (typeof this.resizedImages.instagram[id]['sizes'] !== 'undefined') {
                            var foundSizes = [];
                            if (typeof this.resizedImages.instagram[id]['sizes']['full'] !== 'undefined') {
                                srcSet[640] = sbswOptions.sbiResizeUrl + this.resizedImages.instagram[id].id + 'full.jpg';
                                foundSizes.push(640);
                            }
                            if (typeof this.resizedImages.instagram[id]['sizes']['low'] !== 'undefined') {
                                srcSet[320] = sbswOptions.sbiResizeUrl + this.resizedImages.instagram[id].id + 'low.jpg';
                                foundSizes.push(320);
                            }
                            if (typeof this.resizedImages.instagram[id]['sizes']['thumb'] !== 'undefined') {
                                foundSizes.push(150);
                                srcSet[150] = sbswOptions.sbiResizeUrl + this.resizedImages.instagram[id].id + 'thumb.jpg';
                            }
                            if (this.settings.favorLocal) {
                                if (foundSizes.indexOf(640) === -1) {
                                    if (foundSizes.indexOf(320) > -1) {
                                        srcSet[640] = sbswOptions.sbiResizeUrl + this.resizedImages.instagram[id].id + 'low.jpg';
                                    }
                                }
                                if (foundSizes.indexOf(320) === -1) {
                                    if (foundSizes.indexOf(640) > -1) {
                                        srcSet[320] = sbswOptions.sbiResizeUrl + this.resizedImages.instagram[id].id + 'full.jpg';
                                    } else if (foundSizes.indexOf(150) > -1) {
                                        srcSet[320] = sbswOptions.sbiResizeUrl + this.resizedImages.instagram[id].id + 'thumb.jpg';
                                    }
                                }
                                if (foundSizes.indexOf(150) === -1) {
                                    if (foundSizes.indexOf(320) > -1) {
                                        srcSet[150] = sbswOptions.sbiResizeUrl + this.resizedImages.instagram[id].id + 'low.jpg';
                                    } else if (foundSizes.indexOf(640) > -1) {
                                        srcSet[150] = sbswOptions.sbiResizeUrl + this.resizedImages.instagram[id].id + 'full.jpg';
                                    }
                                }
                            }
                        }
                    } else if (typeof this.resizedImages.instagram[id] === 'undefined'
                        || (typeof this.resizedImages.instagram[id]['id'] !== 'undefined' && this.resizedImages.instagram[id]['id'] !== 'pending' && this.resizedImages.instagram[id]['id'] !== 'error')) {
                        this.addToNeedsResizing($item,id);
                    }
                } else if ($item.hasClass('sbsw-twitter-item')) {
                    if (typeof feed.resizedImages.twitter[id] !== 'undefined'
                        && feed.resizedImages.twitter[id].id !== 'pending'
                        && feed.resizedImages.twitter[id].id !== 'error') {
                        $.each(feed.resizedImages.twitter[id].sizes,function(index2,value2) {
                            returnSrcSet[value2] = sbswOptions.ctfResizeUrl+feed.resizedImages.twitter[id].id+'-0-'+value2+'.jpg';
                        });
                    } else {
                        if (typeof this.resizedImages.twitter[id] === 'undefined') {
                            this.addToNeedsResizing($item,id);
                        }
                    }
                }
                return returnSrcSet;
            },
            applyImageLiquid: function () {
                var $self = $(this.el),
                    feed = this;
                sbyAddImgLiquid();
                if (typeof $self.find(".sbsw_player_item").sbsw_imgLiquid == 'function') {
                    if ($self.find('.sbsw_player_item').length) {
                        $self.find(".sbsw_player_item .sbsw_player_video_thumbnail").sbsw_imgLiquid({fill: true});
                    }
                    $self.find(".sbsw-item .sbsw_item_video_thumbnail").sbsw_imgLiquid({fill: true});
                }
            },
            listenForVisibilityChange: function() {
                var feed = this;
                sbyAddVisibilityListener();
                if (typeof $(this.el).filter(':hidden').sbyVisibilityChanged == 'function') {
                    //If the feed is initially hidden (in a tab for example) then check for when it becomes visible and set then set the height
                    $(this.el).filter(':hidden').sbyVisibilityChanged({
                        callback: function (element, visible) {
                            feed.afterResize();
                        },
                        runOnLoad: false
                    });
                }
            },
            getColumnCount: function() {
                var $self = $(this.el),
                    cols = this.settings.cols,
                    colsmobile = this.settings.colsmobile,
                    returnCols = cols,
                    screenWidth = window.innerWidth;

                if ($self.hasClass('sbsw-mobcol-auto')) {
                    if (screenWidth < 640 && screenWidth > 480) {
                        returnCols = 2;
                    } else if (screenWidth <= 480) {
                        returnCols = 1;
                    }
                } else if (screenWidth <= 480) {
                    returnCols = colsmobile;
                }

                return parseInt(returnCols);
            },
            smashotopeInit: function() {
                var $self = $(this.el),
                    $container = $self.find('.sbsw-items-wrap-current').length ? $self.find('.sbsw-items-wrap-current') : $self.find('.sb-wall-items-wrap'),
                    columnWidth = '.sbsw-item';

                this.isotopeArgs.masonry = {
                    columnWidth: columnWidth
                };
                $container.smashotope(this.isotopeArgs);
            },
            setMasonry: function() {
                var $self = $(this.el),
                    feedWidth = $self.innerWidth(),
                    photoPadding = parseInt(($self.find('.sb-wall-items-wrap').outerWidth() - $self.find('.sb-wall-items-wrap').width())) / 2,
                    cols = this.getColumnCount(),
                    feedWidthSansPadding = feedWidth - (photoPadding * (cols+2)),
                    colWidth = (feedWidthSansPadding / cols),
                    widthAdjustForPercentPadding = $self.find('.sb-wall-items-wrap').css('padding').indexOf('.') > -1 ? 1 : 0;

                $self.find('.sbsw-item').each(function(index) {
                    $(this).css({
                        width: (colWidth) - photoPadding + 'px',
                    });

                });
            },
            appendSmashotope:function() {
                $self = $(this.el);
                if ($self.find('.sbsw-items-wrap-current').data('smashotope')) {
                    // $self.find('.sbi_new').addClass('sbi_hide');
                    $self.find('.sbsw-items-wrap-current').smashotope('appended', $('.sbsw-new'));
                }
            }
        };

        function SbswLightboxBuilder() {}

        SbswLightboxBuilder.prototype = {
            getData: function(a){
                var closestFeedIndex = parseInt(a.closest('.sb-wall').attr('data-sbsw-index')-1),
                    lbInfoAtt = a.attr("data-lightbox-info"),
                    lbInfo = lbInfoAtt.indexOf('{') === 0 ? JSON.parse(lbInfoAtt) : false;
                if (lbInfo !== false) {
                    return {
                        feedIndex : closestFeedIndex,
                        link: a.attr("href"),
                        mediaType: lbInfo.media_type,
                        mediaURL: lbInfo.media_url,
                        title: window.sbsw.encodeHTML(lbInfo.title),
                        avatar: lbInfo.avatar,
                        name: lbInfo.full_name,
                        accountURL: lbInfo.account_url
                    }
                } else {
                    return {
                        feedIndex : closestFeedIndex,
                        link: a.attr("href"),
                        mediaType: '',
                        mediaURL: '',
                        title: '',
                        avatar: '',
                        name: '',
                        accountURL: ''
                    }
                }
            },
            template: function () {
                return "<div id='sbsw-lightboxOverlay' class='sbsw-lightboxOverlay'></div>"+
                    "<div id='sbsw-lightbox' class='sbsw-lightbox'>"+
                    "<div class='sbsw-lb-outerContainer'>"+
                    "<div class='sbsw-lb-nav'><a class='sbsw-lb-prev' href='#' ><p class='sbsw-screenreader'>Previous Slide</p><span></span></a><a class='sbsw-lb-next' href='#' ><p class='sbsw-screenreader'>Next Slide</p><span></span></a></div>"+
                    "<div class='sbsw-lb-container-wrapper'>"+
                    "<div class='sbsw-lb-container'>"+
                    "<div class='sbsw-lb-media-wrap'>"+
                    "<span class='sbsw-lb-media'>" +
                    "<video class='sbsw-lb-video' src='' poster='' controls autoplay></video>"+
                    "<img class='sbsw-lb-image' alt='Lightbox image placeholder' src='' />"+
                    "<div class='sbsw-lb-player' id='sbsw-lb-player'></div>" +
                    "</span>" +
                    "</div>" +

                    "<div class='sbsw-lb-loader'><a class='sbsw-lb-cancel'></a></div>"+
                    "</div>"+
                    "<div class='sbsw-lb-dataContainer'>"+
                    "<div class='sbsw-lb-data'>"+
                    "<div class='sbsw-lb-details'>"+
                    "<div class='sbsw-lb-caption'></div>"+
                    "<div class='sbsw-lb-info'>"+
                    "<div class='sbsw-lb-number'></div>"+
                    "</div>"+
                    "</div>"+
                    "</div>"+
                    '<div class="sbsw-lb-closeContainer"><a class="sbsw-lb-close"><svg class="svg-inline--fa fa-times fa-w-12" aria-hidden="true" data-fa-processed="" data-prefix="fa" data-icon="times" role="presentation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M323.1 441l53.9-53.9c9.4-9.4 9.4-24.5 0-33.9L279.8 256l97.2-97.2c9.4-9.4 9.4-24.5 0-33.9L323.1 71c-9.4-9.4-24.5-9.4-33.9 0L192 168.2 94.8 71c-9.4-9.4-24.5-9.4-33.9 0L7 124.9c-9.4 9.4-9.4 24.5 0 33.9l97.2 97.2L7 353.2c-9.4 9.4-9.4 24.5 0 33.9L60.9 441c9.4 9.4 24.5 9.4 33.9 0l97.2-97.2 97.2 97.2c9.3 9.3 24.5 9.3 33.9 0z"></path></svg></a></div>' +
                    "<div class=''><a class='sbsw-lb-close'></a></div>"+
                    "</div>"+
                    "</div>"+
                    "</div>"+
                    "</div>";
            },
            beforePlayerSetup: function($lightbox,data,index,album,feed){
                $lightbox.removeClass('sbsw-lightbox-transitioning');
                $lightbox.addClass('sbsw-lightbox-type-' + data.mediaType);
                if (data.mediaType === 'youtube') {
                    $lightbox.find('.sbsw-lb-player').show()
                }
            },
            afterPlayerSetup: function ($lightbox,data,index,album) {
                //Add links to the caption
                var sbyLightboxCaption = data.title,
                    hashRegex = /(^|\s)#(\w[\u0041-\u005A\u0061-\u007A\u00AA\u00B5\u00BA\u00C0-\u00D6\u00D8-\u00F6\u00F8-\u02C1\u02C6-\u02D1\u02E0-\u02E4\u02EC\u02EE\u0370-\u0374\u0376\u0377\u037A-\u037D\u0386\u0388-\u038A\u038C\u038E-\u03A1\u03A3-\u03F5\u03F7-\u0481\u048A-\u0527\u0531-\u0556\u0559\u0561-\u0587\u05D0-\u05EA\u05F0-\u05F2\u0620-\u064A\u066E\u066F\u0671-\u06D3\u06D5\u06E5\u06E6\u06EE\u06EF\u06FA-\u06FC\u06FF\u0710\u0712-\u072F\u074D-\u07A5\u07B1\u07CA-\u07EA\u07F4\u07F5\u07FA\u0800-\u0815\u081A\u0824\u0828\u0840-\u0858\u08A0\u08A2-\u08AC\u0904-\u0939\u093D\u0950\u0958-\u0961\u0971-\u0977\u0979-\u097F\u0985-\u098C\u098F\u0990\u0993-\u09A8\u09AA-\u09B0\u09B2\u09B6-\u09B9\u09BD\u09CE\u09DC\u09DD\u09DF-\u09E1\u09F0\u09F1\u0A05-\u0A0A\u0A0F\u0A10\u0A13-\u0A28\u0A2A-\u0A30\u0A32\u0A33\u0A35\u0A36\u0A38\u0A39\u0A59-\u0A5C\u0A5E\u0A72-\u0A74\u0A85-\u0A8D\u0A8F-\u0A91\u0A93-\u0AA8\u0AAA-\u0AB0\u0AB2\u0AB3\u0AB5-\u0AB9\u0ABD\u0AD0\u0AE0\u0AE1\u0B05-\u0B0C\u0B0F\u0B10\u0B13-\u0B28\u0B2A-\u0B30\u0B32\u0B33\u0B35-\u0B39\u0B3D\u0B5C\u0B5D\u0B5F-\u0B61\u0B71\u0B83\u0B85-\u0B8A\u0B8E-\u0B90\u0B92-\u0B95\u0B99\u0B9A\u0B9C\u0B9E\u0B9F\u0BA3\u0BA4\u0BA8-\u0BAA\u0BAE-\u0BB9\u0BD0\u0C05-\u0C0C\u0C0E-\u0C10\u0C12-\u0C28\u0C2A-\u0C33\u0C35-\u0C39\u0C3D\u0C58\u0C59\u0C60\u0C61\u0C85-\u0C8C\u0C8E-\u0C90\u0C92-\u0CA8\u0CAA-\u0CB3\u0CB5-\u0CB9\u0CBD\u0CDE\u0CE0\u0CE1\u0CF1\u0CF2\u0D05-\u0D0C\u0D0E-\u0D10\u0D12-\u0D3A\u0D3D\u0D4E\u0D60\u0D61\u0D7A-\u0D7F\u0D85-\u0D96\u0D9A-\u0DB1\u0DB3-\u0DBB\u0DBD\u0DC0-\u0DC6\u0E01-\u0E30\u0E32\u0E33\u0E40-\u0E46\u0E81\u0E82\u0E84\u0E87\u0E88\u0E8A\u0E8D\u0E94-\u0E97\u0E99-\u0E9F\u0EA1-\u0EA3\u0EA5\u0EA7\u0EAA\u0EAB\u0EAD-\u0EB0\u0EB2\u0EB3\u0EBD\u0EC0-\u0EC4\u0EC6\u0EDC-\u0EDF\u0F00\u0F40-\u0F47\u0F49-\u0F6C\u0F88-\u0F8C\u1000-\u102A\u103F\u1050-\u1055\u105A-\u105D\u1061\u1065\u1066\u106E-\u1070\u1075-\u1081\u108E\u10A0-\u10C5\u10C7\u10CD\u10D0-\u10FA\u10FC-\u1248\u124A-\u124D\u1250-\u1256\u1258\u125A-\u125D\u1260-\u1288\u128A-\u128D\u1290-\u12B0\u12B2-\u12B5\u12B8-\u12BE\u12C0\u12C2-\u12C5\u12C8-\u12D6\u12D8-\u1310\u1312-\u1315\u1318-\u135A\u1380-\u138F\u13A0-\u13F4\u1401-\u166C\u166F-\u167F\u1681-\u169A\u16A0-\u16EA\u1700-\u170C\u170E-\u1711\u1720-\u1731\u1740-\u1751\u1760-\u176C\u176E-\u1770\u1780-\u17B3\u17D7\u17DC\u1820-\u1877\u1880-\u18A8\u18AA\u18B0-\u18F5\u1900-\u191C\u1950-\u196D\u1970-\u1974\u1980-\u19AB\u19C1-\u19C7\u1A00-\u1A16\u1A20-\u1A54\u1AA7\u1B05-\u1B33\u1B45-\u1B4B\u1B83-\u1BA0\u1BAE\u1BAF\u1BBA-\u1BE5\u1C00-\u1C23\u1C4D-\u1C4F\u1C5A-\u1C7D\u1CE9-\u1CEC\u1CEE-\u1CF1\u1CF5\u1CF6\u1D00-\u1DBF\u1E00-\u1F15\u1F18-\u1F1D\u1F20-\u1F45\u1F48-\u1F4D\u1F50-\u1F57\u1F59\u1F5B\u1F5D\u1F5F-\u1F7D\u1F80-\u1FB4\u1FB6-\u1FBC\u1FBE\u1FC2-\u1FC4\u1FC6-\u1FCC\u1FD0-\u1FD3\u1FD6-\u1FDB\u1FE0-\u1FEC\u1FF2-\u1FF4\u1FF6-\u1FFC\u2071\u207F\u2090-\u209C\u2102\u2107\u210A-\u2113\u2115\u2119-\u211D\u2124\u2126\u2128\u212A-\u212D\u212F-\u2139\u213C-\u213F\u2145-\u2149\u214E\u2183\u2184\u2C00-\u2C2E\u2C30-\u2C5E\u2C60-\u2CE4\u2CEB-\u2CEE\u2CF2\u2CF3\u2D00-\u2D25\u2D27\u2D2D\u2D30-\u2D67\u2D6F\u2D80-\u2D96\u2DA0-\u2DA6\u2DA8-\u2DAE\u2DB0-\u2DB6\u2DB8-\u2DBE\u2DC0-\u2DC6\u2DC8-\u2DCE\u2DD0-\u2DD6\u2DD8-\u2DDE\u2E2F\u3005\u3006\u3031-\u3035\u303B\u303C\u3041-\u3096\u309D-\u309F\u30A1-\u30FA\u30FC-\u30FF\u3105-\u312D\u3131-\u318E\u31A0-\u31BA\u31F0-\u31FF\u3400-\u4DB5\u4E00-\u9FCC\uA000-\uA48C\uA4D0-\uA4FD\uA500-\uA60C\uA610-\uA61F\uA62A\uA62B\uA640-\uA66E\uA67F-\uA697\uA6A0-\uA6E5\uA717-\uA71F\uA722-\uA788\uA78B-\uA78E\uA790-\uA793\uA7A0-\uA7AA\uA7F8-\uA801\uA803-\uA805\uA807-\uA80A\uA80C-\uA822\uA840-\uA873\uA882-\uA8B3\uA8F2-\uA8F7\uA8FB\uA90A-\uA925\uA930-\uA946\uA960-\uA97C\uA984-\uA9B2\uA9CF\uAA00-\uAA28\uAA40-\uAA42\uAA44-\uAA4B\uAA60-\uAA76\uAA7A\uAA80-\uAAAF\uAAB1\uAAB5\uAAB6\uAAB9-\uAABD\uAAC0\uAAC2\uAADB-\uAADD\uAAE0-\uAAEA\uAAF2-\uAAF4\uAB01-\uAB06\uAB09-\uAB0E\uAB11-\uAB16\uAB20-\uAB26\uAB28-\uAB2E\uABC0-\uABE2\uAC00-\uD7A3\uD7B0-\uD7C6\uD7CB-\uD7FB\uF900-\uFA6D\uFA70-\uFAD9\uFB00-\uFB06\uFB13-\uFB17\uFB1D\uFB1F-\uFB28\uFB2A-\uFB36\uFB38-\uFB3C\uFB3E\uFB40\uFB41\uFB43\uFB44\uFB46-\uFBB1\uFBD3-\uFD3D\uFD50-\uFD8F\uFD92-\uFDC7\uFDF0-\uFDFB\uFE70-\uFE74\uFE76-\uFEFC\uFF21-\uFF3A\uFF41-\uFF5A\uFF66-\uFFBE\uFFC2-\uFFC7\uFFCA-\uFFCF\uFFD2-\uFFD7\uFFDA-\uFFDC+0-9_]+)|(#[a-я]+)|(#[\u3000-\u303f\u3040-\u309f\u30a0-\u30ff\uff00-\uff9f\u4e00-\u9faf\u3400-\u4dbf]+)/gi,
                    tagRegex = /[@]+[A-Za-z0-9-_\."<]+/g;
                if (typeof sbyLightboxCaption !== 'undefined' && sbyLightboxCaption !== '') {
                    sbyLightboxCaption = sbyLightboxCaption.replace(/(>#)/g,'> #');
                }
                (sbyLightboxCaption) ? sbyLightboxCaption = sbyLinkify(sbyLightboxCaption) : sbyLightboxCaption = '';

                if (typeof sbyLightboxAction === 'function') {
                    setTimeout(function() {
                        sbyLightboxAction();
                    },100);
                }
                var avatarImageHtml = '',
                    userHtml = '',
                    nameHtml = '';
                if (typeof data.avatar !== 'undefined' && data.avatar !== '' && typeof data.name !== 'undefined') {
                    avatarImageHtml = (data.avatar !== 'undefined') ? '<img src="'+data.avatar+'" />' : '';
                    nameHtml = '<a class="sbsw-lightbox_username" href="'+data.accountURL+'" target="_blank" rel="noopener">'+avatarImageHtml+'<p>@'+data.name + '</p></a> ';
                } else if (typeof data.name !== 'undefined') {
                    $.each(window.sbsw.feeds, function() {
                        if (typeof this.availableAvatarUrls !== 'undefined' && typeof this.availableAvatarUrls[data.name] !== 'undefined' && this.availableAvatarUrls[data.name] !== 'undefined') {
                            avatarImageHtml = '<img src="'+this.availableAvatarUrls[data.name]+'" />';
                        }
                    });
                }

                $lightbox.find(".sbsw-lb-caption").html(nameHtml + '<span class="sbsw-caption-text">' + sbyLightboxCaption + '</span>').fadeIn("fast");

            },
            afterResize: function(){
                var playerHeight = $('#sbsw-lightbox .sbsw-lb-player').height();

                if (playerHeight > 100) {
                    var heightDif = $('#sbsw-lightbox .sbsw-lb-outerContainer').height() - playerHeight;
                    if (heightDif > 10) {
                        $('#sbsw-lightbox .sbsw-lb-player').css('top',heightDif/2);
                    }
                }
            },
            pausePlayer: function () {
                if (typeof YT.get('sbsw-lb-player') !== 'undefined' && typeof YT.get('sbsw-lb-player').pauseVideo === 'function') {
                    YT.get('sbsw-lb-player').pauseVideo()
                } else if (typeof window.sbswLightboxPlayer !== 'undefined' && typeof window.sbswLightboxPlayer.pauseVideo === 'function') {
                    window.sbswLightboxPlayer.pauseVideo();
                }
                if (sbsw_supports_video() && $('#sbsw-lightbox video.sbsw-lb-video').length) $('#sbsw-lightbox video.sbsw-lb-video')[0].pause();
            },
            afterSlideChange: function () {
                var $lightbox = $('#sbsw-lightbox');
                $lightbox.addClass('sbsw-lightbox-transitioning');
                $lightbox.removeClass('sbsw-lightbox-type-image sbsw-lightbox-type-video sbsw-lightbox-type-iframe sbsw-lightbox-type-carousel sbsw-lightbox-type-youtube');
                $lightbox.find('.sbsw-lb-iframe').remove();
                $lightbox.find('.sbsw-lb-player').hide();
            }

        };

        function SbTwitterCardHandler(prefix,$context) {
            this.$context = $context;
            this.prefix = prefix;
        }

        SbTwitterCardHandler.prototype = {
            setSize: function(colWidth){
                var $cards = this.$context.find('.'+this.prefix+'-twitter-card');
                if (colWidth > 550) {
                    $cards.addClass(this.prefix + '-tc-wide');
                } else {
                    $cards.removeClass(this.prefix + '-tc-wide');
                }
            },
            handleImageError: function($item){
                var prefix = this.prefix;
                $item.find('.' + prefix + '-twitter-card').each(function(){
                    $(this).find('img').on('error', function () {
                        $(this).closest('.' + prefix + '-twitter-card').addClass(prefix + '-tc-no-image');
                        $(this).remove();
                    });
                });
            },
            maybeRaiseResolution: function(){
                var cardHandler = this,
                    $cards = this.$context.find('.'+this.prefix+'-twitter-card'),
                    prefix = this.prefix;;

                $cards.each(function (index) {
                    if (!$(this).closest('.'+prefix+'-item').hasClass(prefix+'-num-diff-hide')) {
                        cardHandler.maybeRaiseSingleResolution($(this),index);
                    }
                });
            },
            maybeRaiseSingleResolution: function($item){
                var $img = $item.find('img').length ? $item.find('img') : false;

                if (!$img) {
                    return;
                }
                if (typeof $img.attr('data-available-images') === 'undefined') {
                    return;
                }

                var imageUrls = JSON.parse($img.attr('data-available-images').replace(/\\\//g, '/')),
                    currentUrl = $img.attr('src'),
                    currentRes = typeof $img.attr('data-current') === 'undefined' ? 0 : parseInt($img.attr('data-current')),
                    neededRes = $img.closest('.sbsw-tc-media').innerWidth();
                changeToRes = 0;

                if (currentRes < neededRes) {
                    var foundBestRes = false,
                        newUrl = currentUrl;
                    $.each(imageUrls, function (ii, value) {

                        if (! foundBestRes && ii >= neededRes) {
                            changeToRes = parseInt(ii);

                            if (changeToRes >= neededRes) {
                                foundBestRes = true;

                                newUrl = sbswOptions.ctfResizeUrl+value;
                            }
                        }
                    });

                    if (newUrl !== currentUrl) {
                        $img.attr('data-current',changeToRes);

                        if (newUrl !== currentUrl) {
                            $img.attr('src', newUrl);
                            if ($img.closest('.sbsw_imgLiquid_ready').length) {
                                $img.closest('.sbsw_imgLiquid_ready').css('background-image', 'url("' + newUrl + '")').attr('data-bg',newUrl);
                            }
                        }
                    }

                    $img.on('error', function () {
                        if (!$(this).hasClass('sbsw-img-error')) {
                            $(this).addClass('sbsw-img-error');
                            setTimeout(function() {
                                feed.afterResize();
                            }, 1500)
                        } else {
                            console.log('unfixed error ' + $(this).attr('src'));
                        }
                    });
                }
            }
        };

        function SbFilterBar(feed) {
            this.feed = feed;
            this.pages = {
                instagram : 0,
                facebook : 0,
                twitter : 0,
                youtube : 0
            },
                this.currentFilter = 'all',
                this.pagesAvailable = {
                    instagram : true,
                    facebook : true,
                    twitter : true,
                    youtube : true
                };
        }

        SbFilterBar.prototype = {
            init: function() {
                var filterBar = this,
                    feed = this.feed,
                    $self = $(this.feed.el);
                $self.find('.sbsw-single-filter a').each(function() {
                    $(this).click(function(event) {
                        event.preventDefault();
                        if (!$self.hasClass('sbsw-wrap-switching')) {
                            var plugin = $(this).attr('data-plugin');
                            if (plugin !== filterBar.currentFilter) {
                                filterBar.currentFilter = plugin;
                                filterBar.switchToFilteredSet(plugin);
                            }
                        }


                    });
                });
            },
            updateShowLoadMore: function() {
                var shouldShow = true,
                    $self = $(this.feed.el);
                if (this.currentFilter === 'all') {
                    shouldShow = this.feed.outOfPages === false;
                } else if ( !this.pagesAvailable[this.currentFilter] ) {
                    shouldShow = false;
                }

                if ( ! shouldShow ) {
                    $self.find('.sb-wall-load-btn').hide();
                } else {
                    $self.find('.sb-wall-load-btn').show();
                }

            },
            beforeSwitch: function() {
                var feed = this.feed,
                    $self = $(this.feed.el);

                this.updateShowLoadMore();
                $self.addClass('sbsw-wrap-switching');
                $self.find('.sb-wall-items-wrap').removeClass('sbsw-items-wrap-current');
                $self.find('.sb-wall-items-wrap-outer-wrap').css('height', $self.find('.sbsw-items-wrap-visible').innerHeight()+'px');
            },
            afterSwitch: function() {
                var feed = this.feed,
                    $self = $(this.feed.el);

                $self.removeClass('sbsw-wrap-switching');
                $self.find('.sb-wall-items-wrap-outer-wrap').css('height','');
                feed.afterResize();
            },
            switchToFilteredSet: function(plugin) {
                this.beforeSwitch();

                var filterBar = this,
                    feed = this.feed,
                    $self = $(this.feed.el);

                if (!$self.find('.sbsw-items-wrap-'+plugin).length) {
                    $self.find('.sb-wall-items-wrap').last().after('<div class="sb-wall-items-wrap sbsw-items-wrap-'+plugin+' sbsw-items-wrap-visible"></div>');
                    $self.find('.sbsw-items-wrap-'+plugin).addClass('sbsw-items-wrap-current');
                    this.addRemoteFilteredPosts();
                } else {
                    $self.find('.sbsw-items-wrap-'+plugin).addClass('sbsw-items-wrap-current');
                    setTimeout(function() {
                        $self.find('.sbsw-items-wrap-'+plugin).addClass('sbsw-items-wrap-visible');
                        $self.find('.sb-wall-items-wrap').each(function() {
                            if (!$(this).hasClass('sbsw-items-wrap-'+plugin)) {
                                $(this).removeClass('sbsw-items-wrap-visible');
                            }
                        })
                        filterBar.afterSwitch();
                    }, 300)

                }
            },
            addRemoteFilteredPosts: function() {
                var filterBar = this,
                    feed = this.feed,
                    $self = $(this.feed.el),
                    plugin = this.currentFilter,
                    page = this.pages[plugin];

                var itemOffset = $self.find('.sbsw-items-wrap-current .sbsw-item').length,
                    submitData = {
                        action: 'sbsw_load_more_clicked',
                        filter_type: plugin,
                        offset: itemOffset,
                        feed_id: feed.settings.feedID,
                        atts: feed.settings.shortCodeAtts,
                        current_resolution: feed.imageResolution,
                        page: page
                    };
                var onSuccess = function (data) {
                    $self.find('.sb-wall-footer .sb-wall-load-btn').removeClass('sbsw-loading');
                    $self.find('.sbsw-loader').remove();
                    if (data.trim().indexOf('{') === 0) {
                        var response = JSON.parse(data);
                        if (feed.settings.debugEnabled) {
                            console.log(response);
                        }
                        filterBar.pages[plugin] ++;
                        feed.appendNewPosts(response.html,plugin);
                        if (feed.settings.ajaxPostLoad) {
                            feed.settings.ajaxPostLoad = false;
                            feed.afterInitialImagesLoaded();
                        } else {
                            feed.afterNewImagesLoaded();
                        }
                        $('.sbsw_no_js').removeClass('sbsw_no_js');
                        var isOutOfPages = response.feedStatus.filterOutOfPages.indexOf(plugin) > -1;
                        if (isOutOfPages) {
                            filterBar.pagesAvailable[plugin] = false;
                            $self.find('.sb-wall-load-btn').hide();
                        }
                    }
                    $self.find('.sbsw-items-wrap-'+plugin).addClass('sbsw-items-wrap-visible');
                    $self.find('.sbsw-items-wrap-'+plugin).find('.sbsw-lightbox-hover').each(function() {
                        $(this).attr('data-sbsw-lightbox',feed.index+plugin);
                    })
                    //sbsw-lightbox-hover
                    $self.find('.sb-wall-items-wrap').each(function() {
                        if (!$(this).hasClass('sbsw-items-wrap-'+plugin)) {
                            $(this).removeClass('sbsw-items-wrap-visible');
                        }
                    })
                    filterBar.afterSwitch();
                };
                sbswAjax(submitData, onSuccess);
            }
        };

        window.sb_wall_init = function() {
            window.sbsw = new Sbsw();
            window.sbsw.createPage( window.sbsw.createFeeds, {whenFeedsCreated: window.sbsw.afterFeedsCreated});
        };

        function sbswShareHTML($item) {
            var shareData = JSON.parse($item.find('.sbsw-share-button').attr('data-share-data'));

            if ($item.find('.sbsw-lightbox-hover').length){
                shareData.content = JSON.parse($item.find('.sbsw-lightbox-hover').attr('data-lightbox-info'));
            } else {
                shareData.content = {
                    media_url : $item.find('.sbsw-item-avatar img').length ? $item.find('.sbsw-item-avatar img').attr('src') : '',
                    title : $item.find('.sbsw-item-bottom-content').text()
                }
            }

            var facebook = 'https://www.facebook.com/sharer/sharer.php?u='+ shareData.link,
                twitter = 'https://twitter.com/intent/tweet?text='+ shareData.link,
                pinterest = 'https://pinterest.com/pin/create/button/?url='+ shareData.link +'&media='+ encodeURIComponent(shareData.content.media_url) +'&description='+ encodeURIComponent(shareData.content.title),
                linkedIn = 'https://www.linkedin.com/shareArticle?mini=true&url='+ shareData.link +'&title='+ encodeURIComponent(shareData.content.title),
                email = 'mailto:?subject=Facebook&body='+ shareData.link +'%20-%20'+ encodeURIComponent(shareData.content.title);

            var html = '<p class="sbsw-share-popup">' +

                '<a href="'+facebook+'" class="sbsw-share-facebook" target="_blank" rel="noopener">' +
                '<span class="sbsw-screenreader">Facebook</span><svg aria-hidden="true" focusable="false" data-prefix="fab" data-icon="facebook" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-facebook fa-w-16"><path fill="currentColor" d="M504 256C504 119 393 8 256 8S8 119 8 256c0 123.78 90.69 226.38 209.25 245V327.69h-63V256h63v-54.64c0-62.15 37-96.48 93.67-96.48 27.14 0 55.52 4.84 55.52 4.84v61h-31.28c-30.8 0-40.41 19.12-40.41 38.73V256h68.78l-11 71.69h-57.78V501C413.31 482.38 504 379.78 504 256z" class=""></path></svg>' +
                '</a>' +

                '<a href="'+twitter+'" class="sbsw-share-twitter" target="_blank" rel="noopener">' +
                '<span class="sbsw-screenreader">Twitter</span><svg class="svg-inline--fa fa-twitter fa-w-16" aria-hidden="true" aria-label="twitter logo" data-fa-processed="" data-prefix="fab" data-icon="twitter" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path></svg>' +
                '</a>' +

                '<a href="'+linkedIn+'" class="sbsw-share-linkedin" target="_blank" rel="noopener">' +
                '<span class="sbsw-screenreader">Linkedin</span><svg aria-hidden="true" focusable="false" data-prefix="fab" data-icon="linkedin-in" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-linkedin-in fa-w-14"><path fill="currentColor" d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z" class=""></path></svg>' +
                '</a>' +

                '<a href="'+pinterest+'" class="sbsw-share-pinterest" target="_blank" rel="noopener">' +
                '<span class="sbsw-screenreader">Pinterest</span><svg aria-hidden="true" focusable="false" data-prefix="fab" data-icon="pinterest" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 496 512" class="svg-inline--fa fa-pinterest fa-w-16"><path fill="currentColor" d="M496 256c0 137-111 248-248 248-25.6 0-50.2-3.9-73.4-11.1 10.1-16.5 25.2-43.5 30.8-65 3-11.6 15.4-59 15.4-59 8.1 15.4 31.7 28.5 56.8 28.5 74.8 0 128.7-68.8 128.7-154.3 0-81.9-66.9-143.2-152.9-143.2-107 0-163.9 71.8-163.9 150.1 0 36.4 19.4 81.7 50.3 96.1 4.7 2.2 7.2 1.2 8.3-3.3.8-3.4 5-20.3 6.9-28.1.6-2.5.3-4.7-1.7-7.1-10.1-12.5-18.3-35.3-18.3-56.6 0-54.7 41.4-107.6 112-107.6 60.9 0 103.6 41.5 103.6 100.9 0 67.1-33.9 113.6-78 113.6-24.3 0-42.6-20.1-36.7-44.8 7-29.5 20.5-61.3 20.5-82.6 0-19-10.2-34.9-31.4-34.9-24.9 0-44.9 25.7-44.9 60.2 0 22 7.4 36.8 7.4 36.8s-24.5 103.8-29 123.2c-5 21.4-3 51.6-.9 71.2C65.4 450.9 0 361.1 0 256 0 119 111 8 248 8s248 111 248 248z" class=""></path></svg>' +
                '</a>' +

                '<a href="'+email+'" class="sbsw-share-email" target="_blank" rel="noopener">' +
                '<span class="sbsw-screenreader">Email</span><svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="envelope" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-envelope fa-w-16"><path fill="currentColor" d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm0 48v40.805c-22.422 18.259-58.168 46.651-134.587 106.49-16.841 13.247-50.201 45.072-73.413 44.701-23.208.375-56.579-31.459-73.413-44.701C106.18 199.465 70.425 171.067 48 152.805V112h416zM48 400V214.398c22.914 18.251 55.409 43.862 104.938 82.646 21.857 17.205 60.134 55.186 103.062 54.955 42.717.231 80.509-37.199 103.053-54.947 49.528-38.783 82.032-64.401 104.947-82.653V400H48z" class=""></path></svg>' +
                '</a>' +

                '<svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="play" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="svg-inline--fa fa-play fa-w-14"><path fill="currentColor" d="M424.4 214.7L72.4 6.6C43.8-10.3 0 6.1 0 47.9V464c0 37.5 40.7 60.1 72.4 41.3l352-208c31.4-18.5 31.5-64.1 0-82.6z" class=""></path></svg>' +

                '</p>';

            return html;

        }

        function sbswGetNewFeed(feed,index,feedOptions) {
            return new SbswFeed(feed,index,feedOptions);
        }

        function sbswGetlightboxBuilder() {
            return new SbswLightboxBuilder();
        }

        function sbswAjax(submitData,onSuccess) {
            $.ajax({
                url: sbswOptions.adminAjaxUrl,
                type: 'post',
                data: submitData,
                success: onSuccess
            });
        }

        function sbswIsTourch() {
            if ("ontouchstart" in document.documentElement) {
                return true;
            }
            return false;
        }

    })(jQuery);

    jQuery(document).ready(function($) {
        sb_wall_init();
    });

} // if sbsw_js_exists



window.onYouTubeIframeAPIReady = function() {
    window.swAPIReady = true;
};
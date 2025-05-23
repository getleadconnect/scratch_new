/*! wScratchPad - v2.1.0 - 2014-04-14 */
/* this is custom script for scrach */

 ! function(a) {
	"use strict";

	function b(b, c) {
		this.$el = a(b), this.options = c, this.init = !1, this.enabled = !0, this._generate()
	}
	b.prototype = {
		_generate: function() {
			return a.support.canvas ? (this.canvas = document.createElement("canvas"), this.ctx = this.canvas.getContext("2d"), "static" === this.$el.css("position") && this.$el.css("position", "relative"), 
				this.$img = $('<img src="" />').css({ 
				position: 'absolute',
				width: '100%',
				height: '100%'
			}), this.$scratchpad = a(this.canvas).css({
				position: "absolute",
				width: "100%",
				height: "100%"
			}), this.$scratchpad.bindMobileEvents(), this.$scratchpad.mousedown(a.proxy(function(b) {
				return this.enabled ? (this.canvasOffset = a(this.canvas).offset(), this.scratch = !0, void this._scratchFunc(b, "Down")) : !0
			}, this)).mousemove(a.proxy(function(a) {
				this.scratch && this._scratchFunc(a, "Move")
			}, this)).mouseup(a.proxy(function(a) {
				this.scratch && (this.scratch = !1, this._scratchFunc(a, "Up"))
			}, this)), this._setOptions(), this.$el.append(this.$img).append(this.$scratchpad), this.init = !0, void this.reset()) : (this.$el.append("Canvas is not supported in this browser."), !0)
		},
		reset: function() {
			var b = this,
				c = Math.ceil(this.$el.innerWidth()),
				d = Math.ceil(this.$el.innerHeight()),
				e = window.devicePixelRatio || 1;
			this.pixels = c * d, this.$scratchpad.attr("width", c).attr("height", d), this.canvas.setAttribute("width", c * e), this.canvas.setAttribute("height", d * e), this.ctx.scale(e, e), this.pixels = c * e * d * e, this.$img.hide(), this.options.bg && ("#" === this.options.bg.charAt(0) ? this.$el.css("backgroundColor", this.options.bg) : (this.$el.css("backgroundColor", ""), this.$img.attr("src", this.options.bg))), this.options.fg && ("#" === this.options.fg.charAt(0) ? (this.ctx.fillStyle = this.options.fg, this.ctx.beginPath(), this.ctx.rect(0, 0, c, d), this.ctx.fill(), this.$img.show()) : a(new Image).attr("src", this.options.fg).load(function() {
				b.ctx.drawImage(this, 0, 0, c, d), b.$img.show()
			}))
		},
		clear: function() {
			this.ctx.clearRect(0, 0, Math.ceil(this.$el.innerWidth()), Math.ceil(this.$el.innerHeight()))
		},
		enable: function(a) {
			this.enabled = a === !0 ? !0 : !1
		},
		destroy: function() {
			this.$el.children().remove(), a.removeData(this.$el, "wScratchPad")
		},
		_setOptions: function() {
			var a, b;
			for (a in this.options) this.options[a] = this.$el.attr("data-" + a) || this.options[a], b = "set" + a.charAt(0).toUpperCase() + a.substring(1), this[b] && this[b](this.options[a])
		},
		setBg: function() {
			this.init && this.reset()
		},
		setFg: function() {
			this.setBg()
		},
		setCursor: function(a) {
			this.$el.css("cursor", a)
		},
		_scratchFunc: function(a, b) {
			a.pageX = Math.floor(a.pageX - this.canvasOffset.left), a.pageY = Math.floor(a.pageY - this.canvasOffset.top), this["_scratch" + b](a), (this.options.realtime || "Up" === b) && this.options["scratch" + b] && this.options["scratch" + b].apply(this, [a, this._scratchPercent()])
		},
		_scratchPercent: function() {
			for (var a = 0, b = this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height), c = 0, d = b.data.length; d > c; c += 4) 0 === b.data[c] && 0 === b.data[c + 1] && 0 === b.data[c + 2] && 0 === b.data[c + 3] && a++;
			return a / this.pixels * 100
		},
		_scratchDown: function(a) {
			this.ctx.globalCompositeOperation = "destination-out", this.ctx.lineJoin = "round", this.ctx.lineCap = "round", this.ctx.strokeStyle = this.options.color, this.ctx.lineWidth = this.options.size, this.ctx.beginPath(), this.ctx.arc(a.pageX, a.pageY, this.options.size / 2, 0, 2 * Math.PI, !0), this.ctx.closePath(), this.ctx.fill(), this.ctx.beginPath(), this.ctx.moveTo(a.pageX, a.pageY)
		},
		_scratchMove: function(a) {
			this.ctx.lineTo(a.pageX, a.pageY), this.ctx.stroke()
		},
		_scratchUp: function() {
			this.ctx.closePath()
		}
	}, a.support.canvas = document.createElement("canvas").getContext, a.fn.wScratchPad = function(c, d) {
		function e() {
			var d = a.data(this, "wScratchPad");
			return d || (d = new b(this, a.extend(!0, {}, c)), a.data(this, "wScratchPad", d)), d
		}
		if ("string" == typeof c) {
			var f, g = [],
				h = (void 0 !== d ? "set" : "get") + c.charAt(0).toUpperCase() + c.substring(1),
				i = function() {
					f.options[c] && (f.options[c] = d), f[h] && f[h].apply(f, [d])
				},
				j = function() {
					return f[h] ? f[h].apply(f, [d]) : f.options[c] ? f.options[c] : void 0
				},
				k = function() {
					f = a.data(this, "wScratchPad"), f && (f[c] ? f[c].apply(f, [d]) : void 0 !== d ? i() : g.push(j()))
				};
			return this.each(k), g.length ? 1 === g.length ? g[0] : g : this
		}
		return c = a.extend({}, a.fn.wScratchPad.defaults, c), this.each(e)
	}, a.fn.wScratchPad.defaults = {
		size: 5,
		bg: "#cacaca",
		fg: "#6699ff",
		realtime: !0,
		scratchDown: null,
		scratchUp: null,
		scratchMove: null,
		cursor: "crosshair"
	}, a.fn.bindMobileEvents = function() {
		a(this).on("touchstart touchmove touchend touchcancel", function(a) {
			var b = a.changedTouches || a.originalEvent.targetTouches,
				c = b[0],
				d = "";
			switch (a.type) {
				case "touchstart":
					d = "mousedown";
					break;
				case "touchmove":
					d = "mousemove", a.preventDefault();
					break;
				case "touchend":
					d = "mouseup";
					break;
				default:
					return
			}
			var e = document.createEvent("MouseEvent");
			e.initMouseEvent(d, !0, !0, window, 1, c.screenX, c.screenY, c.clientX, c.clientY, !1, !1, !1, !1, 0, null), c.target.dispatchEvent(e)
		})
	}
}(jQuery);
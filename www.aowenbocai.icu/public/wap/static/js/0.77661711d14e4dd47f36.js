webpackJsonp([0], {
	"/xp6": function(t, e) {},
	"2OhQ": function(t, e) {},
	"3IaI": function(t, e, s) {
		"use strict";
		
		function timestampToTime(timestamp) {
 
timestamp=timestamp*1000;
    const hours = parseInt((timestamp / 1000 / 60 / 60) % 24); // 转换为小时
    const minutes = parseInt((timestamp / 1000 / 60) % 60); // 转换为分钟
    const seconds = parseInt((timestamp / 1000) % 60); // 转换为秒钟
    const formattedTime =hours + ":" + ("0" + minutes).slice(-2) +":" + ("0" +
    seconds).slice(-2);
    return formattedTime;
}
		var i = {
				name: "downTime",
				data: function() {
					return {
						show: !1,
						changeNum: "",
						num: ""
					}
				},
				filters: {
					subStr: function(t) {
						return t.toString()
							.slice(4)
					},
					kjIssue: function(t) {
						return t.toString()
							.slice(8)
					}
				},
				computed: {
					expectType: function() {
						return this.$store.state.lottery.expect_type
					},
					name: function() {
						return this.$route.query.name
					},
					cz: function() {
						var t = this.$route.path.replace("/", "");
						return "ssc" == t || "syxw" == t || "pk10" == t || "ks" == t || "pc28" == t || "fc3" == t ? t : this.$route.query.cz
					},
					downTime: function() {
						return this.$store.state.lottery.info.time
					},
					sortExpect: function() {
						return this.$store.state.lottery.info.sort_expect
					},
					expect: function() {
						return this.$store.state.lottery.info.expect
					},
					lastIssue: function() {
						return this.$store.state.lottery.info.lastIssue
					},
					timer: function() {
					    if(this.name=='ynssc'||this.name=='jlssc')
					    	{
					    	    return 0 == this.downTime ? "00:00" : timestampToTime(this.downTime)
					    	}
						return 0 == this.downTime ? "00:00" : this.$bet.formatTime("i:s", this.downTime)
					},
					intervalTime: function() {
						return this.$store.state.intervalTime
					},
					url: function() {
						return "/index/" + this.cz + "/getIssueInfo/name/" + this.name
					},
					timelong: function() {
						return this.$store.state.lottery.info.timelong
					},
					codeArr: function() {
						return this.$store.state.lottery.recent_open
					},
					isGetCode: function() {
						return this.$store.state.lottery.isGetCode || !1
					},
					awardNumber: function() {
						return this.$store.state.lottery.info.awardNumber.split(",")
					},
					CodeXt: function() {
						if ("pc28" == this.cz) {
							var t, e, s = 0;
							for (var i in this.awardNumber) s = Number(s) + Number(this.awardNumber[i]);
							t = s >= 14 ? "大" : "小", e = s % 2 == 0 ? "双" : "单";
							var n = "";
							return [3, 6, 9, 12, 15, 18, 21, 24].indexOf(s) > -1 && (n = "#ff0000"), [2, 5, 8, 11, 17, 20, 23, 26].indexOf(s) > -1 && (n = "#2388f5"), [1, 4, 7, 10, 16, 19, 22, 25].indexOf(s) > -1 && (n = "#12c231"), [0, 13, 14, 27].indexOf(s) > -1 && (n = "#999999"), {
								he: s,
								xt: t + "," + e,
								bg_color: n
							}
						}
					}
				},
				watch: {
					downTime: function(t) {
						var e = this;
						0 == t && "" !== t && (this.$store.commit("clearNewCode"), this.$store.commit("clearRandomNum"), this.$axios(this.url)
							.then(function(t) {
								var s = t.data;
								e.$store.commit("setBetData", s.data), e.$emit("change-time"), e.$messagebox("提示", "期次已更改，当前第" + e.sortExpect + "期"), setTimeout(function() {
									e.$messagebox.close()
								}, 1e4), e.$store.commit("setAwardNumber", s.data.awardNumber.code), e.$store.commit("setRecentOpen", s.data.open), e.$store.commit("isGetNewCode", s.data.getnewcode), e.rMoveNum();
								var i = s.data.timelong,
									n = setTimeout(function() {
										e.getNewCode(), clearTimeout(n)
									}, i < 5 ? 1e4 : 6e4)
							}))
					}
				},
				methods: {
					timeInterval: function() {
						var t = this;
						this.$store.state.intervalTime = setInterval(function() {
							t.$store.commit("setDownTime")
						}, 1e3)
					},
					getRandomNum: function() {
						var t = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
						"syxw" == this.cz && (t = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11"]), "ssc" == this.cz && (t = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"]), "fc3" == this.cz && (t = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"]), "pk10" == this.cz && (t = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10"]), "pc28" == this.cz && (t = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10"]), this.num = this.$bet.getRandomArrayEle(t, 1)
							.join(","), Number(this.changeNum) == Number(this.num) ? this.getRandomNum() : this.changeNum = this.num
					},
					rMoveNum: function() {
						var t = this;
						this.$store.state.rNumTimer = setInterval(function() {
							t.getRandomNum()
						}, 100)
					},
					getNewCode: function() {
						var t = this;
						this.$store.commit("clearNewCode");
						var e = this.timelong < 5 ? 5e3 : 1e4;
						this.$store.state.newCodeFun = setInterval(function() {
							t.$axios.get("/index/" + t.cz + "/getNewCode", {
									params: {
										name: t.name,
										issue: t.lastIssue
									}
								})
								.then(function(e) {
									var s = e.data;
									s.err || (t.$store.commit("setRecentOpen", s.data.tenCode), t.$store.commit("setAwardNumber", s.data.codeOpen), t.$store.commit("clearNewCode"), t.$store.commit("clearRandomNum"), t.$store.commit("isGetNewCode", !1), "ks" === t.cz && "pc28" === t.cz || t.$axios.get("/index/" + t.cz + "/getmiss", {
											params: {
												name: t.name
											}
										})
										.then(function(e) {
											var s = e.data;
											s.err || t.$store.commit("setMiss", s.data)
										})
										.catch(function(t) {
											var e = t.error;
											console.log(e)
										}))
								})
								.catch(function(t) {
									var e = t.error;
									console.log(e)
								})
						}, e)
					},
					initData: function() {
						var t = this;
						"/betOrder" == this.$route.path ? this.$axios(this.url)
							.then(function(e) {
								var s = e.data;
								t.$store.commit("setBetData", s.data), t.$store.commit("setAwardNumber", s.data.awardNumber.code), t.$store.commit("setRecentOpen", s.data.open), t.$store.commit("isGetNewCode", s.data.getnewcode), t.$store.commit("setType", s.data.expect_type), t.$store.commit("setFirstIssue", s.data.firstIssue), t.$store.commit("clearDownTime"), t.timeInterval(), s.data.getnewcode && (t.$store.commit("clearNewCode"), t.$store.commit("clearRandomNum"), t.getNewCode(), t.rMoveNum())
							})
							.catch(function(t) {
								var e = t.error;
								console.log(e)
							}) : (this.$store.commit("clearDownTime"), this.timeInterval(), this.$store.state.lottery.isGetCode && (this.$store.commit("clearNewCode"), this.$store.commit("clearRandomNum"), this.getNewCode(), this.rMoveNum()))
					}
				},
				created: function() {
					this.initData()
				},
				activated: function() {
					this.$store.commit("clearRandomNum"), this.$store.commit("clearNewCode"), this.$store.state.lottery.isGetCode && (this.rMoveNum(), this.getNewCode())
				}
			},
			n = {
				render: function() {
					var t = this,
						e = t.$createElement,
						s = t._self._c || e;
						
					
					return s("div", {
						staticClass: "top-info head"
					}, [s("div", {
						staticClass: "flex-box time-info border-bottom-1px"
					}, [s("div", {
						staticClass: "flex info-kjCode",
						on: {
							click: function(e) {
								t.show = !t.show
							}
						}
					}, [s("div", {
						staticClass: "tc kj-title"
					}, [t.expectType ? [t._v("第" + t.lastIssue + "期开奖 :")] : [t._v("第" + t.lastIssue + "期开奖 :")]], 2), t._v(" "), s("div", {
						staticClass: "tc"
					}, ["ssc" == t.cz || "fc3" == t.cz || "syxw" == t.cz ? [t._l(5, function(e) {
						return t.isGetCode ? s("em", {
							staticClass: "ball-open"
						}, [t._v(t._s(t.changeNum))]) : t._e()
					}), t._v(" "), t._l(t.awardNumber, function(e) {
						return t.isGetCode ? t._e() : s("em", {
							staticClass: "ball-open"
						}, [t._v(t._s(e))])
					})] : t._e(), t._v(" "), "pk10" == t.cz ? [t._l(10, function(e) {
						return t.isGetCode ? s("em", {
							staticClass: "pk-ball"
						}, [s("em", [t._v(t._s(t.changeNum))])]) : t._e()
					}), t._v(" "), t._l(t.awardNumber, function(e) {
						return t.isGetCode ? t._e() : s("em", {
							staticClass: "pk-ball"
						}, [s("em", {
							class: "code-" + parseInt(e)
						}, [t._v(t._s(e))])])
					})] : t._e(), t._v(" "), "pc28" == t.cz ? [t.isGetCode ? s("em", {
						staticClass: "pk-ball pc28-code"
					}, [s("em", [t._v(t._s(t.changeNum))]), s("i", [t._v("+")]), s("em", [t._v(t._s(t.changeNum))]), s("i", [t._v("+")]), s("em", [t._v(t._s(t.changeNum))]), s("i", [t._v("=")]), s("em", [t._v("?")]), t._v(" "), s("span")]) : t._e(), t._v(" "), t.isGetCode ? t._e() : s("em", {
						staticClass: "pk-ball pc28-code"
					}, [s("em", [t._v(t._s(t.awardNumber[0]))]), s("i", [t._v("+")]), s("em", [t._v(t._s(t.awardNumber[1]))]), s("i", [t._v("+")]), s("em", [t._v(t._s(t.awardNumber[2]))]), s("i", [t._v("=")]), s("em", {
						staticClass: "res",
						style: {
							backgroundColor: t.CodeXt.bg_color
						}
					}, [t._v(t._s(t.CodeXt.he))]), t._v(" "), s("span", {
						staticClass: "f-mini"
					}, [t._v(t._s(t.CodeXt.xt))])])] : t._e(), t._v(" "), s("i", {
						staticClass: "iconfont icon-jiantou c-3",
						class: t.show ? "is-active" : "no-active"
					})], 2)]), t._v(" "), s("div", {
						staticClass: "info-downTime",
						class: {
							"pk-downTime": "pk10" == t.cz
						}
					}, [s("div", {
						staticClass: "time-info tc f-sm"
					}, [s("div", {
						staticClass: "tc kj-title"
					}, [t._v("\n                    距第" + t._s(t.sortExpect) + "期截止\n                ")]), t._v(" "), s("div", {
						staticClass: "tc kj-time"
					}, [t._v("\n                    " + t._s(t.timer) + "\n                ")])])])]), t._v(" "), s("div", {
						directives: [{
							name: "show",
							rawName: "v-show",
							value: t.show,
							expression: "show"
						}],
						staticClass: "layout z-1",
						on: {
							click: function(e) {
								t.show = !t.show
							}
						}
					}, [s("div", {
						staticClass: "recent-open-box"
					}, ["syxw" == t.cz ? [s("table", {
						directives: [{
							name: "show",
							rawName: "v-show",
							value: t.show,
							expression: "show"
						}],
						staticClass: "table-list",
						attrs: {
							cellpadding: "0",
							cellspacing: "0"
						}
					}, [t._m(0), t._v(" "), t._l(t.codeArr, function(e, i) {
						return i < 10 ? s("tr", {
							key: i
						}, [t.expectType ? s("td", [t._v(t._s(e.expect))]) : s("td", [t._v(t._s(t._f("subStr")(e.expect)))]), t._v(" "), s("td", t._l(e.code, function(e) {
							return s("span", {
								staticClass: "ball-sm"
							}, [t._v(t._s(e))])
						}), 0), t._v(" "), s("td", [t._v(t._s(e.dx))]), t._v(" "), s("td", [t._v(t._s(e.jo))])]) : t._e()
					})], 2)] : t._e(), t._v(" "), "ssc" == t.cz ? [s("table", {
						directives: [{
							name: "show",
							rawName: "v-show",
							value: t.show,
							expression: "show"
						}],
						staticClass: "table-list",
						attrs: {
							cellpadding: "0",
							cellspacing: "0"
						}
					}, [t._m(1), t._v(" "), t._l(t.codeArr, function(e, i) {
						return i < 10 ? s("tr", {
							key: i
						}, [t.expectType ? s("td", [t._v(t._s(e.expect))]) : s("td", [t._v(t._s(t._f("subStr")(e.expect)))]), t._v(" "), s("td", t._l(e.code, function(e) {
							return s("span", {
								staticClass: "ball-sm"
							}, [t._v(t._s(e))])
						}), 0), t._v(" "), s("td", [t._v(t._s(e.shi))]), t._v(" "), s("td", [t._v(t._s(e.ge))]), t._v(" "), s("td", [t._v(t._s(e.hs))])]) : t._e()
					})], 2)] : t._e(), t._v(" "), "pk10" == t.cz ? [s("table", {
						directives: [{
							name: "show",
							rawName: "v-show",
							value: t.show,
							expression: "show"
						}],
						staticClass: "table-list",
						attrs: {
							cellpadding: "0",
							cellspacing: "0"
						}
					}, [t._m(2), t._v(" "), t._l(t.codeArr, function(e, i) {
						return i < 10 ? s("tr", {
							key: i
						}, [t.expectType ? s("td", [t._v(t._s(e.expect))]) : s("td", [t._v(t._s(t._f("subStr")(e.expect)))]), t._v(" "), s("td", t._l(e.code, function(e) {
							return s("span", {
								staticClass: "pk-ball"
							}, [s("em", {
								class: "code-" + parseInt(e)
							}, [t._v(t._s(e))])])
						}), 0)]) : t._e()
					})], 2)] : t._e(), t._v(" "), "pc28" == t.cz ? [s("table", {
						directives: [{
							name: "show",
							rawName: "v-show",
							value: t.show,
							expression: "show"
						}],
						staticClass: "table-list",
						attrs: {
							cellpadding: "0",
							cellspacing: "0"
						}
					}, [t._m(3), t._v(" "), t._l(t.codeArr, function(e, i) {
						return i < 10 ? s("tr", {
							key: i
						}, [t.expectType ? s("td", [t._v(t._s(e.expect))]) : s("td", [t._v(t._s(t._f("subStr")(e.expect)))]), t._v(" "), s("td", [s("span", {
							staticClass: "ball-sm",
							staticStyle: {
								"background-color": "#b5b5b5"
							}
						}, [t._v(t._s(e.code[0]))]), t._v(" "), s("em", [t._v("+")]), t._v(" "), s("span", {
							staticClass: "ball-sm",
							staticStyle: {
								"background-color": "#b5b5b5"
							}
						}, [t._v(t._s(e.code[1]))]), t._v(" "), s("em", [t._v("+")]), t._v(" "), s("span", {
							staticClass: "ball-sm",
							staticStyle: {
								"background-color": "#b5b5b5"
							}
						}, [t._v(t._s(e.code[2]))]), t._v(" "), s("em", [t._v("=")]), t._v(" "), s("span", {
							staticClass: "ball-sm ball-res",
							style: {
								backgroundColor: t.$store.getters.pcResult(e.code)
									.bg_color
							}
						}, [t._v(t._s(e.he))])]), t._v(" "), s("td", [t._v(t._s(e.xt[0]) + "," + t._s(e.xt[1]))]), t._v(" "), s("td", [s("span", {
							staticClass: "pc-ball-res",
							style: {
								backgroundColor: t.$store.getters.pcResult(e.code)
									.bg_color
							}
						}, [t._v(t._s(t.$store.getters.pcResult(e.code)
							.text_color))])]), t._v(" "), s("td", [t._v(t._s(t.$store.getters.pcResult(e.code)
							.jz))])]) : t._e()
					})], 2)] : t._e(), t._v(" "), s("div", {
						staticClass: "tc c-3 foot-tip"
					}, [t._v("已显示近10期开奖")])], 2)])])
				},
				staticRenderFns: [function() {
					var t = this.$createElement,
						e = this._self._c || t;
					return e("tr", [e("th", {
						attrs: {
							width: "20%"
						}
					}, [this._v("期号")]), this._v(" "), e("th", {
						attrs: {
							width: "50%"
						}
					}, [this._v("开奖号码")]), this._v(" "), e("th", {
						attrs: {
							width: "15%"
						}
					}, [this._v("大小比")]), this._v(" "), e("th", {
						attrs: {
							width: "15%"
						}
					}, [this._v("奇偶比")])])
				}, function() {
					var t = this,
						e = t.$createElement,
						s = t._self._c || e;
					return s("tr", [s("th", {
						attrs: {
							width: "20%"
						}
					}, [t._v("期号")]), t._v(" "), s("th", {
						attrs: {
							width: "35%"
						}
					}, [t._v("开奖号码")]), t._v(" "), s("th", {
						attrs: {
							width: "15%"
						}
					}, [t._v("十位")]), t._v(" "), s("th", {
						attrs: {
							width: "15%"
						}
					}, [t._v("个位")]), t._v(" "), s("th", {
						attrs: {
							width: "15%"
						}
					}, [t._v("后三")])])
				}, function() {
					var t = this.$createElement,
						e = this._self._c || t;
					return e("tr", [e("th", {
						attrs: {
							width: "25%"
						}
					}, [this._v("期号")]), this._v(" "), e("th", {
						attrs: {
							width: "75%"
						}
					}, [this._v("开奖号码")])])
				}, function() {
					var t = this,
						e = t.$createElement,
						s = t._self._c || e;
					return s("tr", [s("th", {
						attrs: {
							width: "18%"
						}
					}, [t._v("期数")]), t._v(" "), s("th", {
						attrs: {
							width: "45%"
						}
					}, [t._v("开奖号码")]), t._v(" "), s("th", {
						attrs: {
							width: "15%"
						}
					}, [t._v("形态")]), t._v(" "), s("th", [t._v("色波")]), t._v(" "), s("th", [t._v("极值")])])
				}]
			};
		var a = s("VU/8")(i, n, !1, function(t) {
			s("536n")
		}, "data-v-5c01d9a2", null);
		e.a = a.exports
	},
	"3PkU": function(t, e) {},
	"536n": function(t, e) {},
	"7nvY": function(t, e) {},
	"9a9e": function(t, e, s) {
		"use strict";
		var i = {
			render: function() {
				var t = this,
					e = t.$createElement,
					s = t._self._c || e;
				return s("div", {
					directives: [{
						name: "infinite-scroll",
						rawName: "v-infinite-scroll",
						value: t.loadMore,
						expression: "loadMore"
					}],
					attrs: {
						"infinite-scroll-disabled": "loadState",
						"infinite-scroll-distance": "60"
					}
				}, [s("div", [t._t("default", null, {
					data: t.pageData
				})], 2), t._v(" "), s("div", {
					staticClass: "tc f-sm flex-box loading c-3"
				}, ["暂无相关数据" !== t.loadText ? [t.loading ? s("mt-spinner", {
					attrs: {
						type: "fading-circle",
						size: 22
					}
				}) : t._e(), t._v(" "), s("span", [t._v(t._s(t.loadText))])] : [s("div", {
					staticClass: "no-data"
				}, [t._m(0), t._v(" "), s("div", [t._v(t._s(t.loadText))])])]], 2)])
			},
			staticRenderFns: [function() {
				var t = this.$createElement,
					e = this._self._c || t;
				return e("div", {
					staticClass: "tc mf-sm"
				}, [e("i", {
					staticClass: "iconfont icon-icon"
				})])
			}]
		};
		var n = s("VU/8")({
			name: "page",
			props: ["url"],
			data: function() {
				return {
					pageData: [],
					loading: !0,
					loadText: "加载更多",
					currentPage: 1,
					lastPage: "",
					count: 0
				}
			},
			computed: {
				symbol: function() {
					return this.url.indexOf("?") > -1 ? "&" : "?"
				},
				loadState: function() {
					return this.$store.state.page.loadState
				}
			},
			watch: {
				url: function() {
					this.pageData = [], this.currentPage = 1, this.lastPage = "", this.getPageList()
				}
			},
			methods: {
				getPageList: function(t) {
					var e = this;
					this.loading = !0, this.loadText = "加载中..", this.$axios(this.url + this.symbol + "page=" + this.currentPage)
						.then(function(s) {
							var i = s.data;
							if (i.total) {
								if (t)
									for (var n in i.data) e.pageData.push(i.data[n]);
								else e.$set(e, "pageData", i.data);
								i.current_page == i.last_page ? (e.$store.commit("setPageLoad", !0), e.loadText = "没有更多了") : (e.$store.commit("setPageLoad", !1), e.loadText = "加载更多"), e.currentPage = i.current_page, e.lastPage = i.last_page
							} else e.loadText = "暂无相关数据";
							e.loading = !1
						})
				},
				loadMore: function() {
					this.$store.commit("setPageLoad", !0), this.currentPage++, this.getPageList(!0)
				}
			},
			activated: function() {
				var t = this.currentPage == this.lastPage;
				this.$store.commit("setPageLoad", t)
			},
			created: function() {
				this.$store.commit("setPageLoad", !0), this.getPageList()
			}
		}, i, !1, function(t) {
			s("XsLT")
		}, "data-v-0f84a1aa", null);
		e.a = n.exports
	},
	BK5K: function(t, e, s) {
		"use strict";
		var i = {
				name: "searchCont",
				props: {
					inputValue: {
						type: String
					},
					placeholder: {
						type: String,
						default: "搜索"
					}
				},
				model: {
					prop: "inputValue",
					event: "change"
				},
				data: function() {
					return {
						value: this.inputValue
					}
				},
				watch: {
					inputValue: function(t) {
						"" == t && (this.value = "")
					},
					value: function(t) {
						this.$emit("change", t)
					}
				},
				methods: {
					search: function() {
						this.$emit("search")
					}
				}
			},
			n = {
				render: function() {
					var t = this,
						e = t.$createElement,
						s = t._self._c || e;
					return s("div", {
						staticClass: "searchCont"
					}, [s("input", {
						directives: [{
							name: "model",
							rawName: "v-model",
							value: t.value,
							expression: "value"
						}],
						staticClass: "searchInput",
						attrs: {
							type: "text",
							placeholder: t.placeholder
						},
						domProps: {
							value: t.value
						},
						on: {
							keyup: function(e) {
								return !e.type.indexOf("key") && t._k(e.keyCode, "enter", 13, e.key, "Enter") ? null : t.search(e)
							},
							input: function(e) {
								e.target.composing || (t.value = e.target.value)
							}
						}
					}), t._v(" "), s("span", {
						staticClass: "searchBtn",
						on: {
							click: t.search
						}
					}, [s("i", {
						staticClass: "iconfont icon-sousuo"
					})])])
				},
				staticRenderFns: []
			};
		var a = s("VU/8")(i, n, !1, function(t) {
			s("ketd")
		}, "data-v-305d8d4d", null);
		e.a = a.exports
	},
	DDNP: function(t, e) {},
	DOtQ: function(t, e) {},
	"G+zW": function(t, e) {},
	GXDb: function(t, e) {},
	HnWP: function(t, e, s) {
		"use strict";
		var i = {
				components: {
					inputNumber: s("XYHu")
						.a
				},
				data: function() {
					return {
						popupVisible: !1,
						label: "元",
						value: 1,
						multiple: 1
					}
				},
				watch: {
					multiple: function(t) {
						this.$store.commit("changeMulValue", t)
					}
				},
				computed: {
					modelChoseOption: function() {
						return this.$store.state.setting.mode_unit_value
					},
					getLable: {
						get: function() {
							return this.$store.state.lottery.label
						},
						set: function(t) {
							this.label = t
						}
					},
					getValue: {
						get: function() {
							return this.$store.state.lottery.value
						},
						set: function(t) {
							this.value = t
						}
					},
					getMultiple: {
						get: function() {
							return this.$store.state.lottery.multiple
						},
						set: function(t) {
							this.multiple = t
						}
					},
					scale: function() {
						return this.$store.getters.getScale
					},
					lotteryUnit: function() {
						return this.$store.state.setting.lottery_unit
					}
				},
				methods: {
					showModel: function() {
						this.$messagebox({
							title: "提示",
							message: "1元=1" + this.lotteryUnit + " ; 1角=0.1" + this.lotteryUnit + " ; 1分=0.01" + this.lotteryUnit + " ; 1厘=0.001" + this.lotteryUnit,
							confirmButtonText: "我知道了"
						})
					},
					choseModel: function(t, e) {
						this.value = e, this.label = t;
						var s = [t, e];
						this.popupVisible = !1, this.$store.commit("changeModelValue", s)
					},
					handleMul: function() {
						this.getMultiple = parseInt(this.getMultiple) || 1
					}
				}
			},
			n = {
				render: function() {
					var t = this,
						e = t.$createElement,
						s = t._self._c || e;
					return s("div", {
						staticClass: "flex-box"
					}, [s("input", {
						directives: [{
							name: "model",
							rawName: "v-model",
							value: t.getMultiple,
							expression: "getMultiple"
						}],
						staticClass: "input-mul",
						attrs: {
							type: "tel"
						},
						domProps: {
							value: t.getMultiple
						},
						on: {
							change: t.handleMul,
							input: function(e) {
								e.target.composing || (t.getMultiple = e.target.value)
							}
						}
					}), t._v(" "), s("em", {
						staticClass: "c-w f-sm"
					}, [t._v("倍")]), t._v(" "), s("div", {
						staticClass: "ks-model f-sm"
					}, [s("span", {
						on: {
							click: function(e) {
								t.popupVisible = !0
							}
						}
					}, [t._v(t._s(t.getLable) + " "), s("i", {
						staticClass: "iconfont icon-jiantou c-3 f-mini"
					})]), t._v(" 模式\n        ")]), t._v(" "), s("mt-popup", {
						attrs: {
							position: "bottom"
						},
						model: {
							value: t.popupVisible,
							callback: function(e) {
								t.popupVisible = e
							},
							expression: "popupVisible"
						}
					}, [s("div", {
						staticClass: "options-list"
					}, t._l(t.modelChoseOption, function(e, i) {
						return s("span", {
							class: {
								active: e.value == t.getValue
							},
							on: {
								click: function(s) {
									return t.choseModel(e.label, e.value)
								}
							}
						}, [t._v(t._s(e.label))])
					}), 0)])], 1)
				},
				staticRenderFns: []
			};
		var a = s("VU/8")(i, n, !1, function(t) {
			s("O3In")
		}, "data-v-28419446", null);
		e.a = a.exports
	},
	Lpxy: function(t, e) {},
	NI25: function(t, e) {},
	O3In: function(t, e) {},
	PGEL: function(t, e, s) {
		"use strict";
		var i = {
				data: function() {
					return {
						num: 0,
						animate: !1,
						items: []
					}
				},
				computed: {
					newNavClassId: function() {
						return this.$store.state.newNavClassId
					}
				},
				methods: {
					scroll: function() {
						var t = this;
						this.animate = !0, setTimeout(function() {
							t.items.push(t.items[0]), t.items.shift(), t.animate = !1
						}, 500)
					}
				},
				created: function() {
					var t = this;
					this.$axios.get("/news/index/getnewlist/nav_id/" + this.newNavClassId.notice_id)
						.then(function(e) {
							var s = e.data;
							t.$store.commit("setNewsPrize", s.data), t.$set(t, "items", s.data.slice(0)), setInterval(t.scroll, 3e3)
						})
				}
			},
			n = {
				render: function() {
					var t = this,
						e = t.$createElement,
						s = t._self._c || e;
					return s("div", {
						staticClass: "news-prize flex-box"
					}, [s("div", {
						staticClass: "marquee_box flex"
					}, [s("ul", {
						staticClass: "marquee_list",
						class: {
							anim: 1 == t.animate
						}
					}, t._l(t.items, function(e, i) {
						return s("li", {
							key: i
						}, [s("router-link", {
							staticClass: "name",
							attrs: {
								tag: "span",
								to: "/news/detail?navid=" + e.nav_id + "&id=" + e.id
							}
						}, [s("i", {
							staticClass: "iconfont icon-tongzhi"
						}), t._v(" " + t._s(e.title) + "\n                ")])], 1)
					}), 0)])])
				},
				staticRenderFns: []
			};
		var a = s("VU/8")(i, n, !1, function(t) {
			s("DOtQ")
		}, "data-v-1c6b561e", null);
		e.a = a.exports
	},
	PJHk: function(t, e, s) {
		"use strict";
		var i = {
			render: function() {
				var t = this,
					e = t.$createElement,
					s = t._self._c || e;
				return s("div", {
					staticClass: "recent-open-box"
				}, ["syxw" == t.cz ? [s("table", {
					directives: [{
						name: "show",
						rawName: "v-show",
						value: t.show,
						expression: "show"
					}],
					staticClass: "table-list",
					attrs: {
						cellpadding: "0",
						cellspacing: "0"
					}
				}, [t._m(0), t._v(" "), t._l(t.codeArr, function(e, i) {
					return s("tr", {
						key: i
					}, [s("td", [t._v(t._s(t._f("subStr")(e.expect)))]), t._v(" "), s("td", t._l(e.code, function(e) {
						return s("span", {
							staticClass: "code"
						}, [t._v(t._s(e))])
					}), 0), t._v(" "), s("td", [t._v(t._s(e.dx))]), t._v(" "), s("td", [t._v(t._s(e.jo))])])
				})], 2)] : t._e(), t._v(" "), "ssc" == t.cz || "fc3" == t.cz ? [s("table", {
					directives: [{
						name: "show",
						rawName: "v-show",
						value: t.show,
						expression: "show"
					}],
					staticClass: "table-list",
					attrs: {
						cellpadding: "0",
						cellspacing: "0"
					}
				}, [t._m(1), t._v(" "), t._l(t.codeArr, function(e, i) {
					return s("tr", {
						key: i
					}, [s("td", [t._v(t._s(t._f("subStr")(e.expect)))]), t._v(" "), s("td", t._l(e.code, function(e) {
						return s("span", {
							staticClass: "code"
						}, [t._v(t._s(e))])
					}), 0), t._v(" "), s("td", [t._v(t._s(e.shi))]), t._v(" "), s("td", [t._v(t._s(e.ge))]), t._v(" "), s("td", [t._v(t._s(e.hs))])])
				})], 2)] : t._e(), t._v(" "), "pk10" == t.cz ? [s("table", {
					directives: [{
						name: "show",
						rawName: "v-show",
						value: t.show,
						expression: "show"
					}],
					staticClass: "table-list",
					attrs: {
						cellpadding: "0",
						cellspacing: "0"
					}
				}, [t._m(2), t._v(" "), t._l(t.codeArr, function(e, i) {
					return s("tr", {
						key: i
					}, [s("td", [t._v(t._s(e.expect))]), t._v(" "), s("td", t._l(e.codeInfo, function(e) {
						return s("span", {
							staticClass: "code"
						}, [t._v(t._s(e))])
					}), 0)])
				})], 2)] : t._e(), t._v(" "), s("div", {
					staticClass: "f-mini show-more",
					on: {
						click: function(e) {
							t.show = !t.show
						}
					}
				}, [s("span", {
					staticClass: "border-1px"
				}, [t._v("近期开奖 "), s("i", {
					staticClass: "iconfont icon-xialajiantou",
					class: {
						rotate: t.show
					}
				})])])], 2)
			},
			staticRenderFns: [function() {
				var t = this.$createElement,
					e = this._self._c || t;
				return e("tr", [e("th", {
					attrs: {
						width: "20%"
					}
				}, [this._v("期号")]), this._v(" "), e("th", {
					attrs: {
						width: "50%"
					}
				}, [this._v("开奖号码")]), this._v(" "), e("th", {
					attrs: {
						width: "15%"
					}
				}, [this._v("大小比")]), this._v(" "), e("th", {
					attrs: {
						width: "15%"
					}
				}, [this._v("奇偶比")])])
			}, function() {
				var t = this,
					e = t.$createElement,
					s = t._self._c || e;
				return s("tr", [s("th", {
					attrs: {
						width: "20%"
					}
				}, [t._v("期号")]), t._v(" "), s("th", {
					attrs: {
						width: "35%"
					}
				}, [t._v("开奖号码")]), t._v(" "), s("th", {
					attrs: {
						width: "15%"
					}
				}, [t._v("十位")]), t._v(" "), s("th", {
					attrs: {
						width: "15%"
					}
				}, [t._v("个位")]), t._v(" "), s("th", {
					attrs: {
						width: "15%"
					}
				}, [t._v("后三")])])
			}, function() {
				var t = this.$createElement,
					e = this._self._c || t;
				return e("tr", [e("th", {
					attrs: {
						width: "25%"
					}
				}, [this._v("期号")]), this._v(" "), e("th", {
					attrs: {
						width: "75%"
					}
				}, [this._v("开奖号码")])])
			}]
		};
		var n = s("VU/8")({
			data: function() {
				return {
					show: !1
				}
			},
			computed: {
				name: function() {
					return this.$route.query.name
				},
				downTime: function() {
					return this.$store.state.lottery.info.time
				},
				sortExpect: function() {
					return this.$store.state.lottery.info.sort_expect
				},
				codeArr: function() {
					return this.$store.state.lottery.recent_open
				},
				timer: function() {
					return 0 == this.downTime ? "00:00" : this.$bet.formatTime("i:s", this.downTime)
				},
				intervalTime: function() {
					return this.$store.state.intervalTime
				},
				cz: function() {
					var t = this.$route.path.replace("/", "");
					return "betOrder" == t ? this.$route.query.cz : t
				}
			},
			filters: {
				subStr: function(t) {
					return t.toString()
						.slice(4)
				}
			},
			methods: {},
			created: function() {}
		}, i, !1, function(t) {
			s("oNuw")
		}, "data-v-4044484c", null);
		e.a = n.exports
	},
	R4wc: function(t, e, s) {
		var i = s("kM2E");
		i(i.S + i.F, "Object", {
			assign: s("To3L")
		})
	},
	Rgkj: function(t, e) {},
	To3L: function(t, e, s) {
		"use strict";
		var i = s("lktj"),
			n = s("1kS7"),
			a = s("NpIQ"),
			o = s("sB3e"),
			r = s("MU5D"),
			c = Object.assign;
		t.exports = !c || s("S82l")(function() {
			var t = {},
				e = {},
				s = Symbol(),
				i = "abcdefghijklmnopqrst";
			return t[s] = 7, i.split("")
				.forEach(function(t) {
					e[t] = t
				}), 7 != c({}, t)[s] || Object.keys(c({}, e))
				.join("") != i
		}) ? function(t, e) {
			for (var s = o(t), c = arguments.length, l = 1, u = n.f, h = a.f; c > l;)
				for (var m, d = r(arguments[l++]), v = u ? i(d)
					.concat(u(d)) : i(d), _ = v.length, f = 0; _ > f;) h.call(d, m = v[f++]) && (s[m] = d[m]);
			return s
		} : c
	},
	TrHE: function(t, e, s) {
		"use strict";
		var i = {
				props: ["gain"],
				data: function() {
					return {}
				},
				computed: {
					cz: function() {
						return this.$route.path.replace("/", "")
					},
					rebate: function() {
						return Number(this.$store.state.lottery.bonus_base)
					},
					userRebate: function() {
						return this.$store.state.lottery.userRebate || 0
					},
					rebateVal: function() {
						return this.$store.getters.rebateVal
					},
					sliderValue: {
						get: function() {
							return this.$store.state.lottery.sliderValue
						},
						set: function(t) {
							this.$store.commit("changeRebateVal", t)
						}
					}
				}
			},
			n = {
				render: function() {
					var t = this,
						e = t.$createElement,
						s = t._self._c || e;
					return s("div", {
						staticClass: "rebate-range-box flex-box",
						staticStyle: {
							"justify-content": "flex-end"
						}
					}, [s("span", {
						staticClass: "f-mini rang-val"
					}, [t._v(t._s(t.rebateVal) + "%")]), t._v(" "), s("input", {
						directives: [{
							name: "model",
							rawName: "v-model",
							value: t.sliderValue,
							expression: "sliderValue"
						}],
						attrs: {
							type: "range",
							min: 0,
							max: t.userRebate,
							step: "0.1"
						},
						domProps: {
							value: t.sliderValue
						},
						on: {
							__r: function(e) {
								t.sliderValue = e.target.value
							}
						}
					}), t._v(" "), s("span", {
						staticClass: "f-mini rang-gain"
					}, [t._v(t._s(t.gain))])])
				},
				staticRenderFns: []
			};
		var a = s("VU/8")(i, n, !1, function(t) {
			s("2OhQ")
		}, "data-v-2c840e79", null);
		e.a = a.exports
	},
	"V/qW": function(t, e, s) {
		"use strict";
		var i = {
				name: "telSafeCheck",
				props: {
					url: {
						type: String,
						require: !0
					},
					bindYzm: {},
					checkWay: {
						type: Number,
						default: 1
					}
				},
				model: {
					prop: "bindYzm",
					event: "change"
				},
				data: function() {
					return {
						numSet: "",
						labelWord: 1 == this.checkWay ? "手机号" : "邮箱",
						yzm: this.bindYzm,
						loading: !1,
						text: "获取验证码",
						countDown: 120,
						yzmState: !0
					}
				},
				computed: {
					num: {
						get: function() {
							return 1 == this.checkWay ? this.$store.state.lockInfo.tel : this.$store.state.lockInfo.email
						},
						set: function(t) {
							this.numSet = t
						}
					}
				},
				watch: {
					yzm: function(t) {
						this.$emit("change", t)
					}
				},
				methods: {
					sendSms: function() {
						var t = this;
						if (120 != this.countDown) return !1;
						this.yzmState = !1, this.text = "发送中...", this.$axios.get(this.url)
							.then(function(e) {
								var s = e.data;
								if (s.err) t.$toast({
									message: s.msg,
									duration: 1500
								}), t.text = "获取验证码", t.yzmState = !0;
								else {
									t.text = "重新发送(" + t.countDown + "s)";
									var i = setInterval(function() {
										if (t.countDown = t.countDown - 1, 0 == t.countDown) return t.text = "获取验证码", t.yzmState = !0, t.countDown = 120, void clearInterval(i);
										t.text = "重新发送(" + t.countDown + "s)"
									}, 1e3);
									t.$toast({
										message: "发送成功",
										duration: 1500
									})
								}
							})
							.catch(function(t) {
								console.log(t)
							})
					}
				}
			},
			n = {
				render: function() {
					var t = this,
						e = t.$createElement,
						s = t._self._c || e;
					return s("div", [s("mt-field", {
						attrs: {
							label: t.labelWord,
							placeholder: "请输入" + t.labelWord,
							disabled: ""
						},
						model: {
							value: t.num,
							callback: function(e) {
								t.num = e
							},
							expression: "num"
						}
					}), t._v(" "), s("mt-field", {
						attrs: {
							label: "验证码",
							placeholder: "请输入验证码",
							type: "tel"
						},
						model: {
							value: t.yzm,
							callback: function(e) {
								t.yzm = e
							},
							expression: "yzm"
						}
					}, [s("div", {
						staticClass: "btn-yzm",
						class: t.yzmState ? "abled" : "disabled",
						on: {
							click: t.sendSms
						}
					}, [t._v(t._s(t.text))])])], 1)
				},
				staticRenderFns: []
			};
		var a = s("VU/8")(i, n, !1, function(t) {
			s("G+zW")
		}, "data-v-010b49b9", null);
		e.a = a.exports
	},
	V3tA: function(t, e, s) {
		s("R4wc"), t.exports = s("FeBl")
			.Object.assign
	},
	XYHu: function(t, e, s) {
		"use strict";
		var i = {
				name: "inputNumber",
				props: {
					max: {
						type: Number / String,
						default: 1 / 0
					},
					min: {
						type: Number,
						default: 1
					},
					value: {
						type: Number / String,
						default: 1
					},
					step: {
						type: Number,
						default: 1
					},
					size: {
						type: String,
						default: "normal"
					}
				},
				data: function() {
					return {
						currentValue: this.value
					}
				},
				watch: {
					currentValue: function(t) {
						this.$emit("input", t), this.$emit("on-change", t)
					},
					value: function(t) {
						this.updateValue(t)
					}
				},
				methods: {
					updateValue: function(t) {
						this.$base.isValueNumber(t) ? t > this.max ? t = this.max : t < this.min ? t = this.min : this.currentValue = t : this.currentValue = this.min
					},
					handleDown: function() {
						this.currentValue -= this.step, this.currentValue <= this.min && (this.currentValue = this.min), this.$emit("handle-down")
					},
					handleUp: function() {
						this.currentValue = Number(this.currentValue) + Number(this.step), this.currentValue >= this.max && (this.currentValue = this.max), this.$emit("handle-up")
					},
					handleChange: function(t) {
						var e = t.target.value.trim(),
							s = this.max,
							i = this.min;
						this.$base.isValueNumber(e) ? e = Number(e) : this.currentValue = i, this.currentValue = e, e > s && (this.$toast({
							message: "最大值为" + s,
							duration: 1e3
						}), this.currentValue = s), e < i && (this.currentValue = i)
					}
				},
				mounted: function() {
					this.updateValue(this.value)
				}
			},
			n = {
				render: function() {
					var t = this,
						e = t.$createElement,
						s = t._self._c || e;
					return s("div", {
						staticClass: "input-number",
						class: {
							"size-small": "small" == t.size
						}
					}, [s("input", {
						staticClass: "number-input",
						attrs: {
							type: "tel"
						},
						domProps: {
							value: t.currentValue
						},
						on: {
							change: t.handleChange,
							keyup: [function(e) {
								return !e.type.indexOf("key") && t._k(e.keyCode, "up", 38, e.key, ["Up", "ArrowUp"]) ? null : t.handleUp(e)
							}, function(e) {
								return !e.type.indexOf("key") && t._k(e.keyCode, "down", 40, e.key, ["Down", "ArrowDown"]) ? null : t.handleDown(e)
							}]
						}
					}), t._v(" "), s("span", {
						staticClass: "input-number_icon input-number__decrease border-right-1px",
						class: {
							"is-disabled": t.currentValue <= t.min
						},
						attrs: {
							role: "button"
						},
						on: {
							click: t.handleDown
						}
					}, [s("i", {
						staticClass: "iconfont icon-jian"
					})]), t._v(" "), s("span", {
						staticClass: "input-number_icon input-number_increase border-left-1px",
						class: {
							"is-disabled": t.currentValue >= t.max
						},
						attrs: {
							role: "button"
						},
						on: {
							click: t.handleUp
						}
					}, [s("i", {
						staticClass: "iconfont icon-jia"
					})])])
				},
				staticRenderFns: []
			};
		var a = s("VU/8")(i, n, !1, function(t) {
			s("7nvY")
		}, "data-v-70884de7", null);
		e.a = a.exports
	},
	XaOq: function(t, e) {},
	XsLT: function(t, e) {},
	YL3v: function(t, e, s) {
		"use strict";
		var i = {
			render: function() {
				var t = this,
					e = t.$createElement,
					s = t._self._c || e;
				return s("div", {
					staticClass: "bet-box"
				}, [s("div", {
					staticClass: "flex-box fast-select-box"
				}, [s("div", {
					staticClass: "fast-title"
				}, [t._v(t._s(t.wzText))]), t._v(" "), s("ul", {
					staticClass: "flex-box fast-list"
				}, [s("li", {
					staticClass: "flex",
					on: {
						click: t.choseAll
					}
				}, [t._v("全")]), t._v(" "), s("li", {
					staticClass: "flex",
					on: {
						click: t.choseBig
					}
				}, [t._v("大")]), t._v(" "), s("li", {
					staticClass: "flex",
					on: {
						click: t.choseSmall
					}
				}, [t._v("小")]), t._v(" "), s("li", {
					staticClass: "flex",
					on: {
						click: t.choseJs
					}
				}, [t._v("奇")]), t._v(" "), s("li", {
					staticClass: "flex",
					on: {
						click: t.choseOs
					}
				}, [t._v("偶")]), t._v(" "), s("li", {
					staticClass: "flex",
					on: {
						click: t.clear
					}
				}, [t._v("清")])])]), t._v(" "), s("div", {
					staticClass: "flex-box bet-box"
				}, [t._m(0), t._v(" "), s("div", {
					staticClass: "flex"
				}, [s("ul", {
					staticClass: "balls-box"
				}, t._l(t.ball, function(e, i) {
					return s("li", {
						key: i
					}, [s("a", {
						staticClass: "balls",
						class: {
							selected: e.selected
						},
						on: {
							click: function(s) {
								return t.doBet(e.num, i)
							}
						}
					}, [t._v(t._s(e.num))]), t._v(" "), 0 !== t.miss ? [s("i", {
						staticClass: "f-mini c-3",
						class: {
							red: 0
						}
					}, [t._v("1")])] : [s("i", {
						staticClass: "f-mini c-3"
					}, [t._v("--")])]], 2)
				}), 0)])])])
			},
			staticRenderFns: [function() {
				var t = this.$createElement,
					e = this._self._c || t;
				return e("div", {
					staticClass: "label"
				}, [e("p", [this._v("选号")]), this._v(" "), e("p", [this._v("遗 漏")])])
			}]
		};
		var n = {
				components: {
					betBalls: s("VU/8")({
							props: ["betNumP", "miss", "wzText"],
							model: {
								prop: "betNumP",
								event: "change"
							},
							data: function() {
								return {
									ball: [{
										num: "0",
										selected: this.betNumP.indexOf("0") > -1 ? 1 : 0
									}, {
										num: "1",
										selected: this.betNumP.indexOf("1") > -1 ? 1 : 0
									}, {
										num: "2",
										selected: this.betNumP.indexOf("2") > -1 ? 1 : 0
									}, {
										num: "3",
										selected: this.betNumP.indexOf("3") > -1 ? 1 : 0
									}, {
										num: "4",
										selected: this.betNumP.indexOf("4") > -1 ? 1 : 0
									}, {
										num: "5",
										selected: this.betNumP.indexOf("5") > -1 ? 1 : 0
									}, {
										num: "6",
										selected: this.betNumP.indexOf("6") > -1 ? 1 : 0
									}, {
										num: "7",
										selected: this.betNumP.indexOf("7") > -1 ? 1 : 0
									}, {
										num: "8",
										selected: this.betNumP.indexOf("8") > -1 ? 1 : 0
									}, {
										num: "9",
										selected: this.betNumP.indexOf("9") > -1 ? 1 : 0
									}],
									betNum: this.betNumP
								}
							},
							computed: {},
							watch: {
								betNumP: function(t) {
									if (!t.length)
										for (var e in this.betNum = [], this.ball) this.ball[e].selected = 0;
									this.$emit("change", t)
								}
							},
							methods: {
								doBet: function(t, e) {
									var s = this.betNum.indexOf(t);
									s > -1 ? this.betNum.splice(s, 1) : this.betNum.push(t), this.ball[e].selected = this.ball[e].selected ? 0 : 1, this.$emit("change", this.betNum)
								},
								choseAll: function() {
									for (var t in this.clear(), this.ball) - 1 == this.betNum.indexOf(this.ball[t].num) && (this.betNum.push(this.ball[t].num), this.ball[t].selected = 1);
									this.$emit("change", this.betNum)
								},
								choseBig: function() {
									for (var t in this.clear(), this.ball) t > 4 ? (this.betNum.push(this.ball[t].num), this.ball[t].selected = 1) : this.ball[t].selected = 0;
									this.$emit("change", this.betNum)
								},
								choseSmall: function() {
									for (var t in this.clear(), this.ball) t <= 4 ? (this.betNum.push(this.ball[t].num), this.ball[t].selected = 1) : this.ball[t].selected = 0;
									this.$emit("change", this.betNum)
								},
								choseJs: function() {
									for (var t in this.clear(), this.ball) t % 2 != 0 ? (this.betNum.push(this.ball[t].num), this.ball[t].selected = 1) : this.ball[t].selected = 0;
									this.$emit("change", this.betNum)
								},
								choseOs: function() {
									for (var t in this.clear(), this.ball) t % 2 == 0 ? (this.betNum.push(this.ball[t].num), this.ball[t].selected = 1) : this.ball[t].selected = 0;
									this.$emit("change", this.betNum)
								},
								clear: function() {
									for (var t in this.betNum = [], this.ball) this.ball[t].selected = 0;
									this.$emit("change", this.betNum)
								}
							}
						}, i, !1, function(t) {
							s("3PkU")
						}, "data-v-66cd19fe", null)
						.exports
				},
				props: ["type", "gain", "miss", "title", "text", "expect", "endTime"],
				data: function() {
					return {
						betNum1: [],
						betNum2: [],
						betNum3: [],
						betNum4: [],
						betNum5: [],
						betNum6: [],
						betNum7: []
					}
				},
				computed: {
					name: function() {
						return this.$route.path.replace("/", "")
					},
					lotteryUnit: function() {
						return this.$store.state.setting.lottery_unit
					},
					badge: function() {
						return this.$store.state.shuzicai.plan.length
					},
					notes: function() {
						var t = 0;
						return "pls" == this.name && (t = this.betNum1.length * this.betNum2.length * this.betNum3.length), "plw" == this.name && (t = this.betNum1.length * this.betNum2.length * this.betNum3.length * this.betNum4.length * this.betNum5.length), "qxc" == this.name && (t = this.betNum1.length * this.betNum2.length * this.betNum3.length * this.betNum4.length * this.betNum5.length * this.betNum6.length * this.betNum7.length), t
					},
					totalMoney: function() {
						return 2 * this.notes
					},
					contentH: function() {
						return this.$store.state.clientHeight - 152
					}
				},
				methods: {
					clear: function() {
						this.$set(this, "betNum1", []), this.$set(this, "betNum2", []), this.$set(this, "betNum3", []), this.$set(this, "betNum4", []), this.$set(this, "betNum5", []), this.$set(this, "betNum6", []), this.$set(this, "betNum7", [])
					},
					addBetBasket: function() {
						if (this.notes < 1) this.$toast("每位至少选择1个号码！");
						else {
							var t = this.type,
								e = this.text,
								s = void 0,
								i = void 0;
							s = (this.betNum1.length ? this.betNum1.sort()
								.join(",") + "|" : "") + (this.betNum2.length ? this.betNum2.sort()
								.join(",") + "|" : "") + (this.betNum3.length ? this.betNum3.sort()
								.join(",") + "|" : "") + (this.betNum4.length ? this.betNum4.sort()
								.join(",") + "|" : "") + (this.betNum5.length ? this.betNum5.sort()
								.join(",") + "|" : "") + (this.betNum6.length ? this.betNum6.sort()
								.join(",") + "|" : "") + (this.betNum7.length ? this.betNum7.sort()
								.join(",") : ""), i = "pls" == this.name || "plw" == this.name ? s.substr(0, s.length - 1) : s;
							var n = {};
							n.num = i, n.notes = this.notes, n.type_text = e, n.type = t, this.$store.commit("pushSzcBetNum", n), this.clear()
						}
					},
					submitOrder: function() {
						this.badge < 1 && this.notes < 1 ? this.$messagebox({
							title: "提示",
							message: "至少选择1注投注号码！",
							confirmButtonText: "我知道了"
						}) : (this.notes > 0 && this.addBetBasket(), this.$router.push({
							path: "/shuzicai/bet",
							query: {
								type: this.type,
								name: this.name,
								title: this.title,
								expect: this.expect,
								end_time: this.endTime
							}
						}))
					}
				}
			},
			a = {
				render: function() {
					var t = this,
						e = t.$createElement,
						s = t._self._c || e;
					return s("div", [s("div", {
						staticClass: "bet-ball contentH",
						style: {
							height: t.contentH + "px"
						}
					}, [s("div", {
						staticClass: "bet-tips f-mini c-3 bd-v"
					}, ["pls" == t.name ? s("em", [t._v("玩法提示：所选号码与开奖号码按位数全部相同即中"), s("em", {
						staticClass: "red"
					}, [t._v(t._s(t.gain))]), t._v(t._s(t.lotteryUnit) + "。")]) : t._e(), t._v(" "), "plw" == t.name ? s("em", [t._v("玩法提示：所选号码与开奖号码全部相同且顺序一致，即中"), s("em", {
						staticClass: "red"
					}, [t._v(t._s(t.gain))]), t._v(t._s(t.lotteryUnit) + "。")]) : t._e(), t._v(" "), "qxc" == t.name ? s("em", [t._v("玩法提示：所选号码与开奖号码全部相同且顺序一致，最高可中"), s("em", {
						staticClass: "red"
					}, [t._v(t._s(t.gain))]), t._v(t._s(t.lotteryUnit) + "。")]) : t._e()]), t._v(" "), s("bet-balls", {
						attrs: {
							"wz-text": "第一位",
							miss: t.miss
						},
						model: {
							value: t.betNum1,
							callback: function(e) {
								t.betNum1 = e
							},
							expression: "betNum1"
						}
					}), t._v(" "), s("bet-balls", {
						attrs: {
							"wz-text": "第二位",
							miss: t.miss
						},
						model: {
							value: t.betNum2,
							callback: function(e) {
								t.betNum2 = e
							},
							expression: "betNum2"
						}
					}), t._v(" "), s("bet-balls", {
						attrs: {
							"wz-text": "第三位",
							miss: t.miss
						},
						model: {
							value: t.betNum3,
							callback: function(e) {
								t.betNum3 = e
							},
							expression: "betNum3"
						}
					}), t._v(" "), "plw" == t.name || "qxc" == t.name ? [s("bet-balls", {
						attrs: {
							"wz-text": "第四位",
							miss: t.miss
						},
						model: {
							value: t.betNum4,
							callback: function(e) {
								t.betNum4 = e
							},
							expression: "betNum4"
						}
					}), t._v(" "), s("bet-balls", {
						attrs: {
							"wz-text": "第五位",
							miss: t.miss
						},
						model: {
							value: t.betNum5,
							callback: function(e) {
								t.betNum5 = e
							},
							expression: "betNum5"
						}
					})] : t._e(), t._v(" "), "qxc" == t.name ? [s("bet-balls", {
						attrs: {
							"wz-text": "第六位",
							miss: t.miss
						},
						model: {
							value: t.betNum6,
							callback: function(e) {
								t.betNum6 = e
							},
							expression: "betNum6"
						}
					}), t._v(" "), s("bet-balls", {
						attrs: {
							"wz-text": "第七位",
							miss: t.miss
						},
						model: {
							value: t.betNum7,
							callback: function(e) {
								t.betNum7 = e
							},
							expression: "betNum7"
						}
					})] : t._e()], 2), t._v(" "), s("div", {
						staticClass: "bet-foot"
					}, [s("div", {
						staticClass: "yl-box f-mini border-top-1px tc c-3"
					}, [t.notes ? [t._v("\n                已选"), s("em", {
						staticClass: "red"
					}, [t._v(t._s(t.notes))]), t._v("注，共"), s("em", {
						staticClass: "red"
					}, [t._v(t._s(2 * t.notes))]), t._v(t._s(t.lotteryUnit) + "\n            ")] : [t._v("\n                每位至少选择1个号码\n            ")]], 2), t._v(" "), s("div", {
						staticClass: "flex-box notes-box"
					}, [s("div", {
						staticClass: "flex bet-detail"
					}, [s("div", {
						staticClass: "c-4",
						on: {
							click: t.clear
						}
					}, [s("i", {
						staticClass: "iconfont icon-shanchu btn-icon-clear"
					}), t._v("清空\n                ")])]), t._v(" "), s("div", {
						staticClass: "tc bet-basket",
						staticStyle: {
							margin: "0 15px 0 5px"
						}
					}, [s("button", {
						staticClass: "bet-btn btn-basket",
						on: {
							click: t.addBetBasket
						}
					}, [t._v("+ 号码篮 "), s("mt-badge", {
						staticClass: "badge",
						attrs: {
							type: "error",
							size: "small"
						}
					}, [t._v(t._s(t.badge))])], 1)]), t._v(" "), s("button", {
						staticClass: "bet-btn btn-sure",
						on: {
							click: t.submitOrder
						}
					}, [t._v("选好了")])])])])
				},
				staticRenderFns: []
			};
		var o = s("VU/8")(n, a, !1, function(t) {
			s("Rgkj")
		}, "data-v-d30f88a4", null);
		e.a = o.exports
	},
	ZyZy: function(t, e, s) {
		"use strict";
		var i = {
				name: "orderJoinSet",
				components: {
					inputNumber: s("XYHu")
						.a
				},
				props: {
					visible: {
						type: Boolean,
						require: !0,
						default: !1
					},
					multiple: {
						default: 1
					},
					totalMoney: {
						default: 0
					},
					totalNotes: {
						default: 0
					}
				},
				model: {
					prop: "visible",
					event: "change"
				},
				data: function() {
					return {
						joinVisible: this.visible,
						gainVisible: !1,
						openVisible: !1,
						join: {
							total_share: this.totalMoney,
							buy_share: 0,
							bd_share: 0,
							infoTitle: "完全公开",
							infoState: 0,
							gain: "0",
							declaration: ""
						},
						slots: [{
							flex: 1,
							values: [{
								title: "完全公开",
								value: 0
							}, {
								title: "截止后公开",
								value: 1
							}, {
								title: "仅跟单人可见",
								value: 2
							}, {
								title: "完全保密",
								value: 3
							}],
							className: "slot1",
							textAlign: "center"
						}],
						slots1: [{
							flex: 1,
							values: ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10"],
							className: "slot1",
							textAlign: "center"
						}]
					}
				},
				watch: {
					joinVisible: function(t) {
						this.$emit("change", t)
					},
					totalMoney: function(t) {
						this.join.total_share = t
					},
					"join.total_share": function(t) {
						this.join.buy_share = Math.ceil(0.10 * t), this.join.bd_share + this.join.buy_share > t && (this.join.bd_share = this.bdMax)
					},
					"join.buy_share": function(t) {
						t + this.join.bd_share > this.join.total_share && (this.join.bd_share = this.bdMax)
					}
				},
				computed: {
					lotteryUnit: function() {
						return this.$store.state.setting.lottery_unit
					},
					money: function() {
						return this.$store.state.userinfo.money
					},
					isGain: function() {
						return this.$store.state.setting.isGain
					},
					joinContentH: function() {
						return this.$store.state.clientHeight - 92
					},
					maxShare: function() {
						return Number(this.totalMoney)
					},
					minTotalShare: function() {
						return this.totalMoney ? 1 : 0
					},
					perMoney: function() {
						return this.totalMoney ? this.$bet.accDiv(this.totalMoney, this.join.total_share, 3) : 0
					},
					minJoinMoney: function() {
						return Number(Math.ceil(.10 * this.join.total_share))
					},
					isbuy: function() {
						return this.totalMoney ? Math.floor(this.join.buy_share / this.join.total_share * 1e4) / 100 : 0
					},
					bdMax: function() {
						return this.join.total_share - this.join.buy_share
					},
					bdPercent: function() {
						return this.totalMoney ? Math.floor(this.join.bd_share / this.join.total_share * 1e4) / 100 : 0
					}
				},
				methods: {
					toJoin: function(t) {
						t ? (this.joinVisible = !0, this.join.total_share = this.totalMoney) : this.joinVisible = !1
					},
					onValuesChange: function(t, e) {
						this.join.infoState = e[0].value, this.join.infoTitle = e[0].title
					},
					onGainChange: function(t, e) {
						this.join.gain = e[0]
					},
					submitOrder: function() {
						this.$emit("submit-order", this.join)
					},
					countShareMoney: function(t) {
						return this.$bet.accMul(this.perMoney, t, 3)
					}
				}
			},
			n = {
				render: function() {
					var t = this,
						e = t.$createElement,
						s = t._self._c || e;
					return s("div", [s("mt-header", {
						attrs: {
							title: "合买详情"
						}
					}, [s("mt-button", {
						attrs: {
							slot: "left",
							icon: "back"
						},
						nativeOn: {
							click: function(e) {
								return t.toJoin(!1)
							}
						},
						slot: "left"
					}, [t._v("取消合买")])], 1), t._v(" "), s("div", {
						staticClass: "bet-join contentH",
						style: {
							height: t.joinContentH + "px"
						}
					}, [s("div", {
						staticClass: "info flex-box border-bottom-1px"
					}, [s("div", {
						staticClass: "label"
					}, [t._v("方案金额")]), t._v(" "), s("div", {
						staticClass: "cont"
					}, [s("em", {
						staticClass: "red"
					}, [t._v(t._s(t.totalMoney))]), t._v(" " + t._s(t.lotteryUnit) + "\n                "), s("span", {
						staticClass: "join-tip"
					}, [t._v("\n                            " + t._s(t.totalNotes) + "注 "), s("em", [t._v(t._s(t.multiple) + "倍")])])])]), t._v(" "), s("div", {
						staticClass: "info flex-box border-bottom-1px"
					}, [s("div", {
						staticClass: "label"
					}, [t._v("我要分成")]), t._v(" "), s("div", {
						staticClass: "cont"
					}, [s("input-number", {
						attrs: {
							min: t.minTotalShare,
							max: t.maxShare,
							size: "small"
						},
						model: {
							value: t.join.total_share,
							callback: function(e) {
								t.$set(t.join, "total_share", e)
							},
							expression: "join.total_share"
						}
					}), t._v(" 份\n                "), s("span", {
						staticClass: "join-tip"
					}, [t._v("每份" + t._s(t.perMoney) + t._s(t.lotteryUnit))]), t._v(" "), s("span", {
						staticStyle: {
							"font-size": "10px"
						}
					}, [t._v("(每份至少1" + t._s(t.lotteryUnit) + ")")])], 1)]), t._v(" "), s("div", {
						staticClass: "info flex-box border-bottom-1px"
					}, [s("div", {
						staticClass: "label"
					}, [t._v("我要认购")]), t._v(" "), s("div", {
						staticClass: "cont"
					}, [s("input-number", {
						attrs: {
							min: t.minJoinMoney,
							max: t.join.total_share,
							size: "small"
						},
						model: {
							value: t.join.buy_share,
							callback: function(e) {
								t.$set(t.join, "buy_share", e)
							},
							expression: "join.buy_share"
						}
					}), t._v(" 份\n                "), t._m(0), t._v(" "), s("span", {
						staticStyle: {
							"font-size": "10px"
						}
					}, [t._v("(已认购"), s("em", {
						staticClass: "red"
					}, [t._v(t._s(t.isbuy))]), t._v("%)")])], 1)]), t._v(" "), s("div", {
						staticClass: "info flex-box border-bottom-1px"
					}, [s("div", {
						staticClass: "label"
					}, [t._v("我要保底")]), t._v(" "), s("div", {
						staticClass: "cont"
					}, [s("input-number", {
						attrs: {
							min: 0,
							max: t.bdMax,
							size: "small"
						},
						model: {
							value: t.join.bd_share,
							callback: function(e) {
								t.$set(t.join, "bd_share", e)
							},
							expression: "join.bd_share"
						}
					}), t._v(" 份\n                "), s("span", {
						staticClass: "join-tip"
					}, [t._v("最多可保底"), s("em", {
						staticClass: "red"
					}, [t._v(t._s(t.bdMax))]), t._v("份")]), t._v(" "), s("span", {
						staticStyle: {
							"font-size": "10px"
						}
					}, [t._v("(已保底"), s("em", {
						staticClass: "red"
					}, [t._v(t._s(t.bdPercent))]), t._v("%)")])], 1)]), t._v(" "), t.isGain ? s("div", {
						staticClass: "info flex-box border-bottom-1px"
					}, [s("div", {
						staticClass: "label"
					}, [t._v("盈利佣金")]), t._v(" "), s("div", {
						staticClass: "cont"
					}, [s("span", {
						staticClass: "select-type border-1px",
						on: {
							click: function(e) {
								t.gainVisible = !t.gainVisible
							}
						}
					}, [t._v(t._s(t.join.gain) + "% "), s("i", {
						staticClass: "iconfont icon-jiantou c-4 f-mini"
					})]), t._v(" "), s("span", {
						staticClass: "join-tip"
					}, [t._v("盈利佣金=奖金*佣金比例")])])]) : t._e(), t._v(" "), s("div", {
						staticClass: "info flex-box border-bottom-1px"
					}, [s("div", {
						staticClass: "label"
					}, [t._v("保密设置")]), t._v(" "), s("div", {
						staticClass: "cont"
					}, [s("span", {
						staticClass: "select-type border-1px",
						on: {
							click: function(e) {
								t.openVisible = !t.openVisible
							}
						}
					}, [t._v(t._s(t.join.infoTitle) + " "), s("i", {
						staticClass: "iconfont icon-jiantou c-4 f-mini"
					})])])]), t._v(" "), s("div", {
						staticClass: "info flex-box border-bottom-1px"
					}, [s("div", {
						staticClass: "label"
					}, [t._v("合买宣言")]), t._v(" "), s("div", {
						staticClass: "cont"
					}, [s("input", {
						directives: [{
							name: "model",
							rawName: "v-model",
							value: t.join.declaration,
							expression: "join.declaration"
						}],
						staticClass: "input",
						attrs: {
							type: "text",
							placeholder: "说点什么吧!"
						},
						domProps: {
							value: t.join.declaration
						},
						on: {
							input: function(e) {
								e.target.composing || t.$set(t.join, "declaration", e.target.value)
							}
						}
					})])])]), t._v(" "), s("div", {
						staticClass: "bet-foot"
					}, [s("div", {
						staticClass: "flex-box notes-box"
					}, [s("div", {
						staticClass: "flex tl bet-basket"
					}, [s("p", [t._v("共 " + t._s(t.countShareMoney(t.join.buy_share + t.join.bd_share)) + " " + t._s(t.lotteryUnit))]), t._v(" "), s("p", {
						staticClass: "f-mini c-4"
					}, [t._v("认购" + t._s(t.countShareMoney(t.join.buy_share)) + t._s(t.lotteryUnit) + "+保底" + t._s(t.countShareMoney(t.join.bd_share)) + t._s(t.lotteryUnit))])]), t._v(" "), s("button", {
						staticClass: "bet-btn btn-sure",
						on: {
							click: t.submitOrder
						}
					}, [t._v("确认合买")])])]), t._v(" "), s("mt-popup", {
						attrs: {
							position: "bottom"
						},
						model: {
							value: t.gainVisible,
							callback: function(e) {
								t.gainVisible = e
							},
							expression: "gainVisible"
						}
					}, [s("mt-picker", {
						attrs: {
							slots: t.slots1
						},
						on: {
							change: t.onGainChange
						}
					})], 1), t._v(" "), s("mt-popup", {
						attrs: {
							position: "bottom"
						},
						model: {
							value: t.openVisible,
							callback: function(e) {
								t.openVisible = e
							},
							expression: "openVisible"
						}
					}, [s("mt-picker", {
						attrs: {
							slots: t.slots,
							valueKey: "title"
						},
						on: {
							change: t.onValuesChange
						}
					})], 1)], 1)
				},
				staticRenderFns: [function() {
					var t = this.$createElement,
						e = this._self._c || t;
					return e("span", {
						staticClass: "join-tip"
					}, [this._v("至少认购"), e("b", {
						staticClass: "red"
					}, [this._v("10")]), this._v("%")])
				}]
			};
		var a = s("VU/8")(i, n, !1, function(t) {
			s("pkw4")
		}, "data-v-008d5263", null);
		e.a = a.exports
	},
	dl95: function(t, e, s) {
		"use strict";
		var i = {
			render: function() {
				var t = this,
					e = t.$createElement,
					s = t._self._c || e;
				return s("div", [s("mt-button", {
					nativeOn: {
						click: function(e) {
							t.sheetVisible = !0
						}
					}
				}, [t._v(t._s(t.showText) + " "), s("i", {
					staticClass: "iconfont icon-xialajiantou"
				})]), t._v(" "), s("mt-actionsheet", {
					attrs: {
						actions: t.actions
					},
					model: {
						value: t.sheetVisible,
						callback: function(e) {
							t.sheetVisible = e
						},
						expression: "sheetVisible"
					}
				})], 1)
			},
			staticRenderFns: []
		};
		var n = s("VU/8")({
			name: "dateActions",
			data: function() {
				return {
					sheetVisible: !1,
					actions: [{
						name: "今天",
						method: this.chose1
					}, {
						name: "昨天",
						method: this.chose2
					}, {
						name: "本月",
						method: this.chose3
					}, {
						name: "上月",
						method: this.chose4
					}],
					time: "1"
				}
			},
			computed: {
				showText: function() {
					return {
						1: "今天",
						2: "昨天",
						3: "本月",
						4: "上月"
					} [this.time]
				}
			},
			watch: {
				time: function(t) {
					this.$emit("change-date", t)
				}
			},
			methods: {
				chose1: function() {
					this.time = 1
				},
				chose2: function() {
					this.time = 2
				},
				chose3: function() {
					this.time = 3
				},
				chose4: function() {
					this.time = 4
				}
			}
		}, i, !1, function(t) {
			s("NI25")
		}, "data-v-2a088b5e", null);
		e.a = n.exports
	},
	fph9: function(t, e, s) {
		"use strict";
		var i = {
				name: "baseModal",
				props: {
					show: {
						type: Boolean,
						default: !1
					}
				},
				data: function() {
					return {
						visible: this.show
					}
				},
				model: {
					prop: "show",
					event: "change"
				},
				watch: {
					show: function(t) {
						this.visible = t, this.$refs.wrapper.scrollTop = 0
					}
				},
				methods: {
					closeMyself: function() {
						this.visible = !1, this.$emit("change")
					},
					hanleCancel: function() {
						this.visible = !1, this.$emit("change"), this.$emit("hanle-cancel")
					},
					hanleSure: function() {
						this.$emit("hanle-sure")
					}
				}
			},
			n = {
				render: function() {
					var t = this,
						e = t.$createElement,
						s = t._self._c || e;
					return s("div", {
						directives: [{
							name: "show",
							rawName: "v-show",
							value: t.visible,
							expression: "visible"
						}],
						staticClass: "jc-modal-layout"
					}, [s("transition", {
						attrs: {
							name: "modal-zoom"
						}
					}, [s("div", {
						directives: [{
							name: "show",
							rawName: "v-show",
							value: t.visible,
							expression: "visible"
						}],
						staticClass: "v-modal-p"
					}, [s("div", {
						staticClass: "v-modal-cont"
					}, [s("div", {
						staticClass: "modal-head"
					}, [t._t("header", [t._v("提示信息")])], 2), t._v(" "), s("div", {
						ref: "wrapper",
						staticClass: "modal-cont flex"
					}, [t._t("main", [t._v("弹窗内容")])], 2), t._v(" "), s("div", {
						staticClass: "modal-foot"
					}, [s("div", {
						staticClass: "flex-box"
					}, [s("div", {
						staticClass: "modal-btn btn-cancle flex",
						on: {
							click: t.hanleCancel
						}
					}, [t._v("取消")]), t._v(" "), s("div", {
						staticClass: "modal-btn btn-sure flex",
						on: {
							click: t.hanleSure
						}
					}, [t._v("确定")])])])])])])], 1)
				},
				staticRenderFns: []
			};
		var a = s("VU/8")(i, n, !1, function(t) {
			s("GXDb")
		}, "data-v-3d415054", null);
		e.a = a.exports
	},
	fviT: function(t, e, s) {
		"use strict";
		var i = {
				name: "telSafeCheck",
				props: {
					url: {
						type: String,
						require: !0
					},
					options: {
						type: Object,
						require: !0
					},
					getWay: {
						type: Number,
						default: 1
					}
				},
				model: {
					prop: "options",
					event: "change"
				},
				data: function() {
					return {
						labelWord: 1 == this.getWay ? "手机号" : "邮箱",
						loading: !1,
						text: "获取验证码",
						countDown: 120,
						yzmState: !1
					}
				},
				computed: {
					isYzm: function() {
						return !!Number(this.$store.state.setting.tel_checked)
					},
					isEmail: function() {
						return !!Number(this.$store.state.setting.email_checked)
					},
					isShow: function() {
						return 1 == this.getWay ? this.isYzm : this.isEmail
					}
				},
				watch: {
					"options.num": function(t) {
						t && (this.yzmState = !0)
					},
					options: function(t) {
						this.$emit("change", t)
					}
				},
				methods: {
					sendSms: function() {
						var t = this,
							e = void 0;
						if (120 != this.countDown) return !1;
						if (1 == this.getWay) {
							if (!this.$base.testTel(this.options.num)) return this.$toast({
								message: "请输入正确的手机号",
								duration: 1500
							}), !1;
							e = {
								tel: this.options.num
							}
						}
						if (2 == this.getWay) {
							if (!this.$base.testEmail(this.options.num)) return this.$toast({
								message: "请输入正确的邮箱地址",
								duration: 1500
							}), !1;
							e = {
								email: this.options.num
							}
						}
						this.yzmState = !1, this.text = "发送中...", this.$axios.get(this.url, {
								params: e
							})
							.then(function(e) {
								var s = e.data;
								if (s.err) t.$toast({
									message: s.msg,
									duration: 1500
								}), t.text = "获取验证码", t.yzmState = !0;
								else {
									t.text = "重新发送(" + t.countDown + "s)";
									var i = setInterval(function() {
										if (t.countDown = t.countDown - 1, 0 == t.countDown) return t.text = "获取验证码", t.yzmState = !0, t.countDown = 120, void clearInterval(i);
										t.text = "重新发送(" + t.countDown + "s)"
									}, 1e3);
									t.$toast({
										message: "发送成功",
										duration: 1500
									})
								}
							})
							.catch(function(t) {
								console.log(t)
							})
					}
				}
			},
			n = {
				render: function() {
					var t = this,
						e = t.$createElement,
						s = t._self._c || e;
					return s("div", [s("mt-field", {
						attrs: {
							label: t.labelWord,
							placeholder: "请输入" + t.labelWord,
							type: 1 == t.getWay ? "tel" : "text"
						},
						model: {
							value: t.options.num,
							callback: function(e) {
								t.$set(t.options, "num", e)
							},
							expression: "options.num"
						}
					}), t._v(" "), t.isShow ? s("mt-field", {
						attrs: {
							label: "验证码",
							placeholder: "请输入验证码",
							type: "tel"
						},
						model: {
							value: t.options.yzm,
							callback: function(e) {
								t.$set(t.options, "yzm", e)
							},
							expression: "options.yzm"
						}
					}, [s("div", {
						staticClass: "btn-yzm",
						class: t.yzmState ? "abled" : "disabled",
						on: {
							click: t.sendSms
						}
					}, [t._v(t._s(t.text))])]) : t._e()], 1)
				},
				staticRenderFns: []
			};
		var a = s("VU/8")(i, n, !1, function(t) {
			s("Lpxy")
		}, "data-v-93bc2aca", null);
		e.a = a.exports
	},
	hZWC: function(t, e, s) {
		"use strict";
		var i = s("woOf"),
			n = s.n(i),
			a = {
				name: "CommonLoopProgress",
				props: {
					bdProgress: {
						required: !0
					},
					progress: {
						required: !0
					},
					progressOption: {
						type: Object,
						default: function() {}
					}
				},
				data: function() {
					return {}
				},
				computed: {
					progressHandle: function() {
						var t = Math.floor(2 * Math.PI * this.option.radius);
						return this.progress * t / 100 + 1 + ",1000000"
					},
					option: function() {
						var t = {
							radius: 40,
							strokeWidth: 5,
							outerColor: "#E6E6E6",
							innerColor: "#e50e03"
						};
						return n()(t, this.progressOption), t.cy = t.cx = t.radius + t.strokeWidth, t.size = 2 * (t.radius + t.strokeWidth), t
					}
				}
			},
			o = {
				render: function() {
					var t = this,
						e = t.$createElement,
						s = t._self._c || e;
					return s("div", {
						staticClass: "progress-circle"
					}, [s("div", [s("svg", {
						attrs: {
							height: t.option.size,
							width: t.option.size,
							"x-mlns": "http://www.w3.org/200/svg"
						}
					}, [s("circle", {
						attrs: {
							r: t.option.radius,
							cx: t.option.cx,
							cy: t.option.cy,
							stroke: t.option.outerColor,
							"stroke-width": t.option.strokeWidth,
							fill: "none",
							"stroke-linecap": "round"
						}
					}), t._v(" "), s("circle", {
						staticClass: "progressRound",
						attrs: {
							id: "progressRound",
							"stroke-dasharray": t.progressHandle,
							r: t.option.radius,
							cx: t.option.cx,
							cy: t.option.cy,
							"stroke-width": t.option.strokeWidth,
							stroke: t.option.innerColor,
							fill: "none"
						}
					}), t._v(" "), s("text", {
						attrs: {
							x: t.option.cx + 2,
							y: t.option.cy + 3,
							"text-anchor": "middle",
							"font-size": "1.0rem",
							fill: "red"
						}
					}, [t._v("\n                " + t._s(t.progress) + "%\n            ")]), t._v(" "), s("text", {
						staticClass: "f-mini",
						attrs: {
							x: t.option.cx + 2,
							y: t.option.cy + 18,
							"text-anchor": "middle",
							"font-size": "0.7rem",
							fill: "orange"
						}
					}, [t._v("\n                保" + t._s(t.bdProgress) + "%\n            ")])])])])
				},
				staticRenderFns: []
			};
		var r = s("VU/8")(a, o, !1, function(t) {
			s("rOIT")
		}, "data-v-e65f76ba", null);
		e.a = r.exports
	},
	iQLX: function(t, e, s) {
		"use strict";
		var i = {
				name: "betAgreeChecked",
				props: {
					checked: {
						type: Boolean,
						require: !0,
						default: !0
					}
				},
				model: {
					prop: "checked",
					event: "change"
				},
				data: function() {
					return {
						check: this.checked,
						agreeVisible: !1,
						gameVisible: !1
					}
				},
				watch: {
					check: function(t) {
						this.$emit("change", t)
					}
				},
				computed: {
					user_web: function() {
						return this.$store.state.setting.user_service
					},
					web_service: function() {
						return this.$store.state.setting.web_service
					}
				}
			},
			n = {
				render: function() {
					var t = this,
						e = t.$createElement,
						s = t._self._c || e;
					return s("div", [s("div", {
						staticClass: "tc flex-box",
						staticStyle: {
							"justify-content": "center",
							"font-size": "13px",
							height: "30px"
						}
					}, [s("label", {
						staticClass: "mint-radiolist-label",
						staticStyle: {
							padding: "0px"
						}
					}, [s("span", {
						staticClass: "mint-radio"
					}, [s("input", {
						directives: [{
							name: "model",
							rawName: "v-model",
							value: t.check,
							expression: "check"
						}],
						staticClass: "mint-radio-input",
						attrs: {
							type: "checkbox"
						},
						domProps: {
							checked: Array.isArray(t.check) ? t._i(t.check, null) > -1 : t.check
						},
						on: {
							change: function(e) {
								var s = t.check,
									i = e.target,
									n = !!i.checked;
								if (Array.isArray(s)) {
									var a = t._i(s, null);
									i.checked ? a < 0 && (t.check = s.concat([null])) : a > -1 && (t.check = s.slice(0, a)
										.concat(s.slice(a + 1)))
								} else t.check = n
							}
						}
					}), t._v(" "), s("span", {
						staticClass: "mint-radio-core"
					})]), t._v(" "), s("span", {
						staticClass: "mint-radio-label",
						staticStyle: {
							"margin-left": "0"
						}
					}, [t._v("我已阅读并同意")])]), t._v(" "), s("em", {
						staticClass: "org",
						on: {
							click: function(e) {
								t.agreeVisible = !0
							}
						}
					}, [t._v("《用户服务协议》")]), s("em", {
						staticClass: "org",
						on: {
							click: function(e) {
								t.gameVisible = !0
							}
						}
					}, [t._v("《游戏服务协议》")])]), t._v(" "), s("mt-popup", {
						staticClass: "agree-poput",
						attrs: {
							position: "right"
						},
						model: {
							value: t.agreeVisible,
							callback: function(e) {
								t.agreeVisible = e
							},
							expression: "agreeVisible"
						}
					}, [s("div", {
						staticClass: "visible-cont"
					}, [s("mt-header", {
						attrs: {
							title: "用户服务协议"
						}
					}, [s("mt-button", {
						attrs: {
							slot: "left",
							icon: "back"
						},
						nativeOn: {
							click: function(e) {
								t.agreeVisible = !1
							}
						},
						slot: "left"
					}, [t._v("关闭")])], 1), t._v(" "), s("div", {
						staticClass: "service-cont",
						domProps: {
							innerHTML: t._s(t.user_web)
						}
					})], 1)]), t._v(" "), s("mt-popup", {
						staticClass: "agree-poput",
						attrs: {
							position: "right"
						},
						model: {
							value: t.gameVisible,
							callback: function(e) {
								t.gameVisible = e
							},
							expression: "gameVisible"
						}
					}, [s("div", {
						staticClass: "visible-cont"
					}, [s("mt-header", {
						attrs: {
							title: "游戏服务协议"
						}
					}, [s("mt-button", {
						attrs: {
							slot: "left",
							icon: "back"
						},
						nativeOn: {
							click: function(e) {
								t.gameVisible = !1
							}
						},
						slot: "left"
					}, [t._v("关闭")])], 1), t._v(" "), s("div", {
						staticClass: "service-cont",
						domProps: {
							innerHTML: t._s(t.web_service)
						}
					})], 1)])], 1)
				},
				staticRenderFns: []
			},
			a = s("VU/8")(i, n, !1, null, null, null);
		e.a = a.exports
	},
	juHj: function(t, e, s) {
		"use strict";
		var i = {
				name: "",
				data: function() {
					return {
						shoseTime: !1,
						time: {
							startBefore: "",
							start: "",
							endBefore: "",
							end: ""
						}
					}
				},
				computed: {
					contentH: function() {
						return this.shoseTime ? "100%" : "auto"
					},
					startData: function() {
						return new Date("January 1,2018")
					},
					endData: function() {
						return new Date
					}
				},
				watch: {
					timeValue: function(t) {
						this.$emit("change", t)
					}
				},
				methods: {
					openSpicker: function() {
						this.$refs.picker.open()
					},
					openEpicker: function() {
						"" == this.time.endBefore && (this.time.endBefore = this.time.startBefore), this.$refs.pickerEnd.open()
					},
					handleConfirm: function() {
						"" == this.time.startBefore && (this.time.startBefore = this.startData), this.time.start = this.$bet.formatTime("Y-m-d", this.time.startBefore / 1e3)
					},
					handleConfirmEnd: function() {
						"" == this.time.endBefore && (this.time.endBefore = this.time.startBefore), this.time.end = this.$bet.formatTime("Y-m-d", this.time.endBefore / 1e3)
					},
					doSearch: function() {
						if (this.time.startBefore)
							if (this.time.endBefore) {
								if (this.time.startBefore > this.time.endBefore) return this.$messagebox("提示", "开始日期不能大于结束日期"), this.time.endBefore = this.time.startBefore, void(this.time.end = this.time.start);
								var t = "starttime=" + this.time.start + "&endtime=" + this.time.end;
								this.shoseTime = !1, this.$emit("do-search", t)
							} else this.$messagebox("提示", "请选择结束日期");
						else this.$messagebox("提示", "请选择开始日期")
					}
				}
			},
			n = {
				render: function() {
					var t = this,
						e = t.$createElement,
						s = t._self._c || e;
					return s("div", [s("div", {
						staticClass: "date-query-fixed",
						style: {
							height: t.contentH
						}
					}, [s("div", {
						staticClass: "mf-sm card border-bottom-1px",
						staticStyle: {
							padding: "10px"
						}
					}, [s("div", {
						staticClass: "flex-box"
					}, [s("div", {
						staticClass: "flex tl"
					}, [t._t("info")], 2), t._v(" "), t.time.start && t.time.end && !t.shoseTime ? s("em", {
						staticClass: "f-mini c-3",
						on: {
							click: function(e) {
								t.shoseTime = !t.shoseTime
							}
						}
					}, [t._v(t._s(t.time.start) + "至" + t._s(t.time.end))]) : t._e(), t._v(" "), s("div", {
						staticStyle: {
							"margin-left": "5px"
						}
					}, [s("i", {
						staticClass: "iconfont icon-rili f-large c-3",
						attrs: {
							slot: "icon"
						},
						on: {
							click: function(e) {
								t.shoseTime = !t.shoseTime
							}
						},
						slot: "icon"
					})])]), t._v(" "), t.shoseTime ? s("div", [s("div", {
						staticClass: "chose-time flex-box border-top-1px"
					}, [t._v("\n                    选择日期\n                    "), s("span", {
						staticClass: "time-value",
						on: {
							click: t.openSpicker
						}
					}, [t._v(t._s(t.time.start))]), t._v("至\n                    "), s("span", {
						staticClass: "time-value",
						on: {
							click: t.openEpicker
						}
					}, [t._v(t._s(t.time.end))]), t._v(" "), s("mt-button", {
						staticClass: "btn-search",
						attrs: {
							size: "small"
						},
						nativeOn: {
							click: function(e) {
								return t.doSearch(e)
							}
						}
					}, [t._v("查询")])], 1)]) : t._e(), t._v(" "), s("div", [s("mt-datetime-picker", {
						ref: "picker",
						attrs: {
							type: "date",
							value: t.startData,
							"year-format": "{value} 年",
							"month-format": "{value} 月",
							"date-format": "{value} 日",
							"start-date": t.startData,
							"end-date": t.endData
						},
						on: {
							confirm: t.handleConfirm
						},
						model: {
							value: t.time.startBefore,
							callback: function(e) {
								t.$set(t.time, "startBefore", e)
							},
							expression: "time.startBefore"
						}
					}), t._v(" "), s("mt-datetime-picker", {
						ref: "pickerEnd",
						attrs: {
							type: "date",
							value: t.startData,
							"year-format": "{value} 年",
							"month-format": "{value} 月",
							"date-format": "{value} 日",
							"start-date": t.startData,
							"end-date": t.endData
						},
						on: {
							confirm: t.handleConfirmEnd
						},
						model: {
							value: t.time.endBefore,
							callback: function(e) {
								t.$set(t.time, "endBefore", e)
							},
							expression: "time.endBefore"
						}
					})], 1)])]), t._v(" "), s("div", {
						staticStyle: {
							height: "46px"
						}
					})])
				},
				staticRenderFns: []
			};
		var a = s("VU/8")(i, n, !1, function(t) {
			s("XaOq")
		}, "data-v-7859e16d", null);
		e.a = a.exports
	},
	ketd: function(t, e) {},
	oNuw: function(t, e) {},
	obK2: function(t, e, s) {
		"use strict";
		var i = {
			render: function() {
				var t = this,
					e = t.$createElement,
					s = t._self._c || e;
				return s("div", {
					staticClass: "copy-box"
				}, [s("div", {
					staticClass: "contentH",
					style: {
						height: t.contentH + "px"
					}
				}, [s("div", {
					staticClass: "bet-tips f-mini c-3 bd-v"
				}, [t._v("前区从0-9中手动输入" + t._s(t.n) + "个号码组成一注号码进行购买，奖金"), s("em", {
					staticClass: "red"
				}, [t._v(t._s(t.gain))]), t._v(t._s(t.lotteryUnit) + "。")]), t._v(" "), s("div", {
					staticClass: "copydiv"
				}, [s("div", {
					staticClass: "pasterIntro mf-sm"
				}, [s("strong", [t._v("格式说明：")]), t._v(" "), s("p", [t._v('\n                    (1)每位之间以英文字符逗号(",")或者空格分割；'), s("br"), t._v("\n                    (2)每行填写一注投注号码；"), s("br"), t._v("\n                    单注示例：\n                    "), "pls" == t.name ? [1.2 == t.type || 3.2 == t.type ? [t._v("1,2,3或1 2 3")] : t._e(), t._v(" "), 2.2 == t.type ? [t._v("1,2或1 2")] : t._e()] : t._e(), t._v(" "), "plw" == t.name ? [t._v("\n                        1,2,3,4,5或1 2 3 4 5\n                    ")] : t._e(), t._v(" "), "qxc" == t.name ? [t._v("\n                        1,2,3,4,5,6,7或1 2 3 4 5 6 7\n                    ")] : t._e()], 2)]), t._v(" "), s("mt-field", {
					attrs: {
						placeholder: "请按照格式说明输入或粘贴投注号码，最多输入500注。",
						type: "textarea",
						rows: "6"
					},
					model: {
						value: t.copyValue,
						callback: function(e) {
							t.copyValue = e
						},
						expression: "copyValue"
					}
				}), t._v(" "), t.errCont.length > 0 && t.show ? s("div", {
					staticClass: "copy_err red f-sm mt-sm"
				}, [s("div", [t._v("共有" + t._s(t.errCont.length) + "行错误！")]), t._v(" "), s("div", t._l(t.errCont, function(e, i) {
					return s("p", [s("span", [t._v("第" + t._s(e.row) + "行")]), t._v(" "), s("span", [t._v(t._s(e.num))])])
				}), 0)]) : t._e()], 1)]), t._v(" "), s("div", {
					staticClass: "bet-foot"
				}, [s("div", {
					staticClass: "yl-box f-mini border-top-1px tc c-3"
				}, [s("em", {
					staticClass: "red"
				}, [t._v(t._s(t.notes))]), t._v("注，共"), s("em", {
					staticClass: "red"
				}, [t._v(t._s(2 * t.notes))]), t._v(t._s(t.lotteryUnit) + ",最多500注\n        ")]), t._v(" "), s("div", {
					staticClass: "flex-box notes-box"
				}, [s("div", {
					staticClass: "flex bet-detail"
				}, [s("div", {
					staticClass: "c-4",
					on: {
						click: t.clear
					}
				}, [s("i", {
					staticClass: "iconfont icon-shanchu btn-icon-clear"
				}), t._v("清空\n                ")])]), t._v(" "), s("div", {
					staticClass: "tc bet-basket",
					staticStyle: {
						margin: "0 15px 0 5px"
					}
				}, [s("button", {
					staticClass: "bet-btn btn-basket",
					on: {
						click: t.addBetBasket
					}
				}, [t._v("+ 号码篮 "), s("mt-badge", {
					staticClass: "badge",
					attrs: {
						type: "error",
						size: "small"
					}
				}, [t._v(t._s(t.badge))])], 1)]), t._v(" "), s("button", {
					staticClass: "bet-btn btn-sure",
					on: {
						click: t.submitOrder
					}
				}, [t._v("选好了")])])])])
			},
			staticRenderFns: []
		};
		var n = s("VU/8")({
			name: "PlsCopyBet",
			props: ["type", "gain", "title", "text", "expect", "endTime", "n"],
			data: function() {
				return {
					copyValue: "",
					errCont: [],
					resArr: [],
					notes: 0,
					show: !1
				}
			},
			watch: {
				copyValue: function() {
					this.show = !1, this.check()
				}
			},
			computed: {
				name: function() {
					return this.$route.path.replace("/", "")
				},
				lotteryUnit: function() {
					return this.$store.state.setting.lottery_unit
				},
				contentH: function() {
					return this.$store.state.clientHeight - 152
				},
				badge: function() {
					return this.$store.state.shuzicai.plan.length
				}
			},
			methods: {
				check: function() {
					this.$set(this, "errCont", []), this.$set(this, "resArr", []), this.notes = 0;
					for (var t, e = this.copyValue.replace(/\s+$/g, "")
						.split(/[(\r\n)\r\n]+/) || [], s = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"], i = 0; i < e.length; i++) {
						for (var n = !1, a = !1, o = e[i].replace(/\s/g, ",")
							.split(",") || [], r = 0; r < o.length; r++)("pls" == this.name && 2.2 == this.type || "pls" == this.name && 3.2 == this.type) && o[r] == o[r + 1] && (n = !0), this.$base.isExit(s, o[r]) || (a = !0);
						a || n || o.length !== this.n ? (t = {
							row: i + 1,
							num: e[i]
						}, this.errCont.push(t)) : (this.resArr.push(e[i]), this.notes += 1)
					}
				},
				clear: function() {
					this.$set(this, "errCont", []), this.$set(this, "resArr", []), this.$set(this, "copyValue", "")
				},
				addNum: function(t) {
					t = "pls" == this.name && 2.2 == this.type || "pls" == this.name && 3.2 == this.type ? t.replace(/ /g, ",") : t.replace(/ |,/g, function(t) {
						return {
							" ": "|",
							",": "|"
						} [t]
					});
					var e = {};
					e.num = t, e.notes = 2.2 == this.type ? 2 : 1, e.type_text = this.text, e.type = this.type, this.$store.commit("pushSzcBetNum", e)
				},
				addBetBasket: function() {
					var t = this;
					if (!this.copyValue.length) return this.$messagebox("提示", "请先复制投注号码！"), !1;
					if (this.notes > 500) return this.$messagebox("提示", "最多只能输入500注！"), !1;
					if (this.check(), this.show = !0, this.errCont.length > 0) this.$messagebox.confirm("所输入号码中有 <b class='red'>" + this.errCont.length + "</b> 行错误，过滤错误号码继续添加？")
						.then(function() {
							for (var e in t.resArr) t.addNum(t.resArr[e]);
							t.clear()
						})
						.catch(function() {});
					else {
						for (var e in this.resArr) this.addNum(this.resArr[e]);
						this.clear()
					}
				},
				submitOrder: function() {
					this.badge < 1 && this.notes < 1 ? this.$messagebox({
						title: "提示",
						message: "至少选择1注投注号码！",
						confirmButtonText: "我知道了"
					}) : (this.notes > 0 && this.addBetBasket(), this.$router.push({
						path: "/shuzicai/bet",
						query: {
							type: this.type,
							name: this.name,
							title: this.title,
							expect: this.expect,
							end_time: this.endTime
						}
					}))
				}
			}
		}, i, !1, function(t) {
			s("DDNP")
		}, "data-v-3b470cdf", null);
		e.a = n.exports
	},
	pkw4: function(t, e) {},
	rOIT: function(t, e) {},
	woOf: function(t, e, s) {
		t.exports = {
			default: s("V3tA"),
			__esModule: !0
		}
	},
	wv44: function(t, e, s) {
		"use strict";
		var i = {
			render: function() {
				var t = this.$createElement,
					e = this._self._c || t;
				return this.isStop ? e("div", {
					staticClass: "stopSale"
				}, [e("div", [e("svg", {
					staticClass: "icon",
					attrs: {
						slot: "icon",
						"aria-hidden": "true"
					},
					slot: "icon"
				}, [e("use", {
					attrs: {
						"xlink:href": "#icon-shuizhuo"
					}
				})])]), this._v(" "), e("p", [this._v("对不起，该彩种暂停销售")])]) : this._e()
			},
			staticRenderFns: []
		};
		var n = s("VU/8")({
			name: "unitConvert",
			props: {
				isStop: {
					default: !1
				}
			},
			data: function() {
				return {}
			}
		}, i, !1, function(t) {
			s("/xp6")
		}, "data-v-9564ecba", null);
		e.a = n.exports
	}
});
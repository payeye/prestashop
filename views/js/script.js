var app = (function (e) {
    "use strict";
    function t() { }
    const n = (e) => e;
    function r(e) {
        return e();
    }
    function i() {
        return Object.create(null);
    }
    function o(e) {
        e.forEach(r);
    }
    function a(e) {
        return "function" == typeof e;
    }
    function s(e, t) {
        return e != e ? t == t : e !== t || (e && "object" == typeof e) || "function" == typeof e;
    }
    let c;
    function l(e, t) {
        return c || (c = document.createElement("a")), (c.href = t), e === c.href;
    }
    function d(e, n, r) {
        e.$$.on_destroy.push(
            (function (e, ...n) {
                if (null == e) return t;
                const r = e.subscribe(...n);
                return r.unsubscribe ? () => r.unsubscribe() : r;
            })(n, r)
        );
    }
    const p = "undefined" != typeof window;
    let u = p ? () => window.performance.now() : () => Date.now(),
        f = p ? (e) => requestAnimationFrame(e) : t;
    const m = new Set();
    function h(e) {
        m.forEach((t) => {
            t.c(e) || (m.delete(t), t.f());
        }),
            0 !== m.size && f(h);
    }
    function g(e) {
        let t;
        return (
            0 === m.size && f(h),
            {
                promise: new Promise((n) => {
                    m.add((t = { c: e, f: n }));
                }),
                abort() {
                    m.delete(t);
                },
            }
        );
    }
    function C(e, t) {
        e.appendChild(t);
    }
    function w(e, t, n) {
        const r = v(e);
        if (!r.getElementById(t)) {
            const e = j("style");
            (e.id = t), (e.textContent = n), y(r, e);
        }
    }
    function v(e) {
        if (!e) return document;
        const t = e.getRootNode ? e.getRootNode() : e.ownerDocument;
        return t && t.host ? t : e.ownerDocument;
    }
    function b(e) {
        const t = j("style");
        return y(v(e), t), t.sheet;
    }
    function y(e, t) {
        return C(e.head || e, t), t.sheet;
    }
    function x(e, t, n) {
        e.insertBefore(t, n || null);
    }
    function k(e) {
        e.parentNode && e.parentNode.removeChild(e);
    }
    function j(e) {
        return document.createElement(e);
    }
    function L(e) {
        return document.createElementNS("http://www.w3.org/2000/svg", e);
    }
    function _(e) {
        return document.createTextNode(e);
    }
    function D() {
        return _(" ");
    }
    function H() {
        return _("");
    }
    function M(e, t, n, r) {
        return e.addEventListener(t, n, r), () => e.removeEventListener(t, n, r);
    }
    function q(e) {
        return function (t) {
            return t.preventDefault(), e.call(this, t);
        };
    }
    function E(e, t, n) {
        null == n ? e.removeAttribute(t) : e.getAttribute(t) !== n && e.setAttribute(t, n);
    }
    function P(e, t) {
        (t = "" + t), e.wholeText !== t && (e.data = t);
    }
    function z(e, t, n, r) {
        null === n ? e.style.removeProperty(t) : e.style.setProperty(t, n, r ? "important" : "");
    }
    function A(e, t, n) {
        e.classList[n ? "add" : "remove"](t);
    }
    const Z = new Map();
    let S,
        V = 0;
    function N(e, t, n, r, i, o, a, s = 0) {
        const c = 16.666 / r;
        let l = "{\n";
        for (let e = 0; e <= 1; e += c) {
            const r = t + (n - t) * o(e);
            l += 100 * e + `%{${a(r, 1 - r)}}\n`;
        }
        const d = l + `100% {${a(n, 1 - n)}}\n}`,
            p = `__svelte_${(function (e) {
                let t = 5381,
                    n = e.length;
                for (; n--;) t = ((t << 5) - t) ^ e.charCodeAt(n);
                return t >>> 0;
            })(d)}_${s}`,
            u = v(e),
            { stylesheet: f, rules: m } =
                Z.get(u) ||
                (function (e, t) {
                    const n = { stylesheet: b(t), rules: {} };
                    return Z.set(e, n), n;
                })(u, e);
        m[p] || ((m[p] = !0), f.insertRule(`@keyframes ${p} ${d}`, f.cssRules.length));
        const h = e.style.animation || "";
        return (e.style.animation = `${h ? `${h}, ` : ""}${p} ${r}ms linear ${i}ms 1 both`), (V += 1), p;
    }
    function W(e, t) {
        const n = (e.style.animation || "").split(", "),
            r = n.filter(t ? (e) => e.indexOf(t) < 0 : (e) => -1 === e.indexOf("__svelte")),
            i = n.length - r.length;
        i &&
            ((e.style.animation = r.join(", ")),
                (V -= i),
                V ||
                f(() => {
                    V ||
                        (Z.forEach((e) => {
                            const { ownerNode: t } = e.stylesheet;
                            t && k(t);
                        }),
                            Z.clear());
                }));
    }
    function R(e) {
        S = e;
    }
    function T(e) {
        (function () {
            if (!S) throw new Error("Function called outside component initialization");
            return S;
        })().$$.on_mount.push(e);
    }
    function O(e, t) {
        const n = e.$$.callbacks[t.type];
        n && n.slice().forEach((e) => e.call(this, t));
    }
    const B = [],
        F = [],
        I = [],
        U = [],
        Q = Promise.resolve();
    let J = !1;
    function X(e) {
        I.push(e);
    }
    const Y = new Set();
    let G,
        K = 0;
    function ee() {
        const e = S;
        do {
            for (; K < B.length;) {
                const e = B[K];
                K++, R(e), te(e.$$);
            }
            for (R(null), B.length = 0, K = 0; F.length;) F.pop()();
            for (let e = 0; e < I.length; e += 1) {
                const t = I[e];
                Y.has(t) || (Y.add(t), t());
            }
            I.length = 0;
        } while (B.length);
        for (; U.length;) U.pop()();
        (J = !1), Y.clear(), R(e);
    }
    function te(e) {
        if (null !== e.fragment) {
            e.update(), o(e.before_update);
            const t = e.dirty;
            (e.dirty = [-1]), e.fragment && e.fragment.p(e.ctx, t), e.after_update.forEach(X);
        }
    }
    function ne() {
        return (
            G ||
            ((G = Promise.resolve()),
                G.then(() => {
                    G = null;
                })),
            G
        );
    }
    function re(e, t, n) {
        e.dispatchEvent(
            (function (e, t, { bubbles: n = !1, cancelable: r = !1 } = {}) {
                const i = document.createEvent("CustomEvent");
                return i.initCustomEvent(e, n, r, t), i;
            })(`${t ? "intro" : "outro"}${n}`)
        );
    }
    const ie = new Set();
    let oe;
    function ae() {
        oe = { r: 0, c: [], p: oe };
    }
    function se() {
        oe.r || o(oe.c), (oe = oe.p);
    }
    function ce(e, t) {
        e && e.i && (ie.delete(e), e.i(t));
    }
    function le(e, t, n, r) {
        if (e && e.o) {
            if (ie.has(e)) return;
            ie.add(e),
                oe.c.push(() => {
                    ie.delete(e), r && (n && e.d(1), r());
                }),
                e.o(t);
        } else r && r();
    }
    const de = { duration: 0 };
    function pe(e, r, i) {
        let o,
            s,
            c = r(e, i),
            l = !1,
            d = 0;
        function p() {
            o && W(e, o);
        }
        function f() {
            const { delay: r = 0, duration: i = 300, easing: a = n, tick: f = t, css: m } = c || de;
            m && (o = N(e, 0, 1, i, r, a, m, d++)), f(0, 1);
            const h = u() + r,
                $ = h + i;
            s && s.abort(),
                (l = !0),
                X(() => re(e, !0, "start")),
                (s = g((t) => {
                    if (l) {
                        if (t >= $) return f(1, 0), re(e, !0, "end"), p(), (l = !1);
                        if (t >= h) {
                            const e = a((t - h) / i);
                            f(e, 1 - e);
                        }
                    }
                    return l;
                }));
        }
        let m = !1;
        return {
            start() {
                m || ((m = !0), W(e), a(c) ? ((c = c()), ne().then(f)) : f());
            },
            invalidate() {
                m = !1;
            },
            end() {
                l && (p(), (l = !1));
            },
        };
    }
    function ue(e, r, i) {
        let s,
            c = r(e, i),
            l = !0;
        const d = oe;
        function p() {
            const { delay: r = 0, duration: i = 300, easing: a = n, tick: p = t, css: f } = c || de;
            f && (s = N(e, 1, 0, i, r, a, f));
            const m = u() + r,
                h = m + i;
            X(() => re(e, !1, "start")),
                g((t) => {
                    if (l) {
                        if (t >= h) return p(0, 1), re(e, !1, "end"), --d.r || o(d.c), !1;
                        if (t >= m) {
                            const e = a((t - m) / i);
                            p(1 - e, e);
                        }
                    }
                    return l;
                });
        }
        return (
            (d.r += 1),
            a(c)
                ? ne().then(() => {
                    (c = c()), p();
                })
                : p(),
            {
                end(t) {
                    t && c.tick && c.tick(1, 0), l && (s && W(e, s), (l = !1));
                },
            }
        );
    }
    function fe(e) {
        e && e.c();
    }
    function me(e, t, n, i) {
        const { fragment: s, after_update: c } = e.$$;
        s && s.m(t, n),
            i ||
            X(() => {
                const t = e.$$.on_mount.map(r).filter(a);
                e.$$.on_destroy ? e.$$.on_destroy.push(...t) : o(t), (e.$$.on_mount = []);
            }),
            c.forEach(X);
    }
    function he(e, t) {
        const n = e.$$;
        null !== n.fragment && (o(n.on_destroy), n.fragment && n.fragment.d(t), (n.on_destroy = n.fragment = null), (n.ctx = []));
    }
    function ge(e, t) {
        -1 === e.$$.dirty[0] && (B.push(e), J || ((J = !0), Q.then(ee)), e.$$.dirty.fill(0)), (e.$$.dirty[(t / 31) | 0] |= 1 << t % 31);
    }
    function $e(e, n, r, a, s, c, l, d = [-1]) {
        const p = S;
        R(e);
        const u = (e.$$ = {
            fragment: null,
            ctx: [],
            props: c,
            update: t,
            not_equal: s,
            bound: i(),
            on_mount: [],
            on_destroy: [],
            on_disconnect: [],
            before_update: [],
            after_update: [],
            context: new Map(n.context || (p ? p.$$.context : [])),
            callbacks: i(),
            dirty: d,
            skip_bound: !1,
            root: n.target || p.$$.root,
        });
        l && l(u.root);
        let f = !1;
        if (
            ((u.ctx = r
                ? r(e, n.props || {}, (t, n, ...r) => {
                    const i = r.length ? r[0] : n;
                    return u.ctx && s(u.ctx[t], (u.ctx[t] = i)) && (!u.skip_bound && u.bound[t] && u.bound[t](i), f && ge(e, t)), n;
                })
                : []),
                u.update(),
                (f = !0),
                o(u.before_update),
                (u.fragment = !!a && a(u.ctx)),
                n.target)
        ) {
            if (n.hydrate) {
                const e = (function (e) {
                    return Array.from(e.childNodes);
                })(n.target);
                u.fragment && u.fragment.l(e), e.forEach(k);
            } else u.fragment && u.fragment.c();
            n.intro && ce(e.$$.fragment), me(e, n.target, n.anchor, n.customElement), ee();
        }
        R(p);
    }
    class Ce {
        $destroy() {
            he(this, 1), (this.$destroy = t);
        }
        $on(e, n) {
            if (!a(n)) return t;
            const r = this.$$.callbacks[e] || (this.$$.callbacks[e] = []);
            return (
                r.push(n),
                () => {
                    const e = r.indexOf(n);
                    -1 !== e && r.splice(e, 1);
                }
            );
        }
        $set(e) {
            var t;
            this.$$set && ((t = e), 0 !== Object.keys(t).length) && ((this.$$.skip_bound = !0), this.$$set(e), (this.$$.skip_bound = !1));
        }
    }
    function we(e) {
        let n, r;
        return {
            c() {
                (n = L("svg")),
                    (r = L("path")),
                    E(r, "d", "M0.499959 -2.40414e-07L0.499955 64L6 70L0.499956 76L0.499956 142"),
                    E(r, "stroke", "#F0F0F0"),
                    E(n, "width", "7"),
                    E(n, "height", "142"),
                    E(n, "viewBox", "0 0 7 142"),
                    E(n, "fill", "none"),
                    E(n, "xmlns", "http://www.w3.org/2000/svg");
            },
            m(e, t) {
                x(e, n, t), C(n, r);
            },
            p: t,
            i: t,
            o: t,
            d(e) {
                e && k(n);
            },
        };
    }
    class ve extends Ce {
        constructor(e) {
            super(), $e(this, e, null, we, s, {});
        }
    }
    const be = {
        cart: { title: "Twój koszyk", scanQR: "Zapłać skanując QR kod" },
        safe: { title: "Bezpieczna płatność" },
        button: { pay: "Zapłać z e-payeye" },
        app: { title: "Dokończ płatność w aplikacji mobilnej PayEye", rescan: "Zeskanuj ponownie QR kod" },
        progress: { title: "Przetwarzanie płatności" },
        rejected: { title: "Płatność nie mogła zostać zrealizowana" },
        success: { title: "Płatność zakończona!" },
    },
        ye = {
            cart: { title: "Your cart", scanQR: "Pay by scanning the QR code" },
            safe: { title: "Secure payment" },
            button: { pay: "Pay with e-payeye" },
            app: { title: "Complete the payment in the PayEye mobile app", rescan: "Scan the QR code again" },
            progress: { title: "Payment processing" },
            rejected: { title: "The payment could not be processed" },
            success: { title: "Payment completed!" },
        };
    class xe {
        static initData() {
            "pl-PL" === this.language || "pl" === this.language.toLowerCase() ? (this.data = be) : (this.data = ye);
        }
    }
    function ke(e) {
        w(e, "pe-1di034j", ".qr.pe-1di034j{position:relative}.heading.pe-1di034j{font-size:0.75rem;line-height:1;color:#272445}img.pe-1di034j{width:6.875rem;height:6.875rem}");
    }
    function je(e) {
        let n, r, i, o, a, s;
        return {
            c() {
                (n = j("div")),
                    (r = j("div")),
                    (i = j("img")),
                    (a = D()),
                    (s = j("div")),
                    (s.textContent = `${xe.data.cart.scanQR}`),
                    l(i.src, (o = e[0])) || E(i, "src", o),
                    E(i, "alt", "PayEye Payments QR Code"),
                    E(i, "class", "pe-1di034j"),
                    E(s, "class", "heading pe-1di034j"),
                    E(n, "class", "qr pe-1di034j");
            },
            m(e, t) {
                x(e, n, t), C(n, r), C(r, i), C(n, a), C(n, s);
            },
            p(e, [t]) {
                1 & t && !l(i.src, (o = e[0])) && E(i, "src", o);
            },
            i: t,
            o: t,
            d(e) {
                e && k(n);
            },
        };
    }
    function Le(e, t, n) {
        let { qr: r } = t;
        return (
            (e.$$set = (e) => {
                "qr" in e && n(0, (r = e.qr));
            }),
            [r]
        );
    }
    class _e extends Ce {
        constructor(e) {
            super(), $e(this, e, Le, je, s, { qr: 0 }, ke);
        }
    }
    function De(e) {
        w(
            e,
            "pe-1mvdr6l",
            ".click.pe-1mvdr6l{cursor:pointer}.basket-wrapper.pe-1mvdr6l{margin-bottom:0.75rem}.heading.pe-1mvdr6l{margin-bottom:0.5rem;font-size:0.75rem;line-height:1rem;color:#706D84}.regular-price.pe-1mvdr6l{color:#706D84;text-decoration:line-through;font-size:0.75rem;line-height:1;margin-bottom:0.5rem}.price.pe-1mvdr6l{font-weight:bold;color:#00AD93;font-size:1.125rem;line-height:1}"
        );
    }
    function He(e) {
        let t,
            n,
            r,
            i = e[0].cart.regularPrice + "";
        return {
            c() {
                (t = j("div")), (n = _(i)), (r = _(" PLN")), E(t, "class", "regular-price pe-1mvdr6l");
            },
            m(e, i) {
                x(e, t, i), C(t, n), C(t, r);
            },
            p(e, t) {
                1 & t && i !== (i = e[0].cart.regularPrice + "") && P(n, i);
            },
            d(e) {
                e && k(t);
            },
        };
    }
    function Me(e) {
        let n,
            r,
            i,
            a,
            s,
            c,
            l,
            d,
            p,
            u,
            f,
            m = e[0].cart.price + "",
            h = e[0].cart.price !== e[0].cart.regularPrice && He(e);
        return {
            c() {
                (n = j("div")),
                    (r = j("div")),
                    (r.innerHTML =
                        '<svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_132_19674)"><path d="M14 48C16.2091 48 18 46.2091 18 44C18 41.7909 16.2091 40 14 40C11.7909 40 10 41.7909 10 44C10 46.2091 11.7909 48 14 48Z" fill="#D9D9D9" fill-opacity="0.507805"></path><path d="M34 48C36.2091 48 38 46.2091 38 44C38 41.7909 36.2091 40 34 40C31.7909 40 30 41.7909 30 44C30 46.2091 31.7909 48 34 48Z" fill="#D9D9D9" fill-opacity="0.507805"></path><path d="M47.3698 2.672C46.9947 2.29706 46.4861 2.08643 45.9558 2.08643C45.4255 2.08643 44.9168 2.29706 44.5418 2.672L34.2238 13L31.1218 9.762C30.9398 9.57263 30.7222 9.42097 30.4816 9.31567C30.241 9.21038 29.982 9.1535 29.7194 9.1483C29.4568 9.1431 29.1957 9.18968 28.9511 9.28537C28.7065 9.38106 28.4831 9.52399 28.2938 9.706C28.1044 9.88801 27.9528 10.1055 27.8475 10.3462C27.7422 10.5868 27.6853 10.8458 27.6801 11.1084C27.6696 11.6388 27.8702 12.1516 28.2378 12.534L31.4658 15.892C31.8097 16.2635 32.2255 16.5614 32.6878 16.7676C33.1502 16.9739 33.6496 17.0842 34.1558 17.092H34.2218C34.7179 17.0936 35.2094 16.9967 35.6677 16.8069C36.1261 16.617 36.5421 16.338 36.8918 15.986L47.3698 5.5C47.7447 5.12494 47.9554 4.61633 47.9554 4.086C47.9554 3.55567 47.7447 3.04705 47.3698 2.672Z" fill="#D9D9D9" fill-opacity="0.507805"></path><path d="M43.8 18.032C43.5414 17.9853 43.2762 17.99 43.0194 18.0458C42.7626 18.1017 42.5194 18.2075 42.3036 18.3574C42.0877 18.5073 41.9036 18.6983 41.7616 18.9194C41.6196 19.1405 41.5225 19.3874 41.476 19.646L41.22 21.064C40.9705 22.4486 40.2424 23.7016 39.1628 24.6038C38.0832 25.5061 36.7209 26.0003 35.314 26H10.836L8.956 10H22C22.5304 10 23.0391 9.78929 23.4142 9.41421C23.7893 9.03914 24 8.53043 24 8C24 7.46957 23.7893 6.96086 23.4142 6.58579C23.0391 6.21071 22.5304 6 22 6H8.484L8.4 5.296C8.22764 3.8372 7.52612 2.49231 6.42838 1.51621C5.33063 0.540109 3.91295 0.000625673 2.444 0L2 0C1.46957 0 0.960859 0.210714 0.585787 0.585786C0.210714 0.960859 0 1.46957 0 2C0 2.53043 0.210714 3.03914 0.585787 3.41421C0.960859 3.78929 1.46957 4 2 4H2.444C2.93387 4.00006 3.40667 4.17991 3.77274 4.50543C4.13881 4.83095 4.37269 5.2795 4.43 5.766L7.182 29.166C7.4677 31.5996 8.63696 33.8436 10.4678 35.472C12.2987 37.1005 14.6637 38.0001 17.114 38H38C38.5304 38 39.0391 37.7893 39.4142 37.4142C39.7893 37.0391 40 36.5304 40 36C40 35.4696 39.7893 34.9609 39.4142 34.5858C39.0391 34.2107 38.5304 34 38 34H17.114C15.873 34.0002 14.6625 33.6157 13.6492 32.8993C12.6359 32.1829 11.8697 31.17 11.456 30H35.314C37.6586 30.0001 39.9287 29.1765 41.7276 27.6729C43.5266 26.1693 44.7401 24.0814 45.156 21.774L45.412 20.354C45.5058 19.8324 45.3887 19.2948 45.0865 18.8594C44.7842 18.4241 44.3215 18.1265 43.8 18.032Z" fill="#D9D9D9" fill-opacity="0.507805"></path></g><defs><clipPath id="clip0_132_19674"><rect width="48" height="48" fill="white"></rect></clipPath></defs></svg>'),
                    (i = D()),
                    (a = j("div")),
                    (a.textContent = `${xe.data.cart.title}:`),
                    (s = D()),
                    h && h.c(),
                    (c = D()),
                    (l = j("div")),
                    (d = _(m)),
                    (p = _(" PLN")),
                    E(r, "class", "basket-wrapper pe-1mvdr6l"),
                    E(a, "class", "heading pe-1mvdr6l"),
                    E(l, "class", "price pe-1mvdr6l"),
                    E(n, "role", "button"),
                    E(n, "class", "click pe-1mvdr6l");
            },
            m(t, o) {
                x(t, n, o), C(n, r), C(n, i), C(n, a), C(n, s), h && h.m(n, null), C(n, c), C(n, l), C(l, d), C(l, p), u || ((f = [M(n, "click", q(e[1])), M(n, "keydown", e[2])]), (u = !0));
            },
            p(e, [t]) {
                e[0].cart.price !== e[0].cart.regularPrice ? (h ? h.p(e, t) : ((h = He(e)), h.c(), h.m(n, c))) : h && (h.d(1), (h = null)), 1 & t && m !== (m = e[0].cart.price + "") && P(d, m);
            },
            i: t,
            o: t,
            d(e) {
                e && k(n), h && h.d(), (u = !1), o(f);
            },
        };
    }
    function qe(e, t, n) {
        let { cart: r } = t;
        return (
            (e.$$set = (e) => {
                "cart" in e && n(0, (r = e.cart));
            }),
            [
                r,
                () => (r.cart.url ? (window.location.href = r.cart.url) : void 0),
                function (t) {
                    O.call(this, e, t);
                },
            ]
        );
    }
    class Ee extends Ce {
        constructor(e) {
            super(), $e(this, e, qe, Me, s, { cart: 0 }, De);
        }
    }
    function Pe(e) {
        w(e, "pe-1os1qqp", ".content-grid.pe-1os1qqp{display:grid;align-items:center;grid-template-columns:1fr auto 1fr;grid-gap:1.25rem}");
    }
    function ze(e) {
        let t, n, r, i, o, a, s;
        return (
            (n = new Ee({ props: { cart: e[0] } })),
            (i = new ve({})),
            (a = new _e({ props: { qr: e[0].cart.qr } })),
            {
                c() {
                    (t = j("div")), fe(n.$$.fragment), (r = D()), fe(i.$$.fragment), (o = D()), fe(a.$$.fragment), E(t, "class", "content-grid pe-1os1qqp");
                },
                m(e, c) {
                    x(e, t, c), me(n, t, null), C(t, r), me(i, t, null), C(t, o), me(a, t, null), (s = !0);
                },
                p(e, [t]) {
                    const r = {};
                    1 & t && (r.cart = e[0]), n.$set(r);
                    const i = {};
                    1 & t && (i.qr = e[0].cart.qr), a.$set(i);
                },
                i(e) {
                    s || (ce(n.$$.fragment, e), ce(i.$$.fragment, e), ce(a.$$.fragment, e), (s = !0));
                },
                o(e) {
                    le(n.$$.fragment, e), le(i.$$.fragment, e), le(a.$$.fragment, e), (s = !1);
                },
                d(e) {
                    e && k(t), he(n), he(i), he(a);
                },
            }
        );
    }
    function Ae(e, t, n) {
        let { cart: r } = t;
        return (
            (e.$$set = (e) => {
                "cart" in e && n(0, (r = e.cart));
            }),
            [r]
        );
    }
    class Ze extends Ce {
        constructor(e) {
            super(), $e(this, e, Ae, ze, s, { cart: 0 }, Pe);
        }
    }
    class Se {
        static get(e) {
            return fetch(e).then((e) => {
                if (!e.ok) throw new Error(e.status.toString());
                return e.json();
            });
        }
        static put(e, t) {
            const n = new Headers();
            return (
                n.append("Content-Type", "application/json"),
                fetch(e, { method: "PUT", headers: n, body: JSON.stringify(t) }).then((e) => {
                    if (!e.ok) throw new Error(e.status.toString());
                    if (204 !== e.status) return e.json();
                })
            );
        }
    }
    const Ve = window.payeye,
        { apiUrl: Ne } = Ve,
        We = [];
    function Re(e, n = t) {
        let r;
        const i = new Set();
        function o(t) {
            if (s(e, t) && ((e = t), r)) {
                const t = !We.length;
                for (const t of i) t[1](), We.push(t, e);
                if (t) {
                    for (let e = 0; e < We.length; e += 2) We[e][0](We[e + 1]);
                    We.length = 0;
                }
            }
        }
        return {
            set: o,
            update: function (t) {
                o(t(e));
            },
            subscribe: function (a, s = t) {
                const c = [a, s];
                return (
                    i.add(c),
                    1 === i.size && (r = n(o) || t),
                    a(e),
                    () => {
                        i.delete(c), 0 === i.size && (r(), (r = null));
                    }
                );
            },
        };
    }
    const Te = "epayeyeBrowser",
        Oe = new URLSearchParams(window.location.search);
    Oe.has("source") && Oe.get("source") === Te && sessionStorage.setItem("source", Te);
    const Be = sessionStorage.getItem("source") === Te,
        Fe = Re(null);
    var Ie;
    Fe.subscribe((e) => {
        var t, n;
        Be && (null === (t = window.webkit) || void 0 === t || t.messageHandlers.epayeyeWidget.postMessage(e), null === (n = window.epayeyeWidget) || void 0 === n || n.postMessage(JSON.stringify(e)));
    }),
        (function (e) {
            (e[(e.qr = 1)] = "qr"), (e[(e.app = 2)] = "app"), (e[(e.progress = 3)] = "progress"), (e[(e.success = 4)] = "success"), (e[(e.rejected = 5)] = "rejected");
        })(Ie || (Ie = {}));
    const Ue = Re(Ie.qr);
    function Qe(e) {
        w(
            e,
            "pe-tb6sbh",
            '.content-grid.pe-tb6sbh{position:relative;display:grid;max-width:9.7rem;font-size:0.75rem;line-height:1rem;color:#706D84}.icon.pe-tb6sbh{position:relative}.svg.pe-tb6sbh{margin-bottom:0.75rem;transition:all ease-in-out 0.1s;animation:pe-tb6sbh-shake 2.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) infinite;perspective:1000px}.title.pe-tb6sbh{margin-bottom:0.75rem}.rescan.pe-tb6sbh{position:relative;padding:0;font-size:0.75rem;line-height:1rem;color:#272445;text-decoration:none;cursor:pointer;transition:color ease-in-out 0.333s}.rescan.pe-tb6sbh:hover{color:#00AD93}.rescan.pe-tb6sbh:hover:before{background-color:#00AD93}.rescan.pe-tb6sbh:before{position:absolute;bottom:0;left:0.125rem;width:calc(100% - 0.25rem);height:1px;content:"";background-color:#272445;transition:background-color ease-in-out 0.333s}@keyframes pe-tb6sbh-shake{4%,36%{transform:translate3d(-1px, 0, 0)}8%,32%{transform:translate3d(2px, 0, 0)}12%,20%,28%{transform:translate3d(-4px, 0, 0)}16%,24%{transform:translate3d(4px, 0, 0)}}'
        );
    }
    function Je(e) {
        let n, r, i, a, s, c, l, d;
        return {
            c() {
                (n = j("div")),
                    (r = j("div")),
                    (r.innerHTML =
                        '<div class="svg pe-tb6sbh"><svg width="33" height="62" viewBox="0 0 33 62" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_192_1112)"><path d="M29.6504 0.829102H3.34957C1.94866 0.829102 0.812988 1.98725 0.812988 3.41589V58.5841C0.812988 60.0128 1.94866 61.1709 3.34957 61.1709H29.6504C31.0513 61.1709 32.187 60.0128 32.187 58.5841V3.41589C32.187 1.98725 31.0513 0.829102 29.6504 0.829102Z" stroke="#D9D9D9" stroke-width="2" stroke-miterlimit="10"></path><path d="M8.86189 0.829102H24.1302V3.06767C24.1302 3.93822 23.4391 4.65125 22.5773 4.65125H10.4066C9.55295 4.65125 8.85376 3.94651 8.85376 3.06767V0.829102H8.86189Z" stroke="#D9D9D9" stroke-width="2" stroke-miterlimit="10"></path><path d="M16.504 56.1382C17.8376 56.1382 18.9186 55.0358 18.9186 53.6758C18.9186 52.3158 17.8376 51.2134 16.504 51.2134C15.1704 51.2134 14.0894 52.3158 14.0894 53.6758C14.0894 55.0358 15.1704 56.1382 16.504 56.1382Z" stroke="#D9D9D9" stroke-width="2" stroke-miterlimit="10"></path><path d="M16.504 38.1303C21.1378 38.1303 24.8943 34.2995 24.8943 29.574C24.8943 24.8485 21.1378 21.0177 16.504 21.0177C11.8702 21.0177 8.11377 24.8485 8.11377 29.574C8.11377 34.2995 11.8702 38.1303 16.504 38.1303Z" stroke="#00AD93" stroke-width="2" stroke-miterlimit="10"></path><path d="M21.0164 29.574H11.4473" stroke="#00AD93" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" class="arrow"></path><path d="M18.74 32.8074C19.6749 31.7296 20.6099 30.6518 21.553 29.5739C20.618 28.4961 19.6831 27.4183 18.74 26.3405" stroke="#00AD93" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="arrow"></path></g><defs><clipPath id="clip0_192_1112"><rect width="33" height="62" fill="white"></rect></clipPath></defs></svg></div> \n        <div class="pulse-wrapper"><div class="pulse"></div></div>'),
                    (i = D()),
                    (a = j("div")),
                    (a.textContent = `${xe.data.app.title}`),
                    (s = D()),
                    (c = j("div")),
                    (c.textContent = `${xe.data.app.rescan}`),
                    E(r, "class", "icon pe-tb6sbh"),
                    E(a, "class", "title pe-tb6sbh"),
                    E(c, "class", "rescan pe-tb6sbh"),
                    E(c, "role", "button"),
                    E(n, "class", "content-grid pe-tb6sbh");
            },
            m(t, o) {
                x(t, n, o), C(n, r), C(n, i), C(n, a), C(n, s), C(n, c), l || ((d = [M(c, "keypress", e[1]), M(c, "click", q(e[0]))]), (l = !0));
            },
            p: t,
            i: t,
            o: t,
            d(e) {
                e && k(n), (l = !1), o(d);
            },
        };
    }
    function Xe(e, t, n) {
        let r;
        d(e, Fe, (e) => n(2, (r = e)));
        return [
            () => {
                var e;
                (e = r.cart.id), Se.put(`${Ne}/widget/status?cartId=${e}`, null), Ue.set(Ie.qr);
            },
            function (t) {
                O.call(this, e, t);
            },
        ];
    }
    class Ye extends Ce {
        constructor(e) {
            super(), $e(this, e, Xe, Je, s, {}, Qe);
        }
    }
    function Ge(e) {
        w(
            e,
            "pe-xnhlbx",
            "div.pe-xnhlbx{display:flex;align-items:center;justify-content:center;gap:0.25rem;padding:0.5rem;background-color:#E6F7F4;color:#00AD93;text-align:center;font-size:0.75rem;line-height:1rem;border-bottom-left-radius:0.75rem;border-bottom-right-radius:0.75rem}"
        );
    }
    function Ke(e) {
        let n,
            r,
            i,
            o,
            a,
            s,
            c,
            l,
            d,
            p,
            u = xe.data.safe.title + "";
        return {
            c() {
                (n = j("div")),
                    (r = L("svg")),
                    (i = L("g")),
                    (o = L("path")),
                    (a = L("path")),
                    (s = L("defs")),
                    (c = L("clipPath")),
                    (l = L("rect")),
                    (d = D()),
                    (p = _(u)),
                    E(
                        o,
                        "d",
                        "M9.5 4.212V3.5C9.5 2.57174 9.13125 1.6815 8.47487 1.02513C7.8185 0.368749 6.92826 0 6 0C5.07174 0 4.1815 0.368749 3.52513 1.02513C2.86875 1.6815 2.5 2.57174 2.5 3.5V4.212C2.05468 4.40635 1.67565 4.72626 1.40925 5.13261C1.14285 5.53895 1.00064 6.01412 1 6.5V9.5C1.00079 10.1628 1.26444 10.7982 1.73311 11.2669C2.20178 11.7356 2.8372 11.9992 3.5 12H8.5C9.1628 11.9992 9.79822 11.7356 10.2669 11.2669C10.7356 10.7982 10.9992 10.1628 11 9.5V6.5C10.9994 6.01412 10.8571 5.53895 10.5908 5.13261C10.3244 4.72626 9.94532 4.40635 9.5 4.212ZM3.5 3.5C3.5 2.83696 3.76339 2.20107 4.23223 1.73223C4.70107 1.26339 5.33696 1 6 1C6.66304 1 7.29893 1.26339 7.76777 1.73223C8.23661 2.20107 8.5 2.83696 8.5 3.5V4H3.5V3.5ZM10 9.5C10 9.89782 9.84196 10.2794 9.56066 10.5607C9.27936 10.842 8.89782 11 8.5 11H3.5C3.10218 11 2.72064 10.842 2.43934 10.5607C2.15804 10.2794 2 9.89782 2 9.5V6.5C2 6.10218 2.15804 5.72064 2.43934 5.43934C2.72064 5.15804 3.10218 5 3.5 5H8.5C8.89782 5 9.27936 5.15804 9.56066 5.43934C9.84196 5.72064 10 6.10218 10 6.5V9.5Z"
                    ),
                    E(o, "fill", "#00AD93"),
                    E(
                        a,
                        "d",
                        "M6 7C5.86739 7 5.74021 7.05268 5.64645 7.14645C5.55268 7.24021 5.5 7.36739 5.5 7.5V8.5C5.5 8.63261 5.55268 8.75979 5.64645 8.85355C5.74021 8.94732 5.86739 9 6 9C6.13261 9 6.25979 8.94732 6.35355 8.85355C6.44732 8.75979 6.5 8.63261 6.5 8.5V7.5C6.5 7.36739 6.44732 7.24021 6.35355 7.14645C6.25979 7.05268 6.13261 7 6 7Z"
                    ),
                    E(a, "fill", "#00AD93"),
                    E(i, "clip-path", "url(#clip0_132_18986)"),
                    E(l, "width", "12"),
                    E(l, "height", "12"),
                    E(l, "fill", "white"),
                    E(c, "id", "clip0_132_18986"),
                    E(r, "width", "12"),
                    E(r, "height", "12"),
                    E(r, "viewBox", "0 0 12 12"),
                    E(r, "fill", "none"),
                    E(r, "xmlns", "http://www.w3.org/2000/svg"),
                    E(n, "class", "pe-xnhlbx");
            },
            m(e, t) {
                x(e, n, t), C(n, r), C(r, i), C(i, o), C(i, a), C(r, s), C(s, c), C(c, l), C(n, d), C(n, p);
            },
            p: t,
            i: t,
            o: t,
            d(e) {
                e && k(n);
            },
        };
    }
    class et extends Ce {
        constructor(e) {
            super(), $e(this, e, null, Ke, s, {}, Ge);
        }
    }
    function tt(e) {
        let n;
        return {
            c() {
                (n = j("div")),
                    (n.innerHTML =
                        '<svg width="118" height="21" viewBox="0 0 118 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M92.8881 2.29285C93.2683 2.29285 93.6084 2.52709 93.7446 2.88064L93.7512 2.89786L96.3657 10.1038L98.5665 2.94139C98.683 2.56185 99.03 2.30099 99.4258 2.29285H99.4443H101.553C101.657 2.29285 101.761 2.3107 101.859 2.34514C102.332 2.51205 102.582 3.02688 102.425 3.50068L102.42 3.51665L98.4848 14.6731C97.1339 18.5086 95.0298 20.1558 91.7855 20.1742L91.6869 20.1746H90.479C89.9717 20.1746 89.5605 19.7634 89.5605 19.2561V17.5059C89.5605 16.9985 89.9717 16.5874 90.479 16.5874H91.2084C92.9777 16.5874 93.821 16.0372 94.3828 14.5152L94.3997 14.4686L89.8496 3.56488C89.8026 3.45277 89.7788 3.33252 89.7788 3.21101C89.7788 2.70965 90.1809 2.30161 90.6807 2.29285H90.697H92.8881Z" fill="#00AD93"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M110.519 1.9054C114.251 1.9054 117.001 4.79111 117.001 8.64728L117.002 8.82954C117.003 9.11545 116.989 9.48372 116.832 9.75867C116.744 9.91305 116.601 10.0349 116.446 10.0972C116.267 10.1683 116.074 10.1695 115.952 10.1695L111.936 10.1727C110.711 10.1742 109.327 10.1767 107.785 10.1792C108.27 11.5073 109.446 11.967 110.852 11.967C111.273 11.967 111.67 11.9006 112.027 11.7851C112.133 11.7506 112.236 11.7121 112.334 11.6695C112.516 11.5909 112.99 11.1845 113.377 11.1845C113.751 11.1845 113.951 11.3579 114.466 11.6439L114.52 11.6733C114.856 11.8565 115.14 12.0265 115.378 12.1746L115.474 12.2351C115.518 12.2623 115.559 12.2889 115.599 12.3146C115.941 12.5332 116.156 13.2867 115.1 14.0254C115.062 14.0523 115.025 14.0805 114.986 14.1077C113.84 14.9191 112.47 15.3889 110.8 15.3889C106.43 15.3889 103.721 12.4521 103.721 8.64728C103.721 4.84215 106.481 1.9054 110.519 1.9054ZM110.612 5.38478C109.166 5.38478 108.197 6.07341 107.83 7.32822L107.819 7.36642H113.329C112.918 5.91151 111.765 5.38478 110.612 5.38478Z" fill="#00AD93"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M44.2866 8.65981C44.2866 4.84748 41.5215 1.9054 38.1679 1.9054C36.4525 1.9054 35.1983 2.49381 34.3531 3.46615V3.18181V3.16553C34.3443 2.66573 33.9366 2.26333 33.4349 2.26333H31.431L31.4148 2.26364C30.9153 2.27241 30.5132 2.68014 30.5132 3.18181V19.2551V19.2713C30.522 19.7711 30.9297 20.1735 31.431 20.1735H33.4349L33.4512 20.1732C33.9507 20.1645 34.3531 19.7567 34.3531 19.2551V13.8538L34.3784 13.8829C35.2233 14.8377 36.4697 15.4145 38.1679 15.4145C41.5215 15.4145 44.2866 12.4721 44.2866 8.65981ZM34.3791 8.69542C34.3791 6.77891 35.6584 5.57828 37.4239 5.57828C39.1895 5.57828 40.4687 6.77891 40.4687 8.69542C40.4687 10.6116 39.1895 11.8125 37.4239 11.8125C35.6584 11.8125 34.3791 10.6116 34.3791 8.69542Z" fill="#272445"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M64.5776 2.29285C64.9577 2.29285 65.2978 2.52709 65.434 2.88064L65.4406 2.89786L68.0548 10.1038L70.256 2.94139C70.3725 2.56185 70.7195 2.30099 71.1153 2.29285H71.1338H73.2428C73.3468 2.29285 73.4502 2.3107 73.5482 2.34514C74.021 2.51205 74.2719 3.02688 74.114 3.50068L74.1087 3.51665L70.1743 14.6731C68.8233 18.5086 66.7192 20.1558 63.475 20.1742L63.3763 20.1746H62.1682C61.6612 20.1746 61.25 19.7634 61.25 19.2561V17.5059C61.25 16.9985 61.6612 16.5874 62.1682 16.5874H62.8978C64.6671 16.5874 65.5105 16.0372 66.0719 14.5152L66.0892 14.4686L61.539 3.56488C61.4921 3.45277 61.4683 3.33252 61.4683 3.21101C61.4683 2.70965 61.8704 2.30161 62.3702 2.29285H62.3864H64.5776Z" fill="#272445"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M55.7409 3.43421C54.8966 2.48129 53.65 1.9054 51.9517 1.9054C48.5978 1.9054 45.833 4.84215 45.833 8.64728C45.833 12.4521 48.5978 15.3889 51.9517 15.3889C53.6672 15.3889 54.9217 14.8017 55.7662 13.8312V14.1134L55.7666 14.1297C55.775 14.6291 56.1831 15.0315 56.6847 15.0315H58.6883L58.7046 15.0312C59.2044 15.0225 59.6068 14.6151 59.6068 14.1134V3.18118L59.6064 3.1649C59.5977 2.66511 59.1903 2.26302 58.6883 2.26302H56.6847H56.6684C56.1686 2.27179 55.7662 2.67951 55.7662 3.18118V3.46302L55.7409 3.43421ZM49.6509 8.69542C49.6509 6.77923 50.9302 5.57828 52.6954 5.57828C54.461 5.57828 55.7402 6.77923 55.7402 8.69542C55.7402 10.6116 54.461 11.8125 52.6954 11.8125C50.9302 11.8125 49.6509 10.6116 49.6509 8.69542Z" fill="#272445"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M82.2087 1.9054C85.9403 1.9054 88.6907 4.79111 88.6907 8.64728L88.6916 8.82954C88.6923 9.11545 88.6788 9.48372 88.5216 9.75867C88.4336 9.91305 88.2905 10.0349 88.1352 10.0972C87.9563 10.1683 87.7628 10.1695 87.641 10.1695L83.6254 10.1727C82.4007 10.1742 81.0165 10.1767 79.474 10.1792C79.9593 11.5073 81.1352 11.967 82.541 11.967C82.9625 11.967 83.3596 11.9006 83.7162 11.7851C83.8224 11.7506 83.9248 11.7121 84.0238 11.6695C84.2054 11.5909 84.6798 11.1845 85.0662 11.1845C85.4464 11.1845 85.6468 11.3639 86.182 11.6586L86.2093 11.6733C86.5453 11.8565 86.8296 12.0265 87.0673 12.1746L87.1631 12.2351C87.2067 12.2623 87.2486 12.2889 87.2887 12.3146C87.6307 12.5332 87.8452 13.2867 86.7895 14.0254C86.7513 14.0523 86.7137 14.0805 86.6755 14.1077C85.5294 14.9191 84.1597 15.3889 82.4896 15.3889C78.1192 15.3889 75.4102 12.4521 75.4102 8.64728C75.4102 4.84215 78.1703 1.9054 82.2087 1.9054ZM82.3017 5.38478C80.8555 5.38478 79.8866 6.07341 79.5193 7.32822L79.5083 7.36642H85.018C84.6077 5.91151 83.4547 5.38478 82.3017 5.38478Z" fill="#00AD93"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M18.8963 11.8666C17.7433 9.69931 14.5936 8.5682 11.9258 9.98616C9.2584 11.4038 8.13574 14.5078 9.41811 16.9191C10.7008 19.3304 13.9031 20.1358 16.5709 18.7181C19.2383 17.3005 20.0491 14.034 18.8963 11.8666Z" fill="#00AD93"></path><path fill-rule="evenodd" clip-rule="evenodd" d="M18.2689 5.3921C20.3247 5.86747 21.2294 3.28709 19.2684 2.0185C17.3078 0.7496 12.355 -1.32504 5.5235 1.17518C2.70512 2.2067 1.40772 3.41047 0.66367 4.43699C-0.0803834 5.4635 -0.268589 6.98919 0.460433 8.3727C1.18977 9.75653 4.16285 11.0333 6.90044 8.62166C11.972 4.15389 16.2127 4.91674 18.2689 5.3921Z" fill="#272445"></path></svg>');
            },
            m(e, t) {
                x(e, n, t);
            },
            p: t,
            i: t,
            o: t,
            d(e) {
                e && k(n);
            },
        };
    }
    class nt extends Ce {
        constructor(e) {
            super(), $e(this, e, null, tt, s, {});
        }
    }
    function rt(e) {
        w(
            e,
            "pe-ro0j7q",
            '.content.pe-ro0j7q{width:62px;height:62px;position:relative;margin-left:auto;margin-right:auto}.loader.pe-ro0j7q::before{content:"";position:absolute;left:0;border-radius:50%;height:100%;width:100%;background:conic-gradient(from 0deg, rgba(217, 217, 217, 0%), #D9D9D9);animation:pe-ro0j7q-loading 2s linear infinite}@keyframes pe-ro0j7q-loading{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}.loader.pe-ro0j7q::after{content:"";position:absolute;left:10%;top:10%;height:80%;width:80%;border-radius:50%;background:white}'
        );
    }
    function it(e) {
        let n;
        return {
            c() {
                (n = j("div")), (n.innerHTML = '<div class="loader pe-ro0j7q"></div>'), E(n, "class", "content pe-ro0j7q");
            },
            m(e, t) {
                x(e, n, t);
            },
            p: t,
            i: t,
            o: t,
            d(e) {
                e && k(n);
            },
        };
    }
    class ot extends Ce {
        constructor(e) {
            super(), $e(this, e, null, it, s, {}, rt);
        }
    }
    function at(e) {
        w(e, "pe-j509wd", ".content-grid.pe-j509wd{display:grid;max-width:8.5rem;font-size:0.75rem;line-height:1rem;color:#706D84}.space.pe-j509wd{margin-bottom:1.5rem}.text.pe-j509wd{margin-bottom:1rem}");
    }
    function st(e) {
        let n, r, i, o, a, s;
        return (
            (i = new ot({})),
            {
                c() {
                    (n = j("div")),
                        (r = j("div")),
                        fe(i.$$.fragment),
                        (o = D()),
                        (a = j("div")),
                        (a.textContent = `${xe.data.progress.title}`),
                        E(r, "class", "space pe-j509wd"),
                        E(a, "class", "text pe-j509wd"),
                        E(n, "class", "content-grid pe-j509wd");
                },
                m(e, t) {
                    x(e, n, t), C(n, r), me(i, r, null), C(n, o), C(n, a), (s = !0);
                },
                p: t,
                i(e) {
                    s || (ce(i.$$.fragment, e), (s = !0));
                },
                o(e) {
                    le(i.$$.fragment, e), (s = !1);
                },
                d(e) {
                    e && k(n), he(i);
                },
            }
        );
    }
    class ct extends Ce {
        constructor(e) {
            super(), $e(this, e, null, st, s, {}, at);
        }
    }
    function lt(e) {
        w(
            e,
            "pe-1af2ty7",
            ".wrapper.pe-1af2ty7{position:relative}svg.pe-1af2ty7{width:62px;animation:pe-1af2ty7-fixed .1s}circle.pe-1af2ty7{stroke-width:2;stroke-dasharray:188.5;stroke-dashoffset:188.5;animation:pe-1af2ty7-dash 1s ease forwards;transform-origin:center;transform:rotate(-90deg)}.checkmark.pe-1af2ty7{position:absolute;top:calc(50% - 5px);left:calc(50% - 13px);display:inline-block;width:22px;height:22px;transform:rotate(45deg)}.checkmark-left.pe-1af2ty7{position:absolute;width:2px;height:28px;background-color:#00AD93;left:11px;top:-13px;border-top-left-radius:2px;border-top-right-radius:2px;animation:pe-1af2ty7-right 1s}.checkmark-right.pe-1af2ty7{position:absolute;width:16px;height:2px;background-color:#00AD93;left:-3px;top:13px;border-bottom-left-radius:2px;border-top-left-radius:2px;animation:pe-1af2ty7-left 1s}@keyframes pe-1af2ty7-fixed{from{opacity:1}}@keyframes pe-1af2ty7-dash{to{stroke-dashoffset:0}}@keyframes pe-1af2ty7-left{from{left:-3px;width:0}}@keyframes pe-1af2ty7-right{from{top:13px;height:0}}"
        );
    }
    function dt(e) {
        let n;
        return {
            c() {
                (n = j("div")),
                    (n.innerHTML =
                        '<svg viewBox="0 0 62 62" class="pe-1af2ty7"><circle cx="31" cy="31" r="30" fill="none" stroke="#00AD93" stroke-width="2" class="pe-1af2ty7"></circle></svg> \n\n    <div class="checkmark pe-1af2ty7"><div class="checkmark-left pe-1af2ty7"></div> \n        <div class="checkmark-right pe-1af2ty7"></div></div>'),
                    E(n, "class", "wrapper pe-1af2ty7");
            },
            m(e, t) {
                x(e, n, t);
            },
            p: t,
            i: t,
            o: t,
            d(e) {
                e && k(n);
            },
        };
    }
    class pt extends Ce {
        constructor(e) {
            super(), $e(this, e, null, dt, s, {}, lt);
        }
    }
    function ut(e) {
        w(e, "pe-oj34cm", ".content-grid.pe-oj34cm{display:grid;max-width:8.5rem;font-size:0.75rem;line-height:1rem;color:#00AD93}.space.pe-oj34cm{display:flex;justify-content:center;margin-bottom:1.5rem}");
    }
    function ft(e) {
        let n,
            r,
            i,
            o,
            a,
            s,
            c = xe.data.success.title + "";
        return (
            (i = new pt({})),
            {
                c() {
                    (n = j("div")), (r = j("div")), fe(i.$$.fragment), (o = D()), (a = _(c)), E(r, "class", "space pe-oj34cm"), E(n, "class", "content-grid pe-oj34cm");
                },
                m(e, t) {
                    x(e, n, t), C(n, r), me(i, r, null), C(n, o), C(n, a), (s = !0);
                },
                p: t,
                i(e) {
                    s || (ce(i.$$.fragment, e), (s = !0));
                },
                o(e) {
                    le(i.$$.fragment, e), (s = !1);
                },
                d(e) {
                    e && k(n), he(i);
                },
            }
        );
    }
    class mt extends Ce {
        constructor(e) {
            super(), $e(this, e, null, ft, s, {}, ut);
        }
    }
    function ht(e) {
        w(
            e,
            "pe-1bn9udr",
            ".wrapper.pe-1bn9udr{position:relative}svg.pe-1bn9udr{width:62px;animation:pe-1bn9udr-fixed .1s}circle.pe-1bn9udr{stroke-width:2;stroke-dasharray:188.5;stroke-dashoffset:188.5;animation:pe-1bn9udr-dash 1s ease forwards;transform-origin:center;transform:rotate(-90deg)}.checkmark.pe-1bn9udr{position:absolute;top:50%;left:50%;width:36px;height:36px;transform:translate(-50%, -50%) rotate(45deg)}.checkmark-left.pe-1bn9udr{position:absolute;width:2px;height:36px;background-color:#E3282D;left:50%;top:50%;transform:translate(-50%, -50%);border-radius:2px;animation:pe-1bn9udr-left 1s;transform-origin:center}.checkmark-right.pe-1bn9udr{position:absolute;width:36px;height:2px;background-color:#E3282D;left:0;top:50%;transform:translateY(-50%);border-radius:2px;animation:pe-1bn9udr-right 1s}@keyframes pe-1bn9udr-fixed{from{opacity:1}}@keyframes pe-1bn9udr-left{from{height:0}}@keyframes pe-1bn9udr-dash{to{stroke-dashoffset:0}}@keyframes pe-1bn9udr-right{from{width:0}}"
        );
    }
    function gt(e) {
        let n;
        return {
            c() {
                (n = j("div")),
                    (n.innerHTML =
                        '<svg viewBox="0 0 62 62" class="pe-1bn9udr"><circle cx="31" cy="31" r="30" fill="none" stroke="#E3282D" stroke-width="2" class="pe-1bn9udr"></circle></svg> \n\n    <div class="checkmark pe-1bn9udr"><div class="checkmark-left pe-1bn9udr"></div> \n        <div class="checkmark-right pe-1bn9udr"></div></div>'),
                    E(n, "class", "wrapper pe-1bn9udr");
            },
            m(e, t) {
                x(e, n, t);
            },
            p: t,
            i: t,
            o: t,
            d(e) {
                e && k(n);
            },
        };
    }
    class $t extends Ce {
        constructor(e) {
            super(), $e(this, e, null, gt, s, {}, ht);
        }
    }
    function Ct(e) {
        w(e, "pe-icaj9t", ".content-grid.pe-icaj9t{display:grid;max-width:8.5rem;font-size:0.75rem;line-height:1rem;color:#E3282D}.space.pe-icaj9t{display:flex;justify-content:center;margin-bottom:1.5rem}");
    }
    function wt(e) {
        let n,
            r,
            i,
            o,
            a,
            s,
            c = xe.data.rejected.title + "";
        return (
            (i = new $t({})),
            {
                c() {
                    (n = j("div")), (r = j("div")), fe(i.$$.fragment), (o = D()), (a = _(c)), E(r, "class", "space pe-icaj9t"), E(n, "class", "content-grid pe-icaj9t");
                },
                m(e, t) {
                    x(e, n, t), C(n, r), me(i, r, null), C(n, o), C(n, a), (s = !0);
                },
                p: t,
                i(e) {
                    s || (ce(i.$$.fragment, e), (s = !0));
                },
                o(e) {
                    le(i.$$.fragment, e), (s = !1);
                },
                d(e) {
                    e && k(n), he(i);
                },
            }
        );
    }
    class vt extends Ce {
        constructor(e) {
            super(), $e(this, e, null, wt, s, {}, Ct);
        }
    }
    function bt(e) {
        w(
            e,
            "pe-kdg6ku",
            "button.pe-kdg6ku{display:flex;align-items:center;justify-content:center;padding:1rem 1.5rem .5rem 1.5rem;background-color:#ffffff;border-top-left-radius:.75rem;border-top-right-radius:.75rem;cursor:pointer;-webkit-appearance:none;border:0}"
        );
    }
    function yt(e) {
        let n, r, i;
        return {
            c() {
                (n = j("button")),
                    (n.innerHTML =
                        '<svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.21984 0.21959C0.360486 0.0789866 0.551217 0 0.75009 0C0.948963 0 1.13969 0.0789866 1.28034 0.21959L5.82284 4.76259C5.86972 4.80946 5.9333 4.83579 5.99959 4.83579C6.06588 4.83579 6.12946 4.80946 6.17634 4.76259L10.7193 0.22009C10.8611 0.0857825 11.0497 0.0120814 11.245 0.0146581C11.4403 0.0172348 11.6269 0.0958861 11.7651 0.233888C11.9033 0.371889 11.9822 0.558356 11.985 0.753637C11.9879 0.948918 11.9144 1.13761 11.7803 1.27959L7.23734 5.82009C6.90882 6.14771 6.4638 6.33168 5.99984 6.33168C5.53588 6.33168 5.09086 6.14771 4.76234 5.82009L0.21934 1.27959C0.0788896 1.13896 0 0.948341 0 0.74959C0 0.550839 0.0793896 0.360215 0.21984 0.21959Z" fill="#272445"></path></svg>'),
                    E(n, "class", "pe-kdg6ku");
            },
            m(t, o) {
                x(t, n, o), r || ((i = M(n, "click", e[0])), (r = !0));
            },
            p: t,
            i: t,
            o: t,
            d(e) {
                e && k(n), (r = !1), i();
            },
        };
    }
    function xt(e) {
        return [
            function (t) {
                O.call(this, e, t);
            },
        ];
    }
    class kt extends Ce {
        constructor(e) {
            super(), $e(this, e, xt, yt, s, {}, bt);
        }
    }
    let jt = JSON.parse(localStorage.getItem("payeye"));
    const Lt = window.innerWidth <= 568 && yn().ui.mobile.open;
    1.2 !== (null == jt ? void 0 : jt.version) && (jt = { launcher: Lt, openWidget: !Lt, version: 1.2 });
    const _t = Re(jt);
    _t.subscribe((e) => (localStorage.payeye = JSON.stringify(e)));
    class Dt {
        static toggleWidget() {
            _t.update((e) => {
                const t = !e.openWidget;
                return Object.assign(Object.assign({}, e), { openWidget: t, launcher: !t });
            });
        }
    }
    function Ht(e) {
        w(
            e,
            "pe-1mgh88v",
            "body{justify-content:flex-end}.widget.pe-1mgh88v{position:relative;background-color:#fff;border-radius:0.75rem;text-align:center;min-width:15.25rem}.toggle.pe-1mgh88v{position:absolute;top:-1.9rem;right:1.5rem}.container.pe-1mgh88v{padding:0 2rem 0.75rem 2rem;min-height:9.625rem;display:flex;align-items:center;justify-content:center}.logo.pe-1mgh88v{padding-top:1.5rem;margin-bottom:1rem}"
        );
    }
    function Mt(e) {
        let t, n;
        return (
            (t = new Ze({ props: { cart: e[1] } })),
            {
                c() {
                    fe(t.$$.fragment);
                },
                m(e, r) {
                    me(t, e, r), (n = !0);
                },
                p(e, n) {
                    const r = {};
                    2 & n && (r.cart = e[1]), t.$set(r);
                },
                i(e) {
                    n || (ce(t.$$.fragment, e), (n = !0));
                },
                o(e) {
                    le(t.$$.fragment, e), (n = !1);
                },
                d(e) {
                    he(t, e);
                },
            }
        );
    }
    function qt(e) {
        let t, n;
        return (
            (t = new Ye({})),
            {
                c() {
                    fe(t.$$.fragment);
                },
                m(e, r) {
                    me(t, e, r), (n = !0);
                },
                i(e) {
                    n || (ce(t.$$.fragment, e), (n = !0));
                },
                o(e) {
                    le(t.$$.fragment, e), (n = !1);
                },
                d(e) {
                    he(t, e);
                },
            }
        );
    }
    function Et(e) {
        let t, n;
        return (
            (t = new ct({})),
            {
                c() {
                    fe(t.$$.fragment);
                },
                m(e, r) {
                    me(t, e, r), (n = !0);
                },
                i(e) {
                    n || (ce(t.$$.fragment, e), (n = !0));
                },
                o(e) {
                    le(t.$$.fragment, e), (n = !1);
                },
                d(e) {
                    he(t, e);
                },
            }
        );
    }
    function Pt(e) {
        let t, n;
        return (
            (t = new mt({})),
            {
                c() {
                    fe(t.$$.fragment);
                },
                m(e, r) {
                    me(t, e, r), (n = !0);
                },
                i(e) {
                    n || (ce(t.$$.fragment, e), (n = !0));
                },
                o(e) {
                    le(t.$$.fragment, e), (n = !1);
                },
                d(e) {
                    he(t, e);
                },
            }
        );
    }
    function zt(e) {
        let t, n;
        return (
            (t = new vt({})),
            {
                c() {
                    fe(t.$$.fragment);
                },
                m(e, r) {
                    me(t, e, r), (n = !0);
                },
                i(e) {
                    n || (ce(t.$$.fragment, e), (n = !0));
                },
                o(e) {
                    le(t.$$.fragment, e), (n = !1);
                },
                d(e) {
                    he(t, e);
                },
            }
        );
    }
    function At(e) {
        let t, n, r, i, o, a, s, c, l, d, p, u, f, m, h, g;
        (r = new kt({})), r.$on("click", e[2]), (a = new nt({}));
        let $ = e[0] === Ie.qr && Mt(e),
            w = e[0] === Ie.app && qt(),
            v = e[0] === Ie.progress && Et(),
            b = e[0] === Ie.success && Pt(),
            y = e[0] === Ie.rejected && zt();
        return (
            (h = new et({})),
            {
                c() {
                    (t = j("div")),
                        (n = j("div")),
                        fe(r.$$.fragment),
                        (i = D()),
                        (o = j("div")),
                        fe(a.$$.fragment),
                        (s = D()),
                        (c = j("div")),
                        (l = j("div")),
                        $ && $.c(),
                        (d = D()),
                        w && w.c(),
                        (p = D()),
                        v && v.c(),
                        (u = D()),
                        b && b.c(),
                        (f = D()),
                        y && y.c(),
                        (m = D()),
                        fe(h.$$.fragment),
                        E(n, "class", "toggle pe-1mgh88v"),
                        E(o, "class", "logo pe-1mgh88v"),
                        E(l, "class", "content"),
                        E(c, "class", "container pe-1mgh88v"),
                        E(t, "class", "widget pe-1mgh88v");
                },
                m(e, k) {
                    x(e, t, k),
                        C(t, n),
                        me(r, n, null),
                        C(t, i),
                        C(t, o),
                        me(a, o, null),
                        C(t, s),
                        C(t, c),
                        C(c, l),
                        $ && $.m(l, null),
                        C(l, d),
                        w && w.m(l, null),
                        C(l, p),
                        v && v.m(l, null),
                        C(l, u),
                        b && b.m(l, null),
                        C(l, f),
                        y && y.m(l, null),
                        C(t, m),
                        me(h, t, null),
                        (g = !0);
                },
                p(e, [t]) {
                    e[0] === Ie.qr
                        ? $
                            ? ($.p(e, t), 1 & t && ce($, 1))
                            : (($ = Mt(e)), $.c(), ce($, 1), $.m(l, d))
                        : $ &&
                        (ae(),
                            le($, 1, 1, () => {
                                $ = null;
                            }),
                            se()),
                        e[0] === Ie.app
                            ? w
                                ? 1 & t && ce(w, 1)
                                : ((w = qt()), w.c(), ce(w, 1), w.m(l, p))
                            : w &&
                            (ae(),
                                le(w, 1, 1, () => {
                                    w = null;
                                }),
                                se()),
                        e[0] === Ie.progress
                            ? v
                                ? 1 & t && ce(v, 1)
                                : ((v = Et()), v.c(), ce(v, 1), v.m(l, u))
                            : v &&
                            (ae(),
                                le(v, 1, 1, () => {
                                    v = null;
                                }),
                                se()),
                        e[0] === Ie.success
                            ? b
                                ? 1 & t && ce(b, 1)
                                : ((b = Pt()), b.c(), ce(b, 1), b.m(l, f))
                            : b &&
                            (ae(),
                                le(b, 1, 1, () => {
                                    b = null;
                                }),
                                se()),
                        e[0] === Ie.rejected
                            ? y
                                ? 1 & t && ce(y, 1)
                                : ((y = zt()), y.c(), ce(y, 1), y.m(l, null))
                            : y &&
                            (ae(),
                                le(y, 1, 1, () => {
                                    y = null;
                                }),
                                se());
                },
                i(e) {
                    g || (ce(r.$$.fragment, e), ce(a.$$.fragment, e), ce($), ce(w), ce(v), ce(b), ce(y), ce(h.$$.fragment, e), (g = !0));
                },
                o(e) {
                    le(r.$$.fragment, e), le(a.$$.fragment, e), le($), le(w), le(v), le(b), le(y), le(h.$$.fragment, e), (g = !1);
                },
                d(e) {
                    e && k(t), he(r), he(a), $ && $.d(), w && w.d(), v && v.d(), b && b.d(), y && y.d(), he(h);
                },
            }
        );
    }
    function Zt(e, t, n) {
        let { step: r } = t,
            { cart: i } = t;
        return (
            (e.$$set = (e) => {
                "step" in e && n(0, (r = e.step)), "cart" in e && n(1, (i = e.cart));
            }),
            [r, i, () => Dt.toggleWidget()]
        );
    }
    class St extends Ce {
        constructor(e) {
            super(), $e(this, e, Zt, At, s, { step: 0, cart: 1 }, Ht);
        }
    }
    function Vt(e) {
        const t = e - 1;
        return t * t * t + 1;
    }
    function Nt(e, { delay: t = 0, duration: n = 400, easing: r = Vt, x: i = 0, y: o = 0, opacity: a = 0 } = {}) {
        const s = getComputedStyle(e),
            c = +s.opacity,
            l = "none" === s.transform ? "" : s.transform,
            d = c * (1 - a);
        return { delay: t, duration: n, easing: r, css: (e, t) => `\n\t\t\ttransform: ${l} translate(${(1 - e) * i}px, ${(1 - e) * o}px);\n\t\t\topacity: ${c - d * t}` };
    }
    function Wt(e) {
        w(e, "pe-357pdf", ".header.pe-357pdf{display:grid;grid-template-columns:auto 1fr;align-items:center;grid-gap:1.25rem}.safe.pe-357pdf{border-radius:0.75rem;overflow:hidden}");
    }
    function Rt(e) {
        let n, r, i, o, a, s;
        return (
            (r = new nt({})),
            (a = new et({})),
            {
                c() {
                    (n = j("div")), fe(r.$$.fragment), (i = D()), (o = j("div")), fe(a.$$.fragment), E(o, "class", "safe pe-357pdf"), E(n, "class", "header pe-357pdf");
                },
                m(e, t) {
                    x(e, n, t), me(r, n, null), C(n, i), C(n, o), me(a, o, null), (s = !0);
                },
                p: t,
                i(e) {
                    s || (ce(r.$$.fragment, e), ce(a.$$.fragment, e), (s = !0));
                },
                o(e) {
                    le(r.$$.fragment, e), le(a.$$.fragment, e), (s = !1);
                },
                d(e) {
                    e && k(n), he(r), he(a);
                },
            }
        );
    }
    class Tt extends Ce {
        constructor(e) {
            super(), $e(this, e, null, Rt, s, {}, Wt);
        }
    }
    function Ot(e) {
        w(
            e,
            "pe-264mdm",
            ".click.pe-264mdm{cursor:pointer}.title.pe-264mdm{margin-bottom:0.5rem;font-size:0.75rem;line-height:1rem;color:#706D84}.regular-price.pe-264mdm{color:#706D84;text-decoration:line-through;font-size:0.75rem;line-height:1;margin-bottom:0.5rem}.price.pe-264mdm{font-weight:bold;color:#00AD93;font-size:1.125rem;line-height:1}"
        );
    }
    function Bt(e) {
        let t,
            n,
            r,
            i = e[0].cart.regularPrice + "";
        return {
            c() {
                (t = j("div")), (n = _(i)), (r = _(" PLN")), E(t, "class", "regular-price pe-264mdm");
            },
            m(e, i) {
                x(e, t, i), C(t, n), C(t, r);
            },
            p(e, t) {
                1 & t && i !== (i = e[0].cart.regularPrice + "") && P(n, i);
            },
            d(e) {
                e && k(t);
            },
        };
    }
    function Ft(e) {
        let n,
            r,
            i,
            a,
            s,
            c,
            l,
            d,
            p,
            u = e[0].cart.price + "",
            f = e[0].cart.price !== e[0].cart.regularPrice && Bt(e);
        return {
            c() {
                (n = j("div")),
                    (r = j("div")),
                    (r.textContent = `${xe.data.cart.title}`),
                    (i = D()),
                    f && f.c(),
                    (a = D()),
                    (s = j("div")),
                    (c = _(u)),
                    (l = _(" PLN")),
                    E(r, "class", "title pe-264mdm"),
                    E(s, "class", "price pe-264mdm"),
                    E(n, "role", "button"),
                    E(n, "class", "click pe-264mdm");
            },
            m(t, o) {
                x(t, n, o), C(n, r), C(n, i), f && f.m(n, null), C(n, a), C(n, s), C(s, c), C(s, l), d || ((p = [M(n, "click", q(e[1])), M(n, "keydown", e[2])]), (d = !0));
            },
            p(e, [t]) {
                e[0].cart.price !== e[0].cart.regularPrice ? (f ? f.p(e, t) : ((f = Bt(e)), f.c(), f.m(n, a))) : f && (f.d(1), (f = null)), 1 & t && u !== (u = e[0].cart.price + "") && P(c, u);
            },
            i: t,
            o: t,
            d(e) {
                e && k(n), f && f.d(), (d = !1), o(p);
            },
        };
    }
    function It(e, t, n) {
        let { cart: r } = t;
        return (
            (e.$$set = (e) => {
                "cart" in e && n(0, (r = e.cart));
            }),
            [
                r,
                () => (r.cart.url ? (window.location.href = r.cart.url) : void 0),
                function (t) {
                    O.call(this, e, t);
                },
            ]
        );
    }
    class Ut extends Ce {
        constructor(e) {
            super(), $e(this, e, It, Ft, s, { cart: 0 }, Ot);
        }
    }
    function Qt(e) {
        w(
            e,
            "pe-kqiutj",
            'a.pe-kqiutj{display:flex;align-items:center;justify-content:center;padding:0.785rem 1rem;gap:0.625rem;background-color:#00AD93;-webkit-appearance:none;border:0;color:#ffffff;width:100%;font-size:0.875rem;line-height:1;border-radius:200px;font-family:"Inter", sans-serif;text-decoration:none}svg.pe-kqiutj{position:relative;animation:pe-kqiutj-right 2s infinite;top:1px}@keyframes pe-kqiutj-right{0%{right:0}25%{right:-0.5rem}50%{right:0}}'
        );
    }
    function Jt(e) {
        let n,
            r,
            i,
            o,
            a,
            s,
            c,
            l,
            d = xe.data.button.pay + "";
        return {
            c() {
                (n = j("a")),
                    (r = _(d)),
                    (i = D()),
                    (o = L("svg")),
                    (a = L("path")),
                    (s = L("path")),
                    E(a, "d", "M19.0508 7.44775H1.20972"),
                    E(a, "stroke", "white"),
                    E(a, "stroke-width", "2"),
                    E(a, "stroke-miterlimit", "10"),
                    E(a, "stroke-linecap", "round"),
                    E(s, "d", "M14.8066 13.4766C16.5498 11.4671 18.293 9.45753 20.0514 7.44798C18.3082 5.43842 16.565 3.42887 14.8066 1.41931"),
                    E(s, "stroke", "white"),
                    E(s, "stroke-width", "2"),
                    E(s, "stroke-linecap", "round"),
                    E(s, "stroke-linejoin", "round"),
                    E(o, "width", "22"),
                    E(o, "height", "15"),
                    E(o, "viewBox", "0 0 22 15"),
                    E(o, "fill", "none"),
                    E(o, "xmlns", "http://www.w3.org/2000/svg"),
                    E(o, "class", "pe-kqiutj"),
                    E(n, "href", e[0]),
                    E(n, "class", "pe-kqiutj");
            },
            m(t, d) {
                x(t, n, d), C(n, r), C(n, i), C(n, o), C(o, a), C(o, s), c || ((l = M(n, "click", q(e[1]))), (c = !0));
            },
            p(e, [t]) {
                1 & t && E(n, "href", e[0]);
            },
            i: t,
            o: t,
            d(e) {
                e && k(n), (c = !1), l();
            },
        };
    }
    function Xt(e, t, n) {
        let { deepLink: r } = t;
        return (
            (e.$$set = (e) => {
                "deepLink" in e && n(0, (r = e.deepLink));
            }),
            [r, () => (window.location.href = r)]
        );
    }
    class Yt extends Ce {
        constructor(e) {
            super(), $e(this, e, Xt, Jt, s, { deepLink: 0 }, Qt);
        }
    }
    function Gt(e) {
        w(
            e,
            "pe-13jr15b",
            ".widget.pe-13jr15b{position:relative;background-color:#fff;border-radius:1.5rem;text-align:center;min-width:15.25rem;padding:1.5rem}.toggle.pe-13jr15b{position:absolute;top:-1.9rem;right:1.5rem}.header.pe-13jr15b{margin-bottom:1.5rem}.cart.pe-13jr15b{margin-bottom:1.5rem}.line.pe-13jr15b{width:9.375rem;background-color:#F0F0F0;height:1px;margin-left:auto;margin-right:auto;margin-bottom:0.75rem}"
        );
    }
    function Kt(e) {
        let t, n, r, i, o, a, s, c, l, d, p, u, f, m;
        return (
            (r = new kt({})),
            r.$on("click", e[1]),
            (a = new Tt({})),
            (p = new Ut({ props: { cart: e[0] } })),
            (f = new Yt({ props: { deepLink: e[0].deepLink } })),
            {
                c() {
                    (t = j("div")),
                        (n = j("div")),
                        fe(r.$$.fragment),
                        (i = D()),
                        (o = j("div")),
                        fe(a.$$.fragment),
                        (s = D()),
                        (c = j("div")),
                        (l = D()),
                        (d = j("div")),
                        fe(p.$$.fragment),
                        (u = D()),
                        fe(f.$$.fragment),
                        E(n, "class", "toggle pe-13jr15b"),
                        E(o, "class", "header pe-13jr15b"),
                        E(c, "class", "line pe-13jr15b"),
                        E(d, "class", "cart pe-13jr15b"),
                        E(t, "class", "widget pe-13jr15b");
                },
                m(e, h) {
                    x(e, t, h), C(t, n), me(r, n, null), C(t, i), C(t, o), me(a, o, null), C(t, s), C(t, c), C(t, l), C(t, d), me(p, d, null), C(t, u), me(f, t, null), (m = !0);
                },
                p(e, [t]) {
                    const n = {};
                    1 & t && (n.cart = e[0]), p.$set(n);
                    const r = {};
                    1 & t && (r.deepLink = e[0].deepLink), f.$set(r);
                },
                i(e) {
                    m || (ce(r.$$.fragment, e), ce(a.$$.fragment, e), ce(p.$$.fragment, e), ce(f.$$.fragment, e), (m = !0));
                },
                o(e) {
                    le(r.$$.fragment, e), le(a.$$.fragment, e), le(p.$$.fragment, e), le(f.$$.fragment, e), (m = !1);
                },
                d(e) {
                    e && k(t), he(r), he(a), he(p), he(f);
                },
            }
        );
    }
    function en(e, t, n) {
        let { cart: r } = t;
        return (
            (e.$$set = (e) => {
                "cart" in e && n(0, (r = e.cart));
            }),
            [r, () => Dt.toggleWidget()]
        );
    }
    class tn extends Ce {
        constructor(e) {
            super(), $e(this, e, en, Kt, s, { cart: 0 }, Gt);
        }
    }
    const nn = Re();
    nn.set(window.innerWidth <= 568), window.addEventListener("resize", () => nn.set(window.innerWidth <= 568));
    const rn = Re(!0);
    function on(e) {
        w(
            e,
            "pe-1cefmwg",
            '@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap");*{box-sizing:border-box}html{font-size:1rem;font-family:"Inter", sans-serif}body{margin:0;display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:center;overflow:hidden;line-height:1.5rem;height:100%}'
        );
    }
    function an(e) {
        let t, n, r, i;
        const o = [cn, sn],
            a = [];
        function s(e, t) {
            return e[1] ? 0 : e[2].openWidget ? 1 : -1;
        }
        return (
            ~(t = s(e)) && (n = a[t] = o[t](e)),
            {
                c() {
                    n && n.c(), (r = H());
                },
                m(e, n) {
                    ~t && a[t].m(e, n), x(e, r, n), (i = !0);
                },
                p(e, i) {
                    let c = t;
                    (t = s(e)),
                        t === c
                            ? ~t && a[t].p(e, i)
                            : (n &&
                                (ae(),
                                    le(a[c], 1, 1, () => {
                                        a[c] = null;
                                    }),
                                    se()),
                                ~t ? ((n = a[t]), n ? n.p(e, i) : ((n = a[t] = o[t](e)), n.c()), ce(n, 1), n.m(r.parentNode, r)) : (n = null));
                },
                i(e) {
                    i || (ce(n), (i = !0));
                },
                o(e) {
                    le(n), (i = !1);
                },
                d(e) {
                    ~t && a[t].d(e), e && k(r);
                },
            }
        );
    }
    function sn(e) {
        let t, n, r, i, o;
        return (
            (n = new St({ props: { cart: e[0], step: e[4] } })),
            {
                c() {
                    (t = j("div")), fe(n.$$.fragment);
                },
                m(e, r) {
                    x(e, t, r), me(n, t, null), (o = !0);
                },
                p(e, t) {
                    const r = {};
                    1 & t && (r.cart = e[0]), 16 & t && (r.step = e[4]), n.$set(r);
                },
                i(e) {
                    o ||
                        (ce(n.$$.fragment, e),
                            X(() => {
                                i && i.end(1), (r = pe(t, Nt, { x: 50 })), r.start();
                            }),
                            (o = !0));
                },
                o(e) {
                    le(n.$$.fragment, e), r && r.invalidate(), (i = ue(t, Nt, { x: 50 })), (o = !1);
                },
                d(e) {
                    e && k(t), he(n), e && i && i.end();
                },
            }
        );
    }
    function cn(e) {
        let t,
            n,
            r = e[2].openWidget && e[3] && ln(e);
        return {
            c() {
                r && r.c(), (t = H());
            },
            m(e, i) {
                r && r.m(e, i), x(e, t, i), (n = !0);
            },
            p(e, n) {
                e[2].openWidget && e[3]
                    ? r
                        ? (r.p(e, n), 12 & n && ce(r, 1))
                        : ((r = ln(e)), r.c(), ce(r, 1), r.m(t.parentNode, t))
                    : r &&
                    (ae(),
                        le(r, 1, 1, () => {
                            r = null;
                        }),
                        se());
            },
            i(e) {
                n || (ce(r), (n = !0));
            },
            o(e) {
                le(r), (n = !1);
            },
            d(e) {
                r && r.d(e), e && k(t);
            },
        };
    }
    function ln(e) {
        let t, n, r, i, o;
        return (
            (n = new tn({ props: { cart: e[0] } })),
            {
                c() {
                    (t = j("div")), fe(n.$$.fragment);
                },
                m(e, r) {
                    x(e, t, r), me(n, t, null), (o = !0);
                },
                p(e, t) {
                    const r = {};
                    1 & t && (r.cart = e[0]), n.$set(r);
                },
                i(e) {
                    o ||
                        (ce(n.$$.fragment, e),
                            X(() => {
                                i && i.end(1), (r = pe(t, Nt, { y: 30 })), r.start();
                            }),
                            (o = !0));
                },
                o(e) {
                    le(n.$$.fragment, e), r && r.invalidate(), (i = ue(t, Nt, { y: 30 })), (o = !1);
                },
                d(e) {
                    e && k(t), he(n), e && i && i.end();
                },
            }
        );
    }
    function dn(e) {
        let t,
            n,
            r = e[0] && an(e);
        return {
            c() {
                r && r.c(), (t = H());
            },
            m(e, i) {
                r && r.m(e, i), x(e, t, i), (n = !0);
            },
            p(e, [n]) {
                e[0]
                    ? r
                        ? (r.p(e, n), 1 & n && ce(r, 1))
                        : ((r = an(e)), r.c(), ce(r, 1), r.m(t.parentNode, t))
                    : r &&
                    (ae(),
                        le(r, 1, 1, () => {
                            r = null;
                        }),
                        se());
            },
            i(e) {
                n || (ce(r), (n = !0));
            },
            o(e) {
                le(r), (n = !1);
            },
            d(e) {
                r && r.d(e), e && k(t);
            },
        };
    }
    function pn(e, t, n) {
        let r, i, o, a;
        d(e, nn, (e) => n(1, (r = e))), d(e, _t, (e) => n(2, (i = e))), d(e, rn, (e) => n(3, (o = e))), d(e, Ue, (e) => n(4, (a = e)));
        let s,
            c = !0;
        return (
            Fe.subscribe((e) => {
                null !== e ? (Ue.set(Ie.qr), e.cart.open && Ue.set(Ie.app), n(0, (s = e)), (c = !0), rn.set(!0)) : n(0, (s = null));
            }),
            setInterval(async function () {
                if (!s || !c) return;
                let e = await ((t = s.cart.id), Se.get(`${Ne}/widget/status?cartId=${t}`));
                var t;
                rn.set(!0),
                    e.status && rn.set(!1),
                    e.open && Ue.set(Ie.app),
                    "ORDER_CREATED" === e.status && Ue.set(Ie.progress),
                    "SUCCESS" === e.status && (Ue.set(Ie.success), setTimeout(() => (e.checkoutUrl ? (window.location.href = e.checkoutUrl) : void 0), 3e3), (c = !1)),
                    "REJECTED" === e.status && (Ue.set(Ie.rejected), (c = !1));
            }, 5e3),
            [s, r, i, o, a]
        );
    }
    class un extends Ce {
        constructor(e) {
            super(), $e(this, e, pn, dn, s, {}, on);
        }
    }
    function fn(e) {
        w(
            e,
            "pe-1itakhx",
            ".payeye-launcher.pe-1itakhx.pe-1itakhx{position:relative;display:flex;align-items:center;justify-content:center;width:60px;height:60px;background-color:#ffffff;box-shadow:0 0 20px 0 rgba(0, 0, 0, 0.2509803922);border-radius:100%;cursor:pointer}.payeye-launcher.pe-1itakhx:hover .payeye-launcher__icon.pe-1itakhx{transform:scale(1.18) rotate(14deg)}.payeye-launcher__count.pe-1itakhx.pe-1itakhx{position:absolute;top:-4px;right:-4px;background-color:#00AD93;width:20px;height:20px;border-radius:100%;display:flex;align-items:center;justify-content:center;font-size:12px;line-height:1;color:#ffffff}.payeye-launcher__icon.pe-1itakhx.pe-1itakhx{transition:transform ease-in-out 0.333s}"
        );
    }
    function mn(e) {
        let n,
            r,
            i,
            o,
            a,
            s,
            c,
            l = (e[0] ?? 0) + "";
        return {
            c() {
                (n = j("div")),
                    (r = L("svg")),
                    (i = L("path")),
                    (o = L("path")),
                    (a = D()),
                    (s = j("div")),
                    (c = _(l)),
                    E(i, "fill-rule", "evenodd"),
                    E(i, "clip-rule", "evenodd"),
                    E(
                        i,
                        "d",
                        "M26.6637 16.7446C25.0366 13.6864 20.5922 12.0903 16.8278 14.0911C13.0639 16.0915 11.4797 20.4715 13.2892 23.874C15.0992 27.2765 19.6179 28.413 23.3822 26.4126C27.1462 24.4122 28.2902 19.8029 26.6637 16.7446Z"
                    ),
                    E(i, "fill", "#00AD93"),
                    E(o, "fill-rule", "evenodd"),
                    E(o, "clip-rule", "evenodd"),
                    E(
                        o,
                        "d",
                        "M25.7786 7.60863C28.6796 8.27941 29.9562 4.63831 27.1891 2.84824C24.4225 1.05774 17.4337 -1.86973 7.79405 1.65825C3.81711 3.11381 1.9864 4.81241 0.936484 6.26089C-0.113427 7.70938 -0.378998 9.86223 0.649703 11.8145C1.67885 13.7671 5.87407 15.5687 9.737 12.1658C16.8933 5.86143 22.8772 6.93786 25.7786 7.60863Z"
                    ),
                    E(o, "fill", "#272445"),
                    E(r, "class", "payeye-launcher__icon pe-1itakhx"),
                    E(r, "width", "29"),
                    E(r, "height", "28"),
                    E(r, "viewBox", "0 0 29 28"),
                    E(r, "fill", "none"),
                    E(r, "xmlns", "http://www.w3.org/2000/svg"),
                    E(s, "class", "payeye-launcher__count pe-1itakhx"),
                    E(n, "class", "payeye-launcher pe-1itakhx"),
                    E(n, "role", "button");
            },
            m(e, t) {
                x(e, n, t), C(n, r), C(r, i), C(r, o), C(n, a), C(n, s), C(s, c);
            },
            p(e, [t]) {
                1 & t && l !== (l = (e[0] ?? 0) + "") && P(c, l);
            },
            i: t,
            o: t,
            d(e) {
                e && k(n);
            },
        };
    }
    function hn(e, t, n) {
        let { count: r } = t;
        return (
            (e.$$set = (e) => {
                "count" in e && n(0, (r = e.count));
            }),
            [r]
        );
    }
    class gn extends Ce {
        constructor(e) {
            super(), $e(this, e, hn, mn, s, { count: 0 }, fn);
        }
    }
    function $n(e) {
        $.ajax({ url: `${e}/widget`, success: (e) => Fe.set(e), error: () => Fe.set(null) });
    }
    function Cn(e) {
        w(
            e,
            "pe-1vxcj2o",
            ".payeye-payments-widget.pe-1vxcj2o{position:fixed;z-index:99998;width:355px;height:280px;bottom:20px;left:50%;filter:drop-shadow(0px 4px 20px rgba(0, 0, 0, 0.15));pointer-events:none;transform:translateX(-50%)}.payeye-payments-widget.active.pe-1vxcj2o{pointer-events:all}.payeye-payments-widget.active-force.pe-1vxcj2o{pointer-events:none}iframe.pe-1vxcj2o{position:absolute;width:100%;height:100%;border:0;margin:0}.payeye-payments-launcher.pe-1vxcj2o{position:fixed;z-index:99998;bottom:20px;right:20px}@media(min-width: 576px){.payeye-payments-widget.pe-1vxcj2o{left:unset;right:20px;transform:translateX(0)}.payeye-payments-widget.active-force.pe-1vxcj2o{pointer-events:none}.payeye-payments-launcher.pe-1vxcj2o{right:20px}}"
        );
    }
    function wn(e) {
        let t, n, r, i, a, s, c, l;
        return (
            (r = new gn({ props: { count: e[1]?.cart?.count } })),
            {
                c() {
                    (t = j("div")), (n = j("div")), fe(r.$$.fragment), E(t, "class", "payeye-payments-launcher pe-1vxcj2o"), E(t, "role", "button"), z(t, "bottom", e[3]);
                },
                m(i, o) {
                    x(i, t, o), C(t, n), me(r, n, null), (s = !0), c || ((l = [M(t, "keydown", e[5]), M(t, "click", e[7])]), (c = !0));
                },
                p(e, t) {
                    const n = {};
                    2 & t && (n.count = e[1]?.cart?.count), r.$set(n);
                },
                i(e) {
                    s ||
                        (ce(r.$$.fragment, e),
                            X(() => {
                                a && a.end(1), (i = pe(n, Nt, { y: 10 })), i.start();
                            }),
                            (s = !0));
                },
                o(e) {
                    le(r.$$.fragment, e), i && i.invalidate(), (a = ue(n, Nt, { y: 10 })), (s = !1);
                },
                d(e) {
                    e && k(t), he(r), e && a && a.end(), (c = !1), o(l);
                },
            }
        );
    }
    function vn(e) {
        let t,
            n,
            r =
                !1 === Be &&
                (function (e) {
                    let t,
                        n,
                        r,
                        i,
                        o,
                        a,
                        s,
                        c = e[1] && e[2].launcher && wn(e);
                    return {
                        c() {
                            (t = j("div")),
                                (n = j("iframe")),
                                (r = D()),
                                c && c.c(),
                                (i = H()),
                                E(n, "id", "payeye-iframe"),
                                E(n, "title", "PayEye Widget"),
                                E(n, "class", "pe-1vxcj2o"),
                                E(t, "class", "payeye-payments-widget pe-1vxcj2o"),
                                z(t, "bottom", e[3]),
                                A(t, "active", e[1]?.cart?.qr),
                                A(t, "active-force", !e[2].openWidget);
                        },
                        m(l, d) {
                            x(l, t, d), C(t, n), e[6](n), x(l, r, d), c && c.m(l, d), x(l, i, d), (o = !0), a || ((s = M(n, "load", e[4])), (a = !0));
                        },
                        p(e, n) {
                            (!o || 2 & n) && A(t, "active", e[1]?.cart?.qr),
                                (!o || 4 & n) && A(t, "active-force", !e[2].openWidget),
                                e[1] && e[2].launcher
                                    ? c
                                        ? (c.p(e, n), 6 & n && ce(c, 1))
                                        : ((c = wn(e)), c.c(), ce(c, 1), c.m(i.parentNode, i))
                                    : c &&
                                    (ae(),
                                        le(c, 1, 1, () => {
                                            c = null;
                                        }),
                                        se());
                        },
                        i(e) {
                            o || (ce(c), (o = !0));
                        },
                        o(e) {
                            le(c), (o = !1);
                        },
                        d(n) {
                            n && k(t), e[6](null), n && k(r), c && c.d(n), n && k(i), (a = !1), s();
                        },
                    };
                })(e);
        return {
            c() {
                r && r.c(), (t = H());
            },
            m(e, i) {
                r && r.m(e, i), x(e, t, i), (n = !0);
            },
            p(e, [t]) {
                !1 === Be && r.p(e, t);
            },
            i(e) {
                n || (ce(r), (n = !0));
            },
            o(e) {
                le(r), (n = !1);
            },
            d(e) {
                r && r.d(e), e && k(t);
            },
        };
    }
    function bn(e, t, n) {
        let r, i;
        var o, a, s;
        let c;
        d(e, Fe, (e) => n(1, (r = e))), d(e, _t, (e) => n(2, (i = e)));
        let l = !1;
        (xe.language = document.documentElement.lang), xe.initData();
        const p = null !== (s = null === (a = null === (o = yn().ui) || void 0 === o ? void 0 : o.position) || void 0 === a ? void 0 : a.bottom) && void 0 !== s ? s : "20px";
        switch (yn().platform) {
            case "WOOCOMMERCE":
                !(function (e) {
                    !(function (t) {
                        t(document.body)
                            .on("added_to_cart removed_from_cart updated_cart_totals wc_fragments_refreshed", () => {
                                t.ajax({ url: e.ajaxUrl, data: { action: e.ajaxActions.renderWidget }, success: (e) => Fe.set(e) });
                            })
                            .trigger("updated_cart_totals"),
                            t(document.body).on("added_to_cart removed_from_cart updated_cart_totals wc_cart_emptied", (n) => {
                                t.ajax({ url: e.ajaxUrl, data: { action: e.ajaxActions.refreshCart, eventType: n.type } });
                            });
                    })(jQuery);
                })(yn());
                break;
            case "PRESTASHOP":
                !(function (e) {
                    "undefined" != typeof prestashop && ($n(e.apiUrl), prestashop.on("updateCart", () => $n(e.apiUrl)));
                })(yn());
        }
        function u() {
            l || ((l = !0), !1 === Be && new un({ target: c.contentDocument.body }));
        }
        T(() => (navigator.userAgent.match("Firefox") ? void 0 : u()));
        return [
            c,
            r,
            i,
            p,
            u,
            function (t) {
                O.call(this, e, t);
            },
            function (e) {
                F[e ? "unshift" : "push"](() => {
                    (c = e), n(0, c);
                });
            },
            () => Dt.toggleWidget(),
        ];
    }
    function yn() {
        return window.payeye;
    }
    return (
        new (class extends Ce {
            constructor(e) {
                super(), $e(this, e, bn, vn, s, {}, Cn);
            }
        })({ target: document.body }),
        (e.payeyeObject = yn),
        e
    );
})({});



// -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// prestashop - 0.0.32 > changes
// -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------



document.addEventListener("DOMContentLoaded", function () {

    var visibleWidget = window.payeye.ui.widget.visible;

    if (!visibleWidget) {
        function hidePayeyeLauncher() {
            var target = document.querySelector('.payeye-payments-launcher');
            if (target) {
                target.style.display = 'none';
                observer.disconnect();
            }
        }
        function hidePayeyeWidget() {
            var target2 = document.querySelector('.payeye-payments-widget');
            if (target2) {
                target2.style.display = 'none';
                observer2.disconnect();
            }
        }

        var observer = new MutationObserver(hidePayeyeLauncher);
        var observer2 = new MutationObserver(hidePayeyeWidget);

        observer.observe(document, { childList: true, subtree: true });
        observer2.observe(document, { childList: true, subtree: true });



        function setLocalStorageValue() {
            localStorage.payeye = JSON.stringify({ "launcher": true, "openWidget": false, "version": 1.2 });
        }
        window.addEventListener("beforeunload", setLocalStorageValue);
        window.addEventListener("unload", setLocalStorageValue);

        var runWidgetBTN = document.getElementById("payeye-run-widget");

        if (runWidgetBTN) {

            runWidgetBTN.style.display = 'none';
            runWidgetBTN.innerHTML = 'Zapłać z <img src="/modules/payeye/images/payeye-white.svg" alt="payeye-white"></img>';
            runWidgetBTN.style.background = '#00AD93';
            runWidgetBTN.style.border = 'none';



            (function () {
                var open = window.XMLHttpRequest.prototype.open;
                window.XMLHttpRequest.prototype.open = function () {
                    this.addEventListener('load', function () {
                        setTimeout(() => {
                            var launcher = document.querySelector('.payeye-payments-launcher');
                            var btn = document.getElementById("payeye-run-widget");
                            var iframe = document.querySelector('.payeye-payments-widget #payeye-iframe');
                            var iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
                            var divElement = iframeDocument.body.querySelector('.content-onclick');
                            var mobileElement = iframeDocument.body.querySelector('.widget.pe-13jr15b');
                            if (launcher || divElement || mobileElement) {
                                btn.style.display = 'flex';
                            } else {
                                btn.style.display = 'none';
                            }
                        }, 1000)
                    });
                    open.apply(this, arguments);
                };
            })();

            runWidgetBTN.style.width = '100%';
            runWidgetBTN.style.padding = '10px 0';
            runWidgetBTN.style.color = 'white';
            runWidgetBTN.style.justifyContent = 'center';
            runWidgetBTN.style.alignItems = 'center';
            runWidgetBTN.style.gap = '0px 10px';
            runWidgetBTN.style.cursor = 'pointer';
            runWidgetBTN.style.visibility = 'initial';
            runWidgetBTN.style.transition = 'opacity 0.1s linear';

            runWidgetBTN.addEventListener("focus", function () {
                runWidgetBTN.style.outline = 'none';
            });
            runWidgetBTN.addEventListener("mouseover", function () {
                runWidgetBTN.style.opacity = '90%';
            });
            runWidgetBTN.addEventListener("mouseout", function () {
                runWidgetBTN.style.opacity = '100%';
            });

        }

        setTimeout(function () {

            if(runWidgetBTN){
                runWidgetBTN.addEventListener("click", handleFunc);
            }


            function handleFunc() {
                var launcher = document.querySelector('.payeye-payments-launcher');
                var widget = document.querySelector(".payeye-payments-widget");

                var iframe = document.querySelector('#payeye-iframe');
                var iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
                var styleElement = iframeDocument.createElement('style');
                styleElement.textContent = `
                        .svg.pe-tb6sbh svg {
                            width: 100px !important;
                            height: auto !important;
                        }
                        .content-grid.pe-tb6sbh .title.pe-tb6sbh {
                            font-size: 16px;
                            max-width: 250px;
                            padding: 20px;
                            line-height: 1.4;
                        }
                        .content-grid.pe-tb6sbh,
                        .content-grid.pe-j509wd{
                            max-width: 560px !important;
                            min-height: 450px;
                            padding-top: 50px;
                            padding-bottom: 30px;
                        }
                        .content-grid.pe-j509wd{
                            display: flex !important;
                            flex-direction: column;
                        }
                        .content-grid.pe-tb6sbh::before,
                        .content-grid.pe-j509wd::before {
                            content: '';
                            left: 0;
                            right: 0;
                            height: 27px;
                            width: 160px;
                            background: url(/modules/payeye/images/payeye-color.png);
                            background-size: cover;
                            background-position: center;
                            margin: 0 auto 40px;
                        }
                        .rescan.pe-tb6sbh {
                            margin: 0 auto !important;
                        }
                        .container.pe-1mgh88v {
                            min-width: 300px;
                        }
                        .content.pe-ro0j7q {
                            width: 100px !important;
                            height: 100px !important;
                        }
                        .text.pe-j509wd{
                            font-size: 16px;
                        }
                        .space.pe-j509wd {
                            margin-top: 50px;
                        }
    
                        img.pe-1di034j {
                            width: 200px !important;
                            height: 200px !important;
                        }
                        .heading.pe-1di034j {
                            font-size: 14px !important;
                        }
                        .price.pe-1mvdr6l {
                            font-size: 24px !important;
                        }
                        .heading.pe-1mvdr6l {
                            font-size: 16px !important;
                        }
                        .basket-wrapper svg {
                            width: 90px;
                            height: 90px;
                            margin-bottom: 30px;
                        }
                        .content-grid.pe-1os1qqp {
                            padding: 40px 0;
                        }
                    `;
                iframeDocument.head.appendChild(styleElement);



                widget.style.left = '0';
                widget.style.right = '0';
                widget.style.bottom = '0';
                widget.style.top = '0';
                widget.style.background = 'rgba(0,0,0,0.6)';
                widget.style.width = '100%';
                widget.style.height = '100%';
                widget.style.transform = 'initial';

                if (launcher) {
                    launcher.click();
                }

                var widget = document.querySelector(".payeye-payments-widget");
                setTimeout(function () {
                    var iframe = document.querySelector('#payeye-iframe');
                    var iframeDocument = iframe.contentDocument || iframe.contentWindow.document;
                    var widgetToggle = iframeDocument.querySelector(".toggle.pe-1mgh88v");
                    var widgetToggleMobile = iframeDocument.querySelector(".toggle.pe-13jr15b");
                    var body = iframeDocument.body;
                    var cardContent = body.querySelector('.widget');

                    body.style.justifyContent = 'center';
                    body.style.alignItems = 'center';
                    body.style.width = '90%';
                    body.style.maxWidth = '840px';
                    body.style.margin = '0 auto';

                    var closeBTN = document.createElement("img");
                    closeBTN.setAttribute("src", "/modules/payeye/images/close_btn.svg");
                    closeBTN.setAttribute("alt", "close-btn");
                    closeBTN.style.position = 'absolute';
                    closeBTN.style.right = '15px';
                    closeBTN.style.top = '15px';
                    closeBTN.style.cursor = 'pointer';
                    closeBTN.style.width = '30px';
                    closeBTN.style.height = '30px';
                    closeBTN.style.opacity = '20%';
                    closeBTN.style.transition = '0.15s linear';
                    closeBTN.addEventListener("click", function () {
                        widget.style.display = 'none';
                    });
                    closeBTN.addEventListener("mouseover", function () {
                        closeBTN.style.opacity = '60%';
                        closeBTN.style.transform = 'scale(1.05)';
                    });
                    closeBTN.addEventListener("mouseout", function () {
                        closeBTN.style.opacity = '20%';
                        closeBTN.style.transform = 'scale(1)';
                    });

                    var isIMG = cardContent.querySelector("img[alt='close-btn']");
                    if (!isIMG) {
                        cardContent.appendChild(closeBTN);
                    }


                    var mobileWidget = iframeDocument.querySelector('.pe-13jr15b');
                    if (!mobileWidget) {
                        var qrElement = iframeDocument.querySelector('.qr');
                        if (qrElement) {
                            qrElement.style.padding = '10px 0 30px 0';
                            var qrIMG = qrElement.querySelector('img');
                            if (qrIMG) {
                                qrIMG.style.width = '200px';
                                qrIMG.style.height = '200px';
                            }
                            var qrHEADING = qrElement.querySelector('.heading');
                            if (qrHEADING) {
                                qrHEADING.style.fontSize = '16px';
                            }
                        }
                        var basketHEADING = iframeDocument.querySelector('.heading.pe-1mvdr6l');
                        if (basketHEADING) {
                            basketHEADING.style.fontSize = '16px';
                            basketHEADING.style.paddingBottom = '10px';
                        }
                        var priceCart = iframeDocument.querySelector('.price');
                        if (priceCart) {
                            priceCart.style.fontSize = '32px';
                        }
                        var clickElement = iframeDocument.querySelector('.content-grid .click');
                        var logoElement = iframeDocument.querySelector('.logo');
                        if (clickElement && qrElement && logoElement) {
                            clickElement.insertBefore(qrElement, clickElement.firstChild);
                            clickElement.insertBefore(logoElement, clickElement.firstChild);
                        }


                        var newElement = document.createElement('div');
                        if (newElement) {
                            newElement.classList.add('content-onclick');
                            newElement.innerHTML = '<p class="title">Przekonaj się, jak szybki i wygodny może być proces płatności.</p><p class="subtitle">Płacąc z PayEye nie musisz każdorazowo zakładać konta w sklepie internetowym, logować się czy wypełniać danych dostawy. Wystarczy jedno kliknięcie!</p><ul><div class="flex"><li>Pobierz aplikację PayEye i utwórz konto</li></div><div class="flex"><li>Uzupełnij swoje dane adresowe i podepnij kartę płatniczą</li></div><div class="flex"><li>Zeskanuj QR kod i zapłać jednym klinięciem</li></div></ul><p class="download">Pobierz aplikację</p><div class="buttons" style="display: flex; gap: 0px 10px;"><a href="https://apps.apple.com/pl/app/payeye/id1628561744" target="_blank"><img src="/modules/payeye/images/appstore.svg"></a><a href="https://play.google.com/store/apps/details?id=com.payeye.passwallet" target="_blank"><img src="/modules/payeye/images/google-play.svg"></a><a href="https://appgallery.huawei.com/app/C106413423" target="_blank"><img src="/modules/payeye/images/app-gallery.svg"></a></div>';
                            newElement.style.textAlign = 'left';
                        }
                        var buttons = newElement.querySelector('.buttons');
                        if (buttons) {
                            buttons.style.display = 'flex';
                            buttons.style.gap = '0 10px';
                        }
                        var contentGridElement = iframeDocument.querySelector('.content-grid');
                        if (contentGridElement) {
                            contentGridElement.style.padding = '40px 0';
                        }


                        var basketWrapper = iframeDocument.querySelector('.basket-wrapper');
                        var contentOnClickElement = contentGridElement.querySelector('.content-onclick');
                        var stepTwo = iframeDocument.querySelector('.icon.pe-tb6sbh');
                        var lastChild = contentGridElement.lastElementChild;

                        if (!contentOnClickElement && newElement && !stepTwo) {
                            if (lastChild.classList.contains('qr')) {
                                console.log('Ostatni element to div o klasie .qr');
                            } else {
                                // console.log('Ostatni element nie jest divem o klasie .qr');
                                contentGridElement.appendChild(newElement);
                                basketWrapper.remove();
                            }
                        }

                        if (logoElement) {
                            var svg = logoElement.querySelector('svg');
                            if (svg) {
                                svg.setAttribute('width', '200');
                                svg.setAttribute('height', '36');
                            }
                            var svgBefore = iframeDocument.querySelector('.content-grid.pe-tb6sbh');
                            if(svgBefore){
                                var before = window.getComputedStyle(svgBefore, '::before');
                                if (before.content !== 'none') {
                                    svg.style.display = 'none';
                                } else {
                                    svg.style.display = 'block';
                                }
                            }
                        }

                        var title = newElement.querySelector('.title');
                        if (title) {
                            title.style.fontSize = '20px';
                            title.style.fontWeight = 'bold';
                            title.style.color = '#272445';
                        }
                        var subtitle = newElement.querySelector('.subtitle');
                        if (subtitle) {
                            subtitle.style.fontSize = '16px';
                            subtitle.style.color = 'rgba(39,36,69,0.75)';
                        }
                        var ul = newElement.querySelector('ul');
                        if (ul) {
                            ul.style.listStyle = 'none';
                            ul.style.paddingLeft = '0px';
                            ul.style.display = 'flex';
                            ul.style.flexDirection = 'column';
                            ul.style.gap = '15px 0';
                        }
                        var flex = newElement.querySelectorAll('.flex');
                        if (flex) {
                            flex.forEach(f => {
                                f.style.display = 'flex';
                                f.style.alignItems = 'center';
                                f.style.gap = '0 10px';
                            })
                        }
                        var li = newElement.querySelectorAll('li');
                        if (li) {
                            li.forEach(i => {
                                i.style.fontSize = '16px';
                                i.style.color = 'rgba(39,36,69,0.75)';
                                i.style.lineHeight = '1.3';
                                var icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"> <g clip-path="url(#clip0_571_1948)"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12 0.875C5.57967 0.875 0.375 6.07967 0.375 12.5C0.375 18.9203 5.57967 24.125 12 24.125C18.4203 24.125 23.625 18.9203 23.625 12.5C23.625 6.07967 18.4203 0.875 12 0.875ZM12 3.125C17.1812 3.125 21.375 7.31802 21.375 12.5C21.375 17.6812 17.182 21.875 12 21.875C6.81881 21.875 2.625 17.682 2.625 12.5C2.625 7.31881 6.81802 3.125 12 3.125ZM17.5155 8.16636L18.5719 9.23126C18.7907 9.45181 18.7893 9.80797 18.5688 10.0268L10.4786 18.052C10.2581 18.2708 9.90194 18.2693 9.68318 18.0488L5.42782 13.759C5.20901 13.5384 5.21046 13.1822 5.43101 12.9635L6.49596 11.9071C6.71651 11.6883 7.07266 11.6898 7.29143 11.9103L10.0942 14.7358L16.7201 8.16317C16.9406 7.94436 17.2968 7.94581 17.5155 8.16636Z" fill="#00AD93"/> </g> <defs> <clipPath id="clip0_571_1948"> <rect width="24" height="24" fill="white"/> </clipPath> </defs> </svg>';
                                i.insertAdjacentHTML("beforebegin", icon);
                            })
                        }
                        var download = newElement.querySelector('.download');
                        if (download) {
                            download.style.fontSize = '16px';
                            download.style.color = '#272445';
                            download.style.fontWeight = '500';
                        }
                        var svg = newElement.querySelectorAll('svg');
                        if (svg) {
                            svg.forEach(s => {
                                s.style.minWidth = '24px';
                            });
                        }

                    } else {
                        closeBTN.style.width = '20px';
                        closeBTN.style.height = '20px';
                        mobileWidget.style.paddingTop = '50px';
                    }


                    if (widgetToggle) {
                        widgetToggle.style.display = 'none';
                    }
                    if (widget.style.display == 'none') {
                        widget.style.display = 'initial';
                    } else if (widget.style.display == 'initial') {
                        widget.style.display = 'none';
                    }
                    if (widgetToggleMobile) {
                        widgetToggleMobile.style.display = 'none';
                    }
                }, 1);
            }
        }, 1500);
    } else {

        var runWidgetBTN = document.getElementById("payeye-run-widget");
        if (runWidgetBTN) {
            runWidgetBTN.style.display = 'none';
        }
    }

});
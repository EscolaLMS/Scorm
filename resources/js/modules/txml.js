var $jscomp = $jscomp || {};
$jscomp.scope = {};
$jscomp.arrayIteratorImpl = function (h) {
  var p = 0;
  return function () {
    return p < h.length ? { done: !1, value: h[p++] } : { done: !0 };
  };
};
$jscomp.arrayIterator = function (h) {
  return { next: $jscomp.arrayIteratorImpl(h) };
};
$jscomp.makeIterator = function (h) {
  var p = "undefined" != typeof Symbol && Symbol.iterator && h[Symbol.iterator];
  return p ? p.call(h) : $jscomp.arrayIterator(h);
};
$jscomp.arrayFromIterator = function (h) {
  for (var p, t = []; !(p = h.next()).done; ) t.push(p.value);
  return t;
};
$jscomp.arrayFromIterable = function (h) {
  return h instanceof Array
    ? h
    : $jscomp.arrayFromIterator($jscomp.makeIterator(h));
};
var txml = function () {
  function h(b, c) {
    function d(k) {
      for (var n = []; b[a]; )
        if (60 == b.charCodeAt(a)) {
          if (47 === b.charCodeAt(a + 1)) {
            var f = a + 2;
            a = b.indexOf(">", a);
            if (-1 == b.substring(f, a).indexOf(k))
              throw (
                ((k = b.substring(0, a).split("\n")),
                Error(
                  "Unexpected close tag\nLine: " +
                    (k.length - 1) +
                    "\nColumn: " +
                    (k[k.length - 1].length + 1) +
                    "\nChar: " +
                    b[a]
                ))
              );
            a + 1 && (a += 1);
            break;
          } else if (33 === b.charCodeAt(a + 1)) {
            if (45 == b.charCodeAt(a + 2)) {
              for (
                f = a;
                -1 !== a &&
                (62 !== b.charCodeAt(a) ||
                  45 != b.charCodeAt(a - 1) ||
                  45 != b.charCodeAt(a - 2) ||
                  -1 == a);

              )
                a = b.indexOf(">", a + 1);
              -1 === a && (a = b.length);
              u && n.push(b.substring(f, a + 1));
            } else if (
              91 === b.charCodeAt(a + 2) &&
              91 === b.charCodeAt(a + 8) &&
              "cdata" === b.substr(a + 3, 5).toLowerCase()
            ) {
              f = b.indexOf("]]\x3e", a);
              -1 == f
                ? (n.push(b.substr(a + 9)), (a = b.length))
                : (n.push(b.substring(a + 9, f)), (a = f + 3));
              continue;
            } else {
              f = a + 1;
              a += 2;
              for (var q = !1; (62 !== b.charCodeAt(a) || !0 === q) && b[a]; )
                91 === b.charCodeAt(a)
                  ? (q = !0)
                  : !0 === q && 93 === b.charCodeAt(a) && (q = !1),
                  a++;
              n.push(b.substring(f, a));
            }
            a++;
            continue;
          }
          f = g();
          n.push(f);
          "?" === f.tagName[0] &&
            (n.push.apply(n, $jscomp.arrayFromIterable(f.children)),
            (f.children = []));
        } else
          (f = a),
            (a = b.indexOf("<", a) - 1),
            -2 === a && (a = b.length),
            (f = b.slice(f, a + 1)),
            (x || 0 < f.trim().length) && n.push(f),
            a++;
      return n;
    }
    function e() {
      for (var k = a; -1 === y.indexOf(b[a]) && b[a]; ) a++;
      return b.slice(k, a);
    }
    function g() {
      a++;
      for (var k = e(), n = {}, f = []; 62 !== b.charCodeAt(a) && b[a]; ) {
        var q = b.charCodeAt(a);
        if ((64 < q && 91 > q) || (96 < q && 123 > q)) {
          q = e();
          for (
            var l = b.charCodeAt(a);
            l &&
            39 !== l &&
            34 !== l &&
            !((64 < l && 91 > l) || (96 < l && 123 > l)) &&
            62 !== l;

          )
            a++, (l = b.charCodeAt(a));
          if (39 === l || 34 === l) {
            if (
              ((l = a + 1),
              (a = b.indexOf(b[a], l)),
              (l = b.slice(l, a)),
              -1 === a)
            )
              return { tagName: k, attributes: n, children: f };
          } else (l = null), a--;
          n[q] = l;
        }
        a++;
      }
      47 !== b.charCodeAt(a - 1)
        ? "script" == k
          ? ((f = a + 1),
            (a = b.indexOf("\x3c/script>", a)),
            (f = [b.slice(f, a)]),
            (a += 9))
          : "style" == k
          ? ((f = a + 1),
            (a = b.indexOf("</style>", a)),
            (f = [b.slice(f, a)]),
            (a += 8))
          : -1 === z.indexOf(k)
          ? (a++, (f = d(k)))
          : a++
        : a++;
      return { tagName: k, attributes: n, children: f };
    }
    function m() {
      var k = new RegExp(
        "\\s" + c.attrName + "\\s*=['\"]" + c.attrValue + "['\"]"
      ).exec(b);
      return k ? k.index : -1;
    }
    c = c || {};
    var a = c.pos || 0,
      u = !!c.keepComments,
      x = !!c.keepWhitespace,
      y = "\r\n\t>/= ",
      z = c.noChildNodes || "img br input meta link hr".split(" "),
      r = null;
    if (void 0 !== c.attrValue)
      for (c.attrName = c.attrName || "id", r = []; -1 !== (a = m()); )
        (a = b.lastIndexOf("<", a)),
          -1 !== a && r.push(g()),
          (b = b.substr(a)),
          (a = 0);
    else r = c.parseNode ? g() : d("");
    c.filter && (r = v(r, c.filter));
    if (c.simplify) return p(Array.isArray(r) ? r : [r]);
    c.setPos && (r.pos = a);
    return r;
  }
  function p(b) {
    var c = {};
    if (!b.length) return "";
    if (1 === b.length && "string" == typeof b[0]) return b[0];
    b.forEach(function (e) {
      if ("object" === typeof e) {
        c[e.tagName] || (c[e.tagName] = []);
        var g = p(e.children);
        c[e.tagName].push(g);
        Object.keys(e.attributes).length && (g._attributes = e.attributes);
      }
    });
    for (var d in c) 1 == c[d].length && (c[d] = c[d][0]);
    return c;
  }
  function t(b, c) {
    c = void 0 === c ? {} : c;
    var d = {};
    if (!b.length) return d;
    if (1 === b.length && "string" == typeof b[0])
      return Object.keys(c).length ? { _attributes: c, value: b[0] } : b[0];
    b.forEach(function (e) {
      if ("object" === typeof e) {
        d[e.tagName] || (d[e.tagName] = []);
        var g = t(e.children || [], e.attributes);
        d[e.tagName].push(g);
        Object.keys(e.attributes).length && (g._attributes = e.attributes);
      }
    });
    return d;
  }
  function v(b, c, d, e) {
    d = void 0 === d ? 0 : d;
    e = void 0 === e ? "" : e;
    var g = [];
    b.forEach(function (m, a) {
      "object" === typeof m && c(m, a, d, e) && g.push(m);
      if (m.children) {
        var u = v(
          m.children,
          c,
          d + 1,
          (e ? e + "." : "") + a + "." + m.tagName
        );
        g = g.concat(u);
      }
    });
    return g;
  }
  function w(b) {
    if (Array.isArray(b)) {
      var c = "";
      b.forEach(function (d) {
        c += " " + w(d);
        c = c.trim();
      });
      return c;
    }
    return "object" === typeof b ? w(b.children) : " " + b;
  }
  return {
    parse: h,
    simplify: p,
    simplifyLostLess: t,
    filter: v,
    stringify: function (b) {
      function c(e) {
        if (e)
          for (var g = 0; g < e.length; g++)
            if ("string" == typeof e[g]) d += e[g].trim();
            else {
              var m = void 0,
                a = e[g];
              d += "<" + a.tagName;
              for (m in a.attributes)
                d =
                  null === a.attributes[m]
                    ? d + (" " + m)
                    : -1 === a.attributes[m].indexOf('"')
                    ? d + (" " + m + '="' + a.attributes[m].trim() + '"')
                    : d + (" " + m + "='" + a.attributes[m].trim() + "'");
              "?" === a.tagName[0]
                ? (d += "?>")
                : ((d += ">"), c(a.children), (d += "</" + a.tagName + ">"));
            }
      }
      var d = "";
      c(b);
      return d;
    },
    toContentString: w,
    getElementById: function (b, c, d) {
      b = h(b, { attrValue: c });
      return d ? tXml.simplify(b) : b[0];
    },
    getElementsByClassName: function (b, c, d) {
      b = h(b, {
        attrName: "class",
        attrValue: "[a-zA-Z0-9- ]*" + c + "[a-zA-Z0-9- ]*",
      });
      return d ? tXml.simplify(b) : b;
    },
  };
};

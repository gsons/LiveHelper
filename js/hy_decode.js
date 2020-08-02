function ANhw(e, t) {
  var n, o;
  n = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
    o = {
      rotl: function (e, t) {
        return e << t | e >>> 32 - t
      },
      rotr: function (e, t) {
        return e << 32 - t | e >>> t
      },
      endian: function (e) {
        if (e.constructor == Number)
          return 16711935 & o.rotl(e, 8) | 4278255360 & o.rotl(e, 24);
        for (var t = 0; t < e.length; t++)
          e[t] = o.endian(e[t]);
        return e
      },
      randomBytes: function (e) {
        for (var t = []; e > 0; e--)
          t.push(Math.floor(256 * Math.random()));
        return t
      },
      bytesToWords: function (e) {
        for (var t = [], n = 0, o = 0; n < e.length; n++,
          o += 8)
          t[o >>> 5] |= e[n] << 24 - o % 32;
        return t
      },
      wordsToBytes: function (e) {
        for (var t = [], n = 0; n < 32 * e.length; n += 8)
          t.push(e[n >>> 5] >>> 24 - n % 32 & 255);
        return t
      },
      bytesToHex: function (e) {
        for (var t = [], n = 0; n < e.length; n++)
          t.push((e[n] >>> 4).toString(16)),
            t.push((15 & e[n]).toString(16));
        return t.join("")
      },
      hexToBytes: function (e) {
        for (var t = [], n = 0; n < e.length; n += 2)
          t.push(parseInt(e.substr(n, 2), 16));
        return t
      },
      bytesToBase64: function (e) {
        for (var t = [], o = 0; o < e.length; o += 3)
          for (var i = e[o] << 16 | e[o + 1] << 8 | e[o + 2], a = 0; a < 4; a++)
            8 * o + 6 * a <= 8 * e.length ? t.push(n.charAt(i >>> 6 * (3 - a) & 63)) : t.push("=");
        return t.join("")
      },
      base64ToBytes: function (e) {
        e = e.replace(/[^A-Z0-9+\/]/gi, "");
        for (var t = [], o = 0, i = 0; o < e.length; i = ++o % 4)
          0 != i && t.push((n.indexOf(e.charAt(o - 1)) & Math.pow(2, -2 * i + 8) - 1) << 2 * i | n.indexOf(e.charAt(o)) >>> 6 - 2 * i);
        return t
      }
    }
  return o;
}

function mmNF(e, t) {
  var n = {
    utf8: {
      stringToBytes: function (e) {
        return n.bin.stringToBytes(unescape(encodeURIComponent(e)))
      },
      bytesToString: function (e) {
        return decodeURIComponent(escape(n.bin.bytesToString(e)))
      }
    },
    bin: {
      stringToBytes: function (e) {
        for (var t = [], n = 0; n < e.length; n++)
          t.push(255 & e.charCodeAt(n));
        return t
      },
      bytesToString: function (e) {
        for (var t = [], n = 0; n < e.length; n++)
          t.push(String.fromCharCode(e[n]));
        return t.join("")
      }
    }
  };
  return n;
};


function BEtg(e, t) {
  function n(e) {
    return !!e.constructor && "function" == typeof e.constructor.isBuffer && e.constructor.isBuffer(e)
  }
  /*!
* Determine if an object is a Buffer
*
* @author   Feross Aboukhadijeh <https://feross.org>
* @license  MIT
*/
  return function (e) {
    return null != e && (n(e) || function (e) {
      return "function" == typeof e.readFloatLE && "function" == typeof e.slice && n(e.slice(0, 0))
    }(e) || !!e._isBuffer)
  }
}



function go(e, t) {

  var o, i, a, r, s;
  o = ANhw(),
    i = mmNF().utf8,
    a = BEtg(),
    r = mmNF().bin,
    // console.log(o.randomBytes(1233));return;
    e.constructor == String ? e = t && "binary" === t.encoding ? r.stringToBytes(e) : i.stringToBytes(e) : a(e) ? e = Array.prototype.slice.call(e, 0) : Array.isArray(e) || (e = e.toString());
  for (var n = o.bytesToWords(e), u = 8 * e.length, l = 1732584193, c = -271733879, d = -1732584194, p = 271733878, f = 0; f < n.length; f++)
    n[f] = 16711935 & (n[f] << 8 | n[f] >>> 24) | 4278255360 & (n[f] << 24 | n[f] >>> 8);
  n[u >>> 5] |= 128 << u % 32,
    n[14 + (u + 64 >>> 9 << 4)] = u;
  var h = function (e, t, n, o, i, a, r) {
    var s = e + (t & n | ~t & o) + (i >>> 0) + r;
    return (s << a | s >>> 32 - a) + t
  }
    , m = function (e, t, n, o, i, a, r) {
      var s = e + (t & o | n & ~o) + (i >>> 0) + r;
      return (s << a | s >>> 32 - a) + t
    }
    , y = function (e, t, n, o, i, a, r) {
      var s = e + (t ^ n ^ o) + (i >>> 0) + r;
      return (s << a | s >>> 32 - a) + t
    }
    , g = function (e, t, n, o, i, a, r) {
      var s = e + (n ^ (t | ~o)) + (i >>> 0) + r;
      return (s << a | s >>> 32 - a) + t
    };
  for (f = 0; f < n.length; f += 16) {
    var v = l
      , _ = c
      , w = d
      , b = p;
    l = h(l, c, d, p, n[f + 0], 7, -680876936),
      p = h(p, l, c, d, n[f + 1], 12, -389564586),
      d = h(d, p, l, c, n[f + 2], 17, 606105819),
      c = h(c, d, p, l, n[f + 3], 22, -1044525330),
      l = h(l, c, d, p, n[f + 4], 7, -176418897),
      p = h(p, l, c, d, n[f + 5], 12, 1200080426),
      d = h(d, p, l, c, n[f + 6], 17, -1473231341),
      c = h(c, d, p, l, n[f + 7], 22, -45705983),
      l = h(l, c, d, p, n[f + 8], 7, 1770035416),
      p = h(p, l, c, d, n[f + 9], 12, -1958414417),
      d = h(d, p, l, c, n[f + 10], 17, -42063),
      c = h(c, d, p, l, n[f + 11], 22, -1990404162),
      l = h(l, c, d, p, n[f + 12], 7, 1804603682),
      p = h(p, l, c, d, n[f + 13], 12, -40341101),
      d = h(d, p, l, c, n[f + 14], 17, -1502002290),
      l = m(l, c = h(c, d, p, l, n[f + 15], 22, 1236535329), d, p, n[f + 1], 5, -165796510),
      p = m(p, l, c, d, n[f + 6], 9, -1069501632),
      d = m(d, p, l, c, n[f + 11], 14, 643717713),
      c = m(c, d, p, l, n[f + 0], 20, -373897302),
      l = m(l, c, d, p, n[f + 5], 5, -701558691),
      p = m(p, l, c, d, n[f + 10], 9, 38016083),
      d = m(d, p, l, c, n[f + 15], 14, -660478335),
      c = m(c, d, p, l, n[f + 4], 20, -405537848),
      l = m(l, c, d, p, n[f + 9], 5, 568446438),
      p = m(p, l, c, d, n[f + 14], 9, -1019803690),
      d = m(d, p, l, c, n[f + 3], 14, -187363961),
      c = m(c, d, p, l, n[f + 8], 20, 1163531501),
      l = m(l, c, d, p, n[f + 13], 5, -1444681467),
      p = m(p, l, c, d, n[f + 2], 9, -51403784),
      d = m(d, p, l, c, n[f + 7], 14, 1735328473),
      l = y(l, c = m(c, d, p, l, n[f + 12], 20, -1926607734), d, p, n[f + 5], 4, -378558),
      p = y(p, l, c, d, n[f + 8], 11, -2022574463),
      d = y(d, p, l, c, n[f + 11], 16, 1839030562),
      c = y(c, d, p, l, n[f + 14], 23, -35309556),
      l = y(l, c, d, p, n[f + 1], 4, -1530992060),
      p = y(p, l, c, d, n[f + 4], 11, 1272893353),
      d = y(d, p, l, c, n[f + 7], 16, -155497632),
      c = y(c, d, p, l, n[f + 10], 23, -1094730640),
      l = y(l, c, d, p, n[f + 13], 4, 681279174),
      p = y(p, l, c, d, n[f + 0], 11, -358537222),
      d = y(d, p, l, c, n[f + 3], 16, -722521979),
      c = y(c, d, p, l, n[f + 6], 23, 76029189),
      l = y(l, c, d, p, n[f + 9], 4, -640364487),
      p = y(p, l, c, d, n[f + 12], 11, -421815835),
      d = y(d, p, l, c, n[f + 15], 16, 530742520),
      l = g(l, c = y(c, d, p, l, n[f + 2], 23, -995338651), d, p, n[f + 0], 6, -198630844),
      p = g(p, l, c, d, n[f + 7], 10, 1126891415),
      d = g(d, p, l, c, n[f + 14], 15, -1416354905),
      c = g(c, d, p, l, n[f + 5], 21, -57434055),
      l = g(l, c, d, p, n[f + 12], 6, 1700485571),
      p = g(p, l, c, d, n[f + 3], 10, -1894986606),
      d = g(d, p, l, c, n[f + 10], 15, -1051523),
      c = g(c, d, p, l, n[f + 1], 21, -2054922799),
      l = g(l, c, d, p, n[f + 8], 6, 1873313359),
      p = g(p, l, c, d, n[f + 15], 10, -30611744),
      d = g(d, p, l, c, n[f + 6], 15, -1560198380),
      c = g(c, d, p, l, n[f + 13], 21, 1309151649),
      l = g(l, c, d, p, n[f + 4], 6, -145523070),
      p = g(p, l, c, d, n[f + 11], 10, -1120210379),
      d = g(d, p, l, c, n[f + 2], 15, 718787259),
      c = g(c, d, p, l, n[f + 9], 21, -343485551),
      l = l + v >>> 0,
      c = c + _ >>> 0,
      d = d + w >>> 0,
      p = p + b >>> 0
  }
  return o.endian([l, c, d, p])
}


 function defaults(e, t=0) {
  if (!e.split("?")[1])
      return e;
  const n = function(e) {
      var t = e.split("?")[1];
      t = t.split("&");
      var n = {};
      for (let e = 0; e < t.length; e++) {
          let o = t[e].split("=");
          2 === o.length && (n[o[0]] = o[1])
      }
      return n
  }(e)
    , i = e.split("?")[0]
    , r = i.split("/")
    , s = r[r.length - 1].replace(/.(flv|m3u8)/g, "");
  let {fm: u, wsTime: l, wsSecret: c, ...d} = n;
  if (!u)
      return null;

    //  var o = n("J66h")

    //
    u=   Buffer.from(decodeURIComponent(u),'base64').toString('utf-8')//new Buffer(decodeURIComponent(u), 'base64').toString()
   //u = o.Base64.decode(decodeURIComponent(u));
  
   var ooo= ANhw();
  const p = u.split("_")[0]
    , f = parseInt(1e4 * (new Date).getTime() + 1e4 * Math.random());
  let h = `${p}_${t}_${s}_${f}_${l}`
    , m = ooo.bytesToHex(ooo.wordsToBytes(go(h)))
    , y = "";
  return Object.keys(d).forEach(e=>{
      y += `&${e}=${d[e]}`
  }
  ),
  `${i}?wsSecret=${m}&wsTime=${l}&u=${t}&seqid=${f}${y}`
}

var url = process.argv.splice(2)[0];
var dd=defaults(url)
console.log(dd);
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
/*  SHA-256 implementation in JavaScript | (c) Chris Veness 2002-2010 | www.movable-type.co.uk    */
/*   - see http://csrc.nist.gov/groups/ST/toolkit/secure_hashing.html                             */
/*         http://csrc.nist.gov/groups/ST/toolkit/examples.html                                   */
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */

var Sha256={};Sha256.hash=function(msg,utf8encode){utf8encode=(typeof utf8encode=='undefined')?true:utf8encode;if(utf8encode)msg=Utf8.encode(msg);var K=[0x428a2f98,0x71374491,0xb5c0fbcf,0xe9b5dba5,0x3956c25b,0x59f111f1,0x923f82a4,0xab1c5ed5,0xd807aa98,0x12835b01,0x243185be,0x550c7dc3,0x72be5d74,0x80deb1fe,0x9bdc06a7,0xc19bf174,0xe49b69c1,0xefbe4786,0x0fc19dc6,0x240ca1cc,0x2de92c6f,0x4a7484aa,0x5cb0a9dc,0x76f988da,0x983e5152,0xa831c66d,0xb00327c8,0xbf597fc7,0xc6e00bf3,0xd5a79147,0x06ca6351,0x14292967,0x27b70a85,0x2e1b2138,0x4d2c6dfc,0x53380d13,0x650a7354,0x766a0abb,0x81c2c92e,0x92722c85,0xa2bfe8a1,0xa81a664b,0xc24b8b70,0xc76c51a3,0xd192e819,0xd6990624,0xf40e3585,0x106aa070,0x19a4c116,0x1e376c08,0x2748774c,0x34b0bcb5,0x391c0cb3,0x4ed8aa4a,0x5b9cca4f,0x682e6ff3,0x748f82ee,0x78a5636f,0x84c87814,0x8cc70208,0x90befffa,0xa4506ceb,0xbef9a3f7,0xc67178f2];var H=[0x6a09e667,0xbb67ae85,0x3c6ef372,0xa54ff53a,0x510e527f,0x9b05688c,0x1f83d9ab,0x5be0cd19];msg+=String.fromCharCode(0x80);var l=msg.length/4+2;var N=Math.ceil(l/16);var M=new Array(N);for(var i=0;i<N;i++){M[i]=new Array(16);for(var j=0;j<16;j++){M[i][j]=(msg.charCodeAt(i*64+j*4)<<24)|(msg.charCodeAt(i*64+j*4+1)<<16)|(msg.charCodeAt(i*64+j*4+2)<<8)|(msg.charCodeAt(i*64+j*4+3));}}
M[N-1][14]=((msg.length-1)*8)/Math.pow(2,32);M[N-1][14]=Math.floor(M[N-1][14])
M[N-1][15]=((msg.length-1)*8)&0xffffffff;var W=new Array(64);var a,b,c,d,e,f,g,h;for(var i=0;i<N;i++){for(var t=0;t<16;t++)W[t]=M[i][t];for(var t=16;t<64;t++)W[t]=(Sha256.sigma1(W[t-2])+W[t-7]+Sha256.sigma0(W[t-15])+W[t-16])&0xffffffff;a=H[0];b=H[1];c=H[2];d=H[3];e=H[4];f=H[5];g=H[6];h=H[7];for(var t=0;t<64;t++){var T1=h+Sha256.Sigma1(e)+Sha256.Ch(e,f,g)+K[t]+W[t];var T2=Sha256.Sigma0(a)+Sha256.Maj(a,b,c);h=g;g=f;f=e;e=(d+T1)&0xffffffff;d=c;c=b;b=a;a=(T1+T2)&0xffffffff;}
H[0]=(H[0]+a)&0xffffffff;H[1]=(H[1]+b)&0xffffffff;H[2]=(H[2]+c)&0xffffffff;H[3]=(H[3]+d)&0xffffffff;H[4]=(H[4]+e)&0xffffffff;H[5]=(H[5]+f)&0xffffffff;H[6]=(H[6]+g)&0xffffffff;H[7]=(H[7]+h)&0xffffffff;}
return Sha256.toHexStr(H[0])+Sha256.toHexStr(H[1])+Sha256.toHexStr(H[2])+Sha256.toHexStr(H[3])+
Sha256.toHexStr(H[4])+Sha256.toHexStr(H[5])+Sha256.toHexStr(H[6])+Sha256.toHexStr(H[7]);}
Sha256.ROTR=function(n,x){return(x>>>n)|(x<<(32-n));}
Sha256.Sigma0=function(x){return Sha256.ROTR(2,x)^Sha256.ROTR(13,x)^Sha256.ROTR(22,x);}
Sha256.Sigma1=function(x){return Sha256.ROTR(6,x)^Sha256.ROTR(11,x)^Sha256.ROTR(25,x);}
Sha256.sigma0=function(x){return Sha256.ROTR(7,x)^Sha256.ROTR(18,x)^(x>>>3);}
Sha256.sigma1=function(x){return Sha256.ROTR(17,x)^Sha256.ROTR(19,x)^(x>>>10);}
Sha256.Ch=function(x,y,z){return(x&y)^(~x&z);}
Sha256.Maj=function(x,y,z){return(x&y)^(x&z)^(y&z);}
Sha256.toHexStr=function(n){var s="",v;for(var i=7;i>=0;i--){v=(n>>>(i*4))&0xf;s+=v.toString(16);}
return s;}
var Utf8={};Utf8.encode=function(strUni){var strUtf=strUni.replace(/[\u0080-\u07ff]/g,function(c){var cc=c.charCodeAt(0);return String.fromCharCode(0xc0|cc>>6,0x80|cc&0x3f);});strUtf=strUtf.replace(/[\u0800-\uffff]/g,function(c){var cc=c.charCodeAt(0);return String.fromCharCode(0xe0|cc>>12,0x80|cc>>6&0x3F,0x80|cc&0x3f);});return strUtf;}
Utf8.decode=function(strUtf){var strUni=strUtf.replace(/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g,function(c){var cc=((c.charCodeAt(0)&0x0f)<<12)|((c.charCodeAt(1)&0x3f)<<6)|(c.charCodeAt(2)&0x3f);return String.fromCharCode(cc);});strUni=strUni.replace(/[\u00c0-\u00df][\u0080-\u00bf]/g,function(c){var cc=(c.charCodeAt(0)&0x1f)<<6|c.charCodeAt(1)&0x3f;return String.fromCharCode(cc);});return strUni;}

/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
/*  Utf8 class: encode / decode between multi-byte Unicode characters and UTF-8 multiple          */
/*              single-byte character encoding (c) Chris Veness 2002-2010                         */
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */

var Utf8 = {};  // Utf8 namespace

/**
 * Encode multi-byte Unicode string into utf-8 multiple single-byte characters 
 * (BMP / basic multilingual plane only)
 *
 * Chars in range U+0080 - U+07FF are encoded in 2 chars, U+0800 - U+FFFF in 3 chars
 *
 * @param {String} strUni Unicode string to be encoded as UTF-8
 * @returns {String} encoded string
 */
Utf8.encode = function(strUni) {
  // use regular expressions & String.replace callback function for better efficiency 
  // than procedural approaches
  var strUtf = strUni.replace(
      /[\u0080-\u07ff]/g,  // U+0080 - U+07FF => 2 bytes 110yyyyy, 10zzzzzz
      function(c) { 
        var cc = c.charCodeAt(0);
        return String.fromCharCode(0xc0 | cc>>6, 0x80 | cc&0x3f); }
    );
  strUtf = strUtf.replace(
      /[\u0800-\uffff]/g,  // U+0800 - U+FFFF => 3 bytes 1110xxxx, 10yyyyyy, 10zzzzzz
      function(c) { 
        var cc = c.charCodeAt(0); 
        return String.fromCharCode(0xe0 | cc>>12, 0x80 | cc>>6&0x3F, 0x80 | cc&0x3f); }
    );
  return strUtf;
}

/**
 * Decode utf-8 encoded string back into multi-byte Unicode characters
 *
 * @param {String} strUtf UTF-8 string to be decoded back to Unicode
 * @returns {String} decoded string
 */
Utf8.decode = function(strUtf) {
  // note: decode 3-byte chars first as decoded 2-byte strings could appear to be 3-byte char!
  var strUni = strUtf.replace(
      /[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g,  // 3-byte chars
      function(c) {  // (note parentheses for precence)
        var cc = ((c.charCodeAt(0)&0x0f)<<12) | ((c.charCodeAt(1)&0x3f)<<6) | ( c.charCodeAt(2)&0x3f); 
        return String.fromCharCode(cc); }
    );
  strUni = strUni.replace(
      /[\u00c0-\u00df][\u0080-\u00bf]/g,                 // 2-byte chars
      function(c) {  // (note parentheses for precence)
        var cc = (c.charCodeAt(0)&0x1f)<<6 | c.charCodeAt(1)&0x3f;
        return String.fromCharCode(cc); }
    );
  return strUni;
}
/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -  */
//JSON encoder/parser;
var JSON;
if(!JSON){
    JSON={};
    
}
(function(){
    'use strict';
    function f(n){
        return n<10?'0'+n:n;
    }
    if(typeof Date.prototype.toJSON!=='function'){
        Date.prototype.toJSON=function(key){
            return isFinite(this.valueOf())?this.getUTCFullYear()+'-'+
            f(this.getUTCMonth()+1)+'-'+
            f(this.getUTCDate())+'T'+
            f(this.getUTCHours())+':'+
            f(this.getUTCMinutes())+':'+
            f(this.getUTCSeconds())+'Z':null;
        };
        
        String.prototype.toJSON=Number.prototype.toJSON=Boolean.prototype.toJSON=function(key){
            return this.valueOf();
        };
    
}
var cx=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,escapable=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,gap,indent,meta={
    '\b':'\\b',
    '\t':'\\t',
    '\n':'\\n',
    '\f':'\\f',
    '\r':'\\r',
    '"':'\\"',
    '\\':'\\\\'
},rep;
function quote(string){
    escapable.lastIndex=0;
    return escapable.test(string)?'"'+string.replace(escapable,function(a){
        var c=meta[a];
        return typeof c==='string'?c:'\\u'+('0000'+a.charCodeAt(0).toString(16)).slice(-4);
    })+'"':'"'+string+'"';
}
function str(key,holder){
    var i,k,v,length,mind=gap,partial,value=holder[key];
    if(value&&typeof value==='object'&&typeof value.toJSON==='function'){
        value=value.toJSON(key);
    }
    if(typeof rep==='function'){
        value=rep.call(holder,key,value);
    }
    switch(typeof value){
        case'string':
            return quote(value);
        case'number':
            return isFinite(value)?String(value):'null';
        case'boolean':case'null':
            return String(value);
        case'object':
            if(!value){
            return'null';
        }
        gap+=indent;
        partial=[];
        if(Object.prototype.toString.apply(value)==='[object Array]'){
            length=value.length;
            for(i=0;i<length;i+=1){
                partial[i]=str(i,value)||'null';
            }
            v=partial.length===0?'[]':gap?'[\n'+gap+partial.join(',\n'+gap)+'\n'+mind+']':'['+partial.join(',')+']';
            gap=mind;
            return v;
        }
        if(rep&&typeof rep==='object'){
            length=rep.length;
            for(i=0;i<length;i+=1){
                if(typeof rep[i]==='string'){
                    k=rep[i];
                    v=str(k,value);
                    if(v){
                        partial.push(quote(k)+(gap?': ':':')+v);
                    }
                }
            }
        }else{
    for(k in value){
        if(Object.prototype.hasOwnProperty.call(value,k)){
            v=str(k,value);
            if(v){
                partial.push(quote(k)+(gap?': ':':')+v);
            }
        }
    }
    }
v=partial.length===0?'{}':gap?'{\n'+gap+partial.join(',\n'+gap)+'\n'+mind+'}':'{'+partial.join(',')+'}';
gap=mind;
return v;
}
}
if(typeof JSON.stringify!=='function'){
    JSON.stringify=function(value,replacer,space){
        var i;
        gap='';
        indent='';
        if(typeof space==='number'){
            for(i=0;i<space;i+=1){
                indent+=' ';
            }
            }else if(typeof space==='string'){
        indent=space;
    }
    rep=replacer;
    if(replacer&&typeof replacer!=='function'&&(typeof replacer!=='object'||typeof replacer.length!=='number')){
        throw new Error('JSON.stringify');
    }
    return str('',{
        '':value
    });
};

}
if(typeof JSON.parse!=='function'){
    JSON.parse=function(text,reviver){
        var j;
        function walk(holder,key){
            var k,v,value=holder[key];
            if(value&&typeof value==='object'){
                for(k in value){
                    if(Object.prototype.hasOwnProperty.call(value,k)){
                        v=walk(value,k);
                        if(v!==undefined){
                            value[k]=v;
                        }else{
                            delete value[k];
                        }
                    }
                }
                }
return reviver.call(holder,key,value);
}
text=String(text);
cx.lastIndex=0;
if(cx.test(text)){
    text=text.replace(cx,function(a){
        return'\\u'+
        ('0000'+a.charCodeAt(0).toString(16)).slice(-4);
    });
}
if(/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,'@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,']').replace(/(?:^|:|,)(?:\s*\[)+/g,''))){
    j=eval('('+text+')');
    return typeof reviver==='function'?walk({
        '':j
    },''):j;
}
throw new SyntaxError('JSON.parse');
};

}
}());

function array2json (object){
    return JSON.stringify(object);
}

//base64
  var keyStr = "ABCDEFGHIJKLMNOP" +
               "QRSTUVWXYZabcdef" +
               "ghijklmnopqrstuv" +
               "wxyz0123456789+/" +
               "=";

  function encode64(input) {
     input = escape(input);
     var output = "";
     var chr1, chr2, chr3 = "";
     var enc1, enc2, enc3, enc4 = "";
     var i = 0;

     do {
        chr1 = input.charCodeAt(i++);
        chr2 = input.charCodeAt(i++);
        chr3 = input.charCodeAt(i++);

        enc1 = chr1 >> 2;
        enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
        enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
        enc4 = chr3 & 63;

        if (isNaN(chr2)) {
           enc3 = enc4 = 64;
        } else if (isNaN(chr3)) {
           enc4 = 64;
        }

        output = output +
           keyStr.charAt(enc1) +
           keyStr.charAt(enc2) +
           keyStr.charAt(enc3) +
           keyStr.charAt(enc4);
        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";
     } while (i < input.length);

     return output;
  }

  function decode64(input) {
     var output = "";
     var chr1, chr2, chr3 = "";
     var enc1, enc2, enc3, enc4 = "";
     var i = 0;

     // remove all characters that are not A-Z, a-z, 0-9, +, /, or =
     var base64test = /[^A-Za-z0-9\+\/\=]/g;
     if (base64test.exec(input)) {
        alert("There were invalid base64 characters in the input text.\n" +
              "Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
              "Expect errors in decoding.");
     }
     input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

     do {
        enc1 = keyStr.indexOf(input.charAt(i++));
        enc2 = keyStr.indexOf(input.charAt(i++));
        enc3 = keyStr.indexOf(input.charAt(i++));
        enc4 = keyStr.indexOf(input.charAt(i++));

        chr1 = (enc1 << 2) | (enc2 >> 4);
        chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
        chr3 = ((enc3 & 3) << 6) | enc4;

        output = output + String.fromCharCode(chr1);

        if (enc3 != 64) {
           output = output + String.fromCharCode(chr2);
        }
        if (enc4 != 64) {
           output = output + String.fromCharCode(chr3);
        }

        chr1 = chr2 = chr3 = "";
        enc1 = enc2 = enc3 = enc4 = "";

     } while (i < input.length);

     return unescape(output);
  }

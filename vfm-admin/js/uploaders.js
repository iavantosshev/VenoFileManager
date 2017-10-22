/*
* MIT Licensed
* http://www.23developer.com/opensource
* http://github.com/23/resumable.js
* Steffen Tiedemann Christensen, steffen@23company.com
*/
!function(){"use strict";var e=function(t){function n(e,t,n){var i=this;i.opts={},i.getOpt=e.getOpt,i._prevProgress=0,i.resumableObj=e,i.file=t,i.fileName=t.fileName||t.name,i.size=t.size,i.relativePath=t.webkitRelativePath||t.relativePath||i.fileName,i.uniqueIdentifier=n,i._pause=!1,i.container="";var s=void 0!==n,o=function(e,t){switch(e){case"progress":i.resumableObj.fire("fileProgress",i);break;case"error":i.abort(),s=!0,i.chunks=[],i.resumableObj.fire("fileError",i,t);break;case"success":if(s)return;i.resumableObj.fire("fileProgress",i),i.isComplete()&&i.resumableObj.fire("fileSuccess",i,t);break;case"retry":i.resumableObj.fire("fileRetry",i)}};return i.chunks=[],i.abort=function(){var e=0;a.each(i.chunks,function(t){"uploading"==t.status()&&(t.abort(),e++)}),e>0&&i.resumableObj.fire("fileProgress",i)},i.cancel=function(){var e=i.chunks;i.chunks=[],a.each(e,function(e){"uploading"==e.status()&&(e.abort(),i.resumableObj.uploadNextChunk())}),i.resumableObj.removeFile(i),i.resumableObj.fire("fileProgress",i)},i.retry=function(){i.bootstrap();var e=!1;i.resumableObj.on("chunkingComplete",function(){e||i.resumableObj.upload(),e=!0})},i.bootstrap=function(){i.abort(),s=!1,i.chunks=[],i._prevProgress=0;for(var e=i.getOpt("forceChunkSize")?Math.ceil:Math.floor,t=Math.max(e(i.file.size/i.getOpt("chunkSize")),1),n=0;t>n;n++)!function(e){window.setTimeout(function(){i.chunks.push(new r(i.resumableObj,i,e,o)),i.resumableObj.fire("chunkingProgress",i,e/t)},0)}(n);window.setTimeout(function(){i.resumableObj.fire("chunkingComplete",i)},0)},i.progress=function(){if(s)return 1;var e=0,t=!1;return a.each(i.chunks,function(n){"error"==n.status()&&(t=!0),e+=n.progress(!0)}),e=t?1:e>.99999?1:e,e=Math.max(i._prevProgress,e),i._prevProgress=e,e},i.isUploading=function(){var e=!1;return a.each(i.chunks,function(t){return"uploading"==t.status()?(e=!0,!1):void 0}),e},i.isComplete=function(){var e=!1;return a.each(i.chunks,function(t){var n=t.status();return"pending"==n||"uploading"==n||1===t.preprocessState?(e=!0,!1):void 0}),!e},i.pause=function(e){"undefined"==typeof e?i._pause=i._pause?!1:!0:i._pause=e},i.isPaused=function(){return i._pause},i.resumableObj.fire("chunkingStart",i),i.bootstrap(),this}function r(e,t,n,r){var i=this;i.opts={},i.getOpt=e.getOpt,i.resumableObj=e,i.fileObj=t,i.fileObjSize=t.size,i.fileObjType=t.file.type,i.offset=n,i.callback=r,i.lastProgressCallback=new Date,i.tested=!1,i.retries=0,i.pendingRetry=!1,i.preprocessState=0;var s=i.getOpt("chunkSize");return i.loaded=0,i.startByte=i.offset*s,i.endByte=Math.min(i.fileObjSize,(i.offset+1)*s),i.fileObjSize-i.endByte<s&&!i.getOpt("forceChunkSize")&&(i.endByte=i.fileObjSize),i.xhr=null,i.test=function(){i.xhr=new XMLHttpRequest;var e=function(e){i.tested=!0;var t=i.status();"success"==t?(i.callback(t,i.message()),i.resumableObj.uploadNextChunk()):i.send()};i.xhr.addEventListener("load",e,!1),i.xhr.addEventListener("error",e,!1),i.xhr.addEventListener("timeout",e,!1);var t=[],n=i.getOpt("parameterNamespace"),r=i.getOpt("query");"function"==typeof r&&(r=r(i.fileObj,i)),a.each(r,function(e,r){t.push([encodeURIComponent(n+e),encodeURIComponent(r)].join("="))}),t.push([n+i.getOpt("chunkNumberParameterName"),encodeURIComponent(i.offset+1)].join("=")),t.push([n+i.getOpt("chunkSizeParameterName"),encodeURIComponent(i.getOpt("chunkSize"))].join("=")),t.push([n+i.getOpt("currentChunkSizeParameterName"),encodeURIComponent(i.endByte-i.startByte)].join("=")),t.push([n+i.getOpt("totalSizeParameterName"),encodeURIComponent(i.fileObjSize)].join("=")),t.push([n+i.getOpt("typeParameterName"),encodeURIComponent(i.fileObjType)].join("=")),t.push([n+i.getOpt("identifierParameterName"),encodeURIComponent(i.fileObj.uniqueIdentifier)].join("=")),t.push([n+i.getOpt("fileNameParameterName"),encodeURIComponent(i.fileObj.fileName)].join("=")),t.push([n+i.getOpt("relativePathParameterName"),encodeURIComponent(i.fileObj.relativePath)].join("=")),t.push([n+i.getOpt("totalChunksParameterName"),encodeURIComponent(i.fileObj.chunks.length)].join("=")),i.xhr.open(i.getOpt("testMethod"),a.getTarget(t)),i.xhr.timeout=i.getOpt("xhrTimeout"),i.xhr.withCredentials=i.getOpt("withCredentials");var s=i.getOpt("headers");"function"==typeof s&&(s=s(i.fileObj,i)),a.each(s,function(e,t){i.xhr.setRequestHeader(e,t)}),i.xhr.send(null)},i.preprocessFinished=function(){i.preprocessState=2,i.send()},i.send=function(){var e=i.getOpt("preprocess");if("function"==typeof e)switch(i.preprocessState){case 0:return i.preprocessState=1,void e(i);case 1:return;case 2:}if(i.getOpt("testChunks")&&!i.tested)return void i.test();i.xhr=new XMLHttpRequest,i.xhr.upload.addEventListener("progress",function(e){new Date-i.lastProgressCallback>1e3*i.getOpt("throttleProgressCallbacks")&&(i.callback("progress"),i.lastProgressCallback=new Date),i.loaded=e.loaded||0},!1),i.loaded=0,i.pendingRetry=!1,i.callback("progress");var t=function(e){var t=i.status();if("success"==t||"error"==t)i.callback(t,i.message()),i.resumableObj.uploadNextChunk();else{i.callback("retry",i.message()),i.abort(),i.retries++;var n=i.getOpt("chunkRetryInterval");void 0!==n?(i.pendingRetry=!0,setTimeout(i.send,n)):i.send()}};i.xhr.addEventListener("load",t,!1),i.xhr.addEventListener("error",t,!1),i.xhr.addEventListener("timeout",t,!1);var n={};n[i.getOpt("chunkNumberParameterName")]=i.offset+1,n[i.getOpt("chunkSizeParameterName")]=i.getOpt("chunkSize"),n[i.getOpt("currentChunkSizeParameterName")]=i.endByte-i.startByte,n[i.getOpt("totalSizeParameterName")]=i.fileObjSize,n[i.getOpt("typeParameterName")]=i.fileObjType,n[i.getOpt("identifierParameterName")]=i.fileObj.uniqueIdentifier,n[i.getOpt("fileNameParameterName")]=i.fileObj.fileName,n[i.getOpt("relativePathParameterName")]=i.fileObj.relativePath,n[i.getOpt("totalChunksParameterName")]=i.fileObj.chunks.length;var r=i.getOpt("query");"function"==typeof r&&(r=r(i.fileObj,i)),a.each(r,function(e,t){n[e]=t});var s=i.fileObj.file.slice?"slice":i.fileObj.file.mozSlice?"mozSlice":i.fileObj.file.webkitSlice?"webkitSlice":"slice",o=i.fileObj.file[s](i.startByte,i.endByte),u=null,l=i.getOpt("target"),f=i.getOpt("parameterNamespace");if("octet"===i.getOpt("method")){u=o;var c=[];a.each(n,function(e,t){c.push([encodeURIComponent(f+e),encodeURIComponent(t)].join("="))}),l=a.getTarget(c)}else u=new FormData,a.each(n,function(e,t){u.append(f+e,t)}),u.append(f+i.getOpt("fileParameterName"),o);var p=i.getOpt("uploadMethod");i.xhr.open(p,l),"octet"===i.getOpt("method")&&i.xhr.setRequestHeader("Content-Type","binary/octet-stream"),i.xhr.timeout=i.getOpt("xhrTimeout"),i.xhr.withCredentials=i.getOpt("withCredentials");var d=i.getOpt("headers");"function"==typeof d&&(d=d(i.fileObj,i)),a.each(d,function(e,t){i.xhr.setRequestHeader(e,t)}),i.xhr.send(u)},i.abort=function(){i.xhr&&i.xhr.abort(),i.xhr=null},i.status=function(){return i.pendingRetry?"uploading":i.xhr?i.xhr.readyState<4?"uploading":200==i.xhr.status||201==i.xhr.status?"success":a.contains(i.getOpt("permanentErrors"),i.xhr.status)||i.retries>=i.getOpt("maxChunkRetries")?"error":(i.abort(),"pending"):"pending"},i.message=function(){return i.xhr?i.xhr.responseText:""},i.progress=function(e){"undefined"==typeof e&&(e=!1);var t=e?(i.endByte-i.startByte)/i.fileObjSize:1;if(i.pendingRetry)return 0;i.xhr&&i.xhr.status||(t*=.95);var n=i.status();switch(n){case"success":case"error":return 1*t;case"pending":return 0*t;default:return i.loaded/(i.endByte-i.startByte)*t}},this}if(!(this instanceof e))return new e(t);if(this.version=1,this.support=!("undefined"==typeof File||"undefined"==typeof Blob||"undefined"==typeof FileList||!Blob.prototype.webkitSlice&&!Blob.prototype.mozSlice&&!Blob.prototype.slice),!this.support)return!1;var i=this;i.files=[],i.defaults={chunkSize:1048576,forceChunkSize:!1,simultaneousUploads:3,fileParameterName:"file",chunkNumberParameterName:"resumableChunkNumber",chunkSizeParameterName:"resumableChunkSize",currentChunkSizeParameterName:"resumableCurrentChunkSize",totalSizeParameterName:"resumableTotalSize",typeParameterName:"resumableType",identifierParameterName:"resumableIdentifier",fileNameParameterName:"resumableFilename",relativePathParameterName:"resumableRelativePath",totalChunksParameterName:"resumableTotalChunks",throttleProgressCallbacks:.5,query:{},headers:{},preprocess:null,method:"multipart",uploadMethod:"POST",testMethod:"GET",prioritizeFirstAndLastChunk:!1,target:"/",parameterNamespace:"",testChunks:!0,generateUniqueIdentifier:null,getTarget:null,maxChunkRetries:void 0,chunkRetryInterval:void 0,permanentErrors:[400,404,415,500,501],maxFiles:void 0,withCredentials:!1,xhrTimeout:0,clearInput:!0,maxFilesErrorCallback:function(e,t){var n=i.getOpt("maxFiles");alert("Please upload no more than "+n+" file"+(1===n?"":"s")+" at a time.")},minFileSize:1,minFileSizeErrorCallback:function(e,t){alert(e.fileName||e.name+" is too small, please upload files larger than "+a.formatSize(i.getOpt("minFileSize"))+".")},maxFileSize:void 0,maxFileSizeErrorCallback:function(e,t){alert(e.fileName||e.name+" is too large, please upload files less than "+a.formatSize(i.getOpt("maxFileSize"))+".")},fileType:[],fileTypeErrorCallback:function(e,t){alert(e.fileName||e.name+" has type not allowed, please upload files of type "+i.getOpt("fileType")+".")}},i.opts=t||{},i.getOpt=function(t){var i=this;if(t instanceof Array){var s={};return a.each(t,function(e){s[e]=i.getOpt(e)}),s}if(i instanceof r){if("undefined"!=typeof i.opts[t])return i.opts[t];i=i.fileObj}if(i instanceof n){if("undefined"!=typeof i.opts[t])return i.opts[t];i=i.resumableObj}return i instanceof e?"undefined"!=typeof i.opts[t]?i.opts[t]:i.defaults[t]:void 0},i.events=[],i.on=function(e,t){i.events.push(e.toLowerCase(),t)},i.fire=function(){for(var e=[],t=0;t<arguments.length;t++)e.push(arguments[t]);for(var n=e[0].toLowerCase(),t=0;t<=i.events.length;t+=2)i.events[t]==n&&i.events[t+1].apply(i,e.slice(1)),"catchall"==i.events[t]&&i.events[t+1].apply(null,e);"fileerror"==n&&i.fire("error",e[2],e[1]),"fileprogress"==n&&i.fire("progress")};var a={stopEvent:function(e){e.stopPropagation(),e.preventDefault()},each:function(e,t){if("undefined"!=typeof e.length){for(var n=0;n<e.length;n++)if(t(e[n])===!1)return}else for(n in e)if(t(n,e[n])===!1)return},generateUniqueIdentifier:function(e){var t=i.getOpt("generateUniqueIdentifier");if("function"==typeof t)return t(e);var n=e.webkitRelativePath||e.fileName||e.name,r=e.size;return r+"-"+n.replace(/[^0-9a-zA-Z_-]/gim,"")},contains:function(e,t){var n=!1;return a.each(e,function(e){return e==t?(n=!0,!1):!0}),n},formatSize:function(e){return 1024>e?e+" bytes":1048576>e?(e/1024).toFixed(0)+" KB":1073741824>e?(e/1024/1024).toFixed(1)+" MB":(e/1024/1024/1024).toFixed(1)+" GB"},getTarget:function(e){var t=i.getOpt("target");return"function"==typeof t?t(e):(t+=t.indexOf("?")<0?"?":"&",t+e.join("&"))}},s=function(e){a.stopEvent(e),e.dataTransfer&&e.dataTransfer.items?u(e.dataTransfer.items,e):e.dataTransfer&&e.dataTransfer.files&&u(e.dataTransfer.files,e)},o=function(e){e.preventDefault()},u=function(e,t,n,r){n||(n={total:0,files:[],event:t}),l(e.length,n);for(var i=0;i<e.length;i++){var a,s,o=e[i];if(o.isFile||o.isDirectory)a=o;else if(o.getAsEntry)a=o.getAsEntry();else{if(!o.webkitGetAsEntry){if("function"==typeof o.getAsFile){f(o.getAsFile(),n,r);continue}if(File&&o instanceof File){f(o,n,r);continue}l(-1,n);continue}a=o.webkitGetAsEntry()}if(a){if(a.isFile)a.file(function(e){f(e,n,r)},function(e){console.warn(e)});else if(a.isDirectory){s=a.createReader();var c=[],p=function(e){s.readEntries(function(r){if(r.length>0){for(var i=0;i<r.length;i++)c.push(r[i]);p(a.fullPath)}else u(c,t,n,e),l(-1,n)},function(e){l(-1,n),console.warn(e)})};p(a.fullPath)}}else l(-1,n)}},l=function(e,t){t.total+=e,t.files.length===t.total&&c(t.files,t.event)},f=function(e,t,n){n&&(e.relativePath=n+"/"+e.name),t.files.push(e),t.files.length===t.total&&c(t.files,t.event)},c=function(e,t){var r=0,s=i.getOpt(["maxFiles","minFileSize","maxFileSize","maxFilesErrorCallback","minFileSizeErrorCallback","maxFileSizeErrorCallback","fileType","fileTypeErrorCallback"]);if("undefined"!=typeof s.maxFiles&&s.maxFiles<e.length+i.files.length){if(1!==s.maxFiles||1!==i.files.length||1!==e.length)return s.maxFilesErrorCallback(e,r++),!1;i.removeFile(i.files[0])}var o=[];a.each(e,function(e){function u(r){i.getFromUniqueIdentifier(r)||!function(){e.uniqueIdentifier=r;var a=new n(i,e,r);i.files.push(a),o.push(a),a.container="undefined"!=typeof t?t.srcElement:null,window.setTimeout(function(){i.fire("fileAdded",a,t)},0)}()}var l=e.name;if(s.fileType.length>0){var f=!1;for(var c in s.fileType){var p="."+s.fileType[c];if(-1!==l.indexOf(p,l.length-p.length)){f=!0;break}}if(!f)return s.fileTypeErrorCallback(e,r++),!1}if("undefined"!=typeof s.minFileSize&&e.size<s.minFileSize)return s.minFileSizeErrorCallback(e,r++),!1;if("undefined"!=typeof s.maxFileSize&&e.size>s.maxFileSize)return s.maxFileSizeErrorCallback(e,r++),!1;var d=a.generateUniqueIdentifier(e);d&&"function"==typeof d.done&&"function"==typeof d.fail?d.done(function(e){u(e)}).fail(function(){u()}):u(d)}),window.setTimeout(function(){i.fire("filesAdded",o)},0)};return i.uploadNextChunk=function(){var e=!1;if(i.getOpt("prioritizeFirstAndLastChunk")&&(a.each(i.files,function(t){return t.chunks.length&&"pending"==t.chunks[0].status()&&0===t.chunks[0].preprocessState?(t.chunks[0].send(),e=!0,!1):t.chunks.length>1&&"pending"==t.chunks[t.chunks.length-1].status()&&0===t.chunks[t.chunks.length-1].preprocessState?(t.chunks[t.chunks.length-1].send(),e=!0,!1):void 0}),e))return!0;if(a.each(i.files,function(t){return t.isPaused()===!1&&a.each(t.chunks,function(t){return"pending"==t.status()&&0===t.preprocessState?(t.send(),e=!0,!1):void 0}),e?!1:void 0}),e)return!0;var t=!1;return a.each(i.files,function(e){return e.isComplete()?void 0:(t=!0,!1)}),t||i.fire("complete"),!1},i.assignBrowse=function(e,t){"undefined"==typeof e.length&&(e=[e]),a.each(e,function(e){var n;"INPUT"===e.tagName&&"file"===e.type?n=e:(n=document.createElement("input"),n.setAttribute("type","file"),n.style.display="none",e.addEventListener("click",function(){n.style.opacity=0,n.style.display="block",n.focus(),n.click(),n.style.display="none"},!1),e.appendChild(n));var r=i.getOpt("maxFiles");"undefined"==typeof r||1!=r?n.setAttribute("multiple","multiple"):n.removeAttribute("multiple"),t?n.setAttribute("webkitdirectory","webkitdirectory"):n.removeAttribute("webkitdirectory"),n.addEventListener("change",function(e){c(e.target.files,e);var t=i.getOpt("clearInput");t&&(e.target.value="")},!1)})},i.assignDrop=function(e){"undefined"==typeof e.length&&(e=[e]),a.each(e,function(e){e.addEventListener("dragover",o,!1),e.addEventListener("dragenter",o,!1),e.addEventListener("drop",s,!1)})},i.unAssignDrop=function(e){"undefined"==typeof e.length&&(e=[e]),a.each(e,function(e){e.removeEventListener("dragover",o),e.removeEventListener("dragenter",o),e.removeEventListener("drop",s)})},i.isUploading=function(){var e=!1;return a.each(i.files,function(t){return t.isUploading()?(e=!0,!1):void 0}),e},i.upload=function(){if(!i.isUploading()){i.fire("uploadStart");for(var e=1;e<=i.getOpt("simultaneousUploads");e++)i.uploadNextChunk()}},i.pause=function(){a.each(i.files,function(e){e.abort()}),i.fire("pause")},i.cancel=function(){i.fire("beforeCancel");for(var e=i.files.length-1;e>=0;e--)i.files[e].cancel();i.fire("cancel")},i.progress=function(){var e=0,t=0;return a.each(i.files,function(n){e+=n.progress()*n.size,t+=n.size}),t>0?e/t:0},i.addFile=function(e,t){c([e],t)},i.removeFile=function(e){for(var t=i.files.length-1;t>=0;t--)i.files[t]===e&&i.files.splice(t,1)},i.getFromUniqueIdentifier=function(e){var t=!1;return a.each(i.files,function(n){n.uniqueIdentifier==e&&(t=n)}),t},i.getSize=function(){var e=0;return a.each(i.files,function(t){e+=t.size}),e},i.handleDropEvent=function(e){s(e)},i.handleChangeEvent=function(e){c(e.target.files,e),e.target.value=""},this};"undefined"!=typeof module?module.exports=e:"function"==typeof define&&define.amd?define(function(){return e}):window.Resumable=e}();
/*!
 * jQuery Form Plugin
 * version: 3.51.0-2014.06.20
 * Requires jQuery v1.5 or later
 * Copyright (c) 2014 M. Alsup
 * Examples and documentation at: http://malsup.com/jquery/form/
 * Project repository: https://github.com/malsup/form
 * Dual licensed under the MIT and GPL licenses.
 * https://github.com/malsup/form#copyright-and-license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery"],e):e("undefined"!=typeof jQuery?jQuery:window.Zepto)}(function(e){"use strict";function t(t){var r=t.data;t.isDefaultPrevented()||(t.preventDefault(),e(t.target).ajaxSubmit(r))}function r(t){var r=t.target,a=e(r);if(!a.is("[type=submit],[type=image]")){var n=a.closest("[type=submit]");if(0===n.length)return;r=n[0]}var i=this;if(i.clk=r,"image"==r.type)if(void 0!==t.offsetX)i.clk_x=t.offsetX,i.clk_y=t.offsetY;else if("function"==typeof e.fn.offset){var o=a.offset();i.clk_x=t.pageX-o.left,i.clk_y=t.pageY-o.top}else i.clk_x=t.pageX-r.offsetLeft,i.clk_y=t.pageY-r.offsetTop;setTimeout(function(){i.clk=i.clk_x=i.clk_y=null},100)}function a(){if(e.fn.ajaxSubmit.debug){var t="[jquery.form] "+Array.prototype.join.call(arguments,"");window.console&&window.console.log?window.console.log(t):window.opera&&window.opera.postError&&window.opera.postError(t)}}var n={};n.fileapi=void 0!==e("<input type='file'/>").get(0).files,n.formdata=void 0!==window.FormData;var i=!!e.fn.prop;e.fn.attr2=function(){if(!i)return this.attr.apply(this,arguments);var e=this.prop.apply(this,arguments);return e&&e.jquery||"string"==typeof e?e:this.attr.apply(this,arguments)},e.fn.ajaxSubmit=function(t){function r(r){var a,n,i=e.param(r,t.traditional).split("&"),o=i.length,s=[];for(a=0;o>a;a++)i[a]=i[a].replace(/\+/g," "),n=i[a].split("="),s.push([decodeURIComponent(n[0]),decodeURIComponent(n[1])]);return s}function o(a){for(var n=new FormData,i=0;i<a.length;i++)n.append(a[i].name,a[i].value);if(t.extraData){var o=r(t.extraData);for(i=0;i<o.length;i++)o[i]&&n.append(o[i][0],o[i][1])}t.data=null;var s=e.extend(!0,{},e.ajaxSettings,t,{contentType:!1,processData:!1,cache:!1,type:u||"POST"});t.uploadProgress&&(s.xhr=function(){var r=e.ajaxSettings.xhr();return r.upload&&r.upload.addEventListener("progress",function(e){var r=0,a=e.loaded||e.position,n=e.total;e.lengthComputable&&(r=Math.ceil(a/n*100)),t.uploadProgress(e,a,n,r)},!1),r}),s.data=null;var c=s.beforeSend;return s.beforeSend=function(e,r){r.data=t.formData?t.formData:n,c&&c.call(this,e,r)},e.ajax(s)}function s(r){function n(e){var t=null;try{e.contentWindow&&(t=e.contentWindow.document)}catch(r){a("cannot get iframe.contentWindow document: "+r)}if(t)return t;try{t=e.contentDocument?e.contentDocument:e.document}catch(r){a("cannot get iframe.contentDocument: "+r),t=e.document}return t}function o(){function t(){try{var e=n(g).readyState;a("state = "+e),e&&"uninitialized"==e.toLowerCase()&&setTimeout(t,50)}catch(r){a("Server abort: ",r," (",r.name,")"),s(k),j&&clearTimeout(j),j=void 0}}var r=f.attr2("target"),i=f.attr2("action"),o="multipart/form-data",c=f.attr("enctype")||f.attr("encoding")||o;w.setAttribute("target",p),(!u||/post/i.test(u))&&w.setAttribute("method","POST"),i!=m.url&&w.setAttribute("action",m.url),m.skipEncodingOverride||u&&!/post/i.test(u)||f.attr({encoding:"multipart/form-data",enctype:"multipart/form-data"}),m.timeout&&(j=setTimeout(function(){T=!0,s(D)},m.timeout));var l=[];try{if(m.extraData)for(var d in m.extraData)m.extraData.hasOwnProperty(d)&&l.push(e.isPlainObject(m.extraData[d])&&m.extraData[d].hasOwnProperty("name")&&m.extraData[d].hasOwnProperty("value")?e('<input type="hidden" name="'+m.extraData[d].name+'">').val(m.extraData[d].value).appendTo(w)[0]:e('<input type="hidden" name="'+d+'">').val(m.extraData[d]).appendTo(w)[0]);m.iframeTarget||v.appendTo("body"),g.attachEvent?g.attachEvent("onload",s):g.addEventListener("load",s,!1),setTimeout(t,15);try{w.submit()}catch(h){var x=document.createElement("form").submit;x.apply(w)}}finally{w.setAttribute("action",i),w.setAttribute("enctype",c),r?w.setAttribute("target",r):f.removeAttr("target"),e(l).remove()}}function s(t){if(!x.aborted&&!F){if(M=n(g),M||(a("cannot access response document"),t=k),t===D&&x)return x.abort("timeout"),void S.reject(x,"timeout");if(t==k&&x)return x.abort("server abort"),void S.reject(x,"error","server abort");if(M&&M.location.href!=m.iframeSrc||T){g.detachEvent?g.detachEvent("onload",s):g.removeEventListener("load",s,!1);var r,i="success";try{if(T)throw"timeout";var o="xml"==m.dataType||M.XMLDocument||e.isXMLDoc(M);if(a("isXml="+o),!o&&window.opera&&(null===M.body||!M.body.innerHTML)&&--O)return a("requeing onLoad callback, DOM not available"),void setTimeout(s,250);var u=M.body?M.body:M.documentElement;x.responseText=u?u.innerHTML:null,x.responseXML=M.XMLDocument?M.XMLDocument:M,o&&(m.dataType="xml"),x.getResponseHeader=function(e){var t={"content-type":m.dataType};return t[e.toLowerCase()]},u&&(x.status=Number(u.getAttribute("status"))||x.status,x.statusText=u.getAttribute("statusText")||x.statusText);var c=(m.dataType||"").toLowerCase(),l=/(json|script|text)/.test(c);if(l||m.textarea){var f=M.getElementsByTagName("textarea")[0];if(f)x.responseText=f.value,x.status=Number(f.getAttribute("status"))||x.status,x.statusText=f.getAttribute("statusText")||x.statusText;else if(l){var p=M.getElementsByTagName("pre")[0],h=M.getElementsByTagName("body")[0];p?x.responseText=p.textContent?p.textContent:p.innerText:h&&(x.responseText=h.textContent?h.textContent:h.innerText)}}else"xml"==c&&!x.responseXML&&x.responseText&&(x.responseXML=X(x.responseText));try{E=_(x,c,m)}catch(y){i="parsererror",x.error=r=y||i}}catch(y){a("error caught: ",y),i="error",x.error=r=y||i}x.aborted&&(a("upload aborted"),i=null),x.status&&(i=x.status>=200&&x.status<300||304===x.status?"success":"error"),"success"===i?(m.success&&m.success.call(m.context,E,"success",x),S.resolve(x.responseText,"success",x),d&&e.event.trigger("ajaxSuccess",[x,m])):i&&(void 0===r&&(r=x.statusText),m.error&&m.error.call(m.context,x,i,r),S.reject(x,"error",r),d&&e.event.trigger("ajaxError",[x,m,r])),d&&e.event.trigger("ajaxComplete",[x,m]),d&&!--e.active&&e.event.trigger("ajaxStop"),m.complete&&m.complete.call(m.context,x,i),F=!0,m.timeout&&clearTimeout(j),setTimeout(function(){m.iframeTarget?v.attr("src",m.iframeSrc):v.remove(),x.responseXML=null},100)}}}var c,l,m,d,p,v,g,x,y,b,T,j,w=f[0],S=e.Deferred();if(S.abort=function(e){x.abort(e)},r)for(l=0;l<h.length;l++)c=e(h[l]),i?c.prop("disabled",!1):c.removeAttr("disabled");if(m=e.extend(!0,{},e.ajaxSettings,t),m.context=m.context||m,p="jqFormIO"+(new Date).getTime(),m.iframeTarget?(v=e(m.iframeTarget),b=v.attr2("name"),b?p=b:v.attr2("name",p)):(v=e('<iframe name="'+p+'" src="'+m.iframeSrc+'" />'),v.css({position:"absolute",top:"-1000px",left:"-1000px"})),g=v[0],x={aborted:0,responseText:null,responseXML:null,status:0,statusText:"n/a",getAllResponseHeaders:function(){},getResponseHeader:function(){},setRequestHeader:function(){},abort:function(t){var r="timeout"===t?"timeout":"aborted";a("aborting upload... "+r),this.aborted=1;try{g.contentWindow.document.execCommand&&g.contentWindow.document.execCommand("Stop")}catch(n){}v.attr("src",m.iframeSrc),x.error=r,m.error&&m.error.call(m.context,x,r,t),d&&e.event.trigger("ajaxError",[x,m,r]),m.complete&&m.complete.call(m.context,x,r)}},d=m.global,d&&0===e.active++&&e.event.trigger("ajaxStart"),d&&e.event.trigger("ajaxSend",[x,m]),m.beforeSend&&m.beforeSend.call(m.context,x,m)===!1)return m.global&&e.active--,S.reject(),S;if(x.aborted)return S.reject(),S;y=w.clk,y&&(b=y.name,b&&!y.disabled&&(m.extraData=m.extraData||{},m.extraData[b]=y.value,"image"==y.type&&(m.extraData[b+".x"]=w.clk_x,m.extraData[b+".y"]=w.clk_y)));var D=1,k=2,A=e("meta[name=csrf-token]").attr("content"),L=e("meta[name=csrf-param]").attr("content");L&&A&&(m.extraData=m.extraData||{},m.extraData[L]=A),m.forceSync?o():setTimeout(o,10);var E,M,F,O=50,X=e.parseXML||function(e,t){return window.ActiveXObject?(t=new ActiveXObject("Microsoft.XMLDOM"),t.async="false",t.loadXML(e)):t=(new DOMParser).parseFromString(e,"text/xml"),t&&t.documentElement&&"parsererror"!=t.documentElement.nodeName?t:null},C=e.parseJSON||function(e){return window.eval("("+e+")")},_=function(t,r,a){var n=t.getResponseHeader("content-type")||"",i="xml"===r||!r&&n.indexOf("xml")>=0,o=i?t.responseXML:t.responseText;return i&&"parsererror"===o.documentElement.nodeName&&e.error&&e.error("parsererror"),a&&a.dataFilter&&(o=a.dataFilter(o,r)),"string"==typeof o&&("json"===r||!r&&n.indexOf("json")>=0?o=C(o):("script"===r||!r&&n.indexOf("javascript")>=0)&&e.globalEval(o)),o};return S}if(!this.length)return a("ajaxSubmit: skipping submit process - no element selected"),this;var u,c,l,f=this;"function"==typeof t?t={success:t}:void 0===t&&(t={}),u=t.type||this.attr2("method"),c=t.url||this.attr2("action"),l="string"==typeof c?e.trim(c):"",l=l||window.location.href||"",l&&(l=(l.match(/^([^#]+)/)||[])[1]),t=e.extend(!0,{url:l,success:e.ajaxSettings.success,type:u||e.ajaxSettings.type,iframeSrc:/^https/i.test(window.location.href||"")?"javascript:false":"about:blank"},t);var m={};if(this.trigger("form-pre-serialize",[this,t,m]),m.veto)return a("ajaxSubmit: submit vetoed via form-pre-serialize trigger"),this;if(t.beforeSerialize&&t.beforeSerialize(this,t)===!1)return a("ajaxSubmit: submit aborted via beforeSerialize callback"),this;var d=t.traditional;void 0===d&&(d=e.ajaxSettings.traditional);var p,h=[],v=this.formToArray(t.semantic,h);if(t.data&&(t.extraData=t.data,p=e.param(t.data,d)),t.beforeSubmit&&t.beforeSubmit(v,this,t)===!1)return a("ajaxSubmit: submit aborted via beforeSubmit callback"),this;if(this.trigger("form-submit-validate",[v,this,t,m]),m.veto)return a("ajaxSubmit: submit vetoed via form-submit-validate trigger"),this;var g=e.param(v,d);p&&(g=g?g+"&"+p:p),"GET"==t.type.toUpperCase()?(t.url+=(t.url.indexOf("?")>=0?"&":"?")+g,t.data=null):t.data=g;var x=[];if(t.resetForm&&x.push(function(){f.resetForm()}),t.clearForm&&x.push(function(){f.clearForm(t.includeHidden)}),!t.dataType&&t.target){var y=t.success||function(){};x.push(function(r){var a=t.replaceTarget?"replaceWith":"html";e(t.target)[a](r).each(y,arguments)})}else t.success&&x.push(t.success);if(t.success=function(e,r,a){for(var n=t.context||this,i=0,o=x.length;o>i;i++)x[i].apply(n,[e,r,a||f,f])},t.error){var b=t.error;t.error=function(e,r,a){var n=t.context||this;b.apply(n,[e,r,a,f])}}if(t.complete){var T=t.complete;t.complete=function(e,r){var a=t.context||this;T.apply(a,[e,r,f])}}var j=e("input[type=file]:enabled",this).filter(function(){return""!==e(this).val()}),w=j.length>0,S="multipart/form-data",D=f.attr("enctype")==S||f.attr("encoding")==S,k=n.fileapi&&n.formdata;a("fileAPI :"+k);var A,L=(w||D)&&!k;t.iframe!==!1&&(t.iframe||L)?t.closeKeepAlive?e.get(t.closeKeepAlive,function(){A=s(v)}):A=s(v):A=(w||D)&&k?o(v):e.ajax(t),f.removeData("jqxhr").data("jqxhr",A);for(var E=0;E<h.length;E++)h[E]=null;return this.trigger("form-submit-notify",[this,t]),this},e.fn.ajaxForm=function(n){if(n=n||{},n.delegation=n.delegation&&e.isFunction(e.fn.on),!n.delegation&&0===this.length){var i={s:this.selector,c:this.context};return!e.isReady&&i.s?(a("DOM not ready, queuing ajaxForm"),e(function(){e(i.s,i.c).ajaxForm(n)}),this):(a("terminating; zero elements found by selector"+(e.isReady?"":" (DOM not ready)")),this)}return n.delegation?(e(document).off("submit.form-plugin",this.selector,t).off("click.form-plugin",this.selector,r).on("submit.form-plugin",this.selector,n,t).on("click.form-plugin",this.selector,n,r),this):this.ajaxFormUnbind().bind("submit.form-plugin",n,t).bind("click.form-plugin",n,r)},e.fn.ajaxFormUnbind=function(){return this.unbind("submit.form-plugin click.form-plugin")},e.fn.formToArray=function(t,r){var a=[];if(0===this.length)return a;var i,o=this[0],s=this.attr("id"),u=t?o.getElementsByTagName("*"):o.elements;if(u&&!/MSIE [678]/.test(navigator.userAgent)&&(u=e(u).get()),s&&(i=e(':input[form="'+s+'"]').get(),i.length&&(u=(u||[]).concat(i))),!u||!u.length)return a;var c,l,f,m,d,p,h;for(c=0,p=u.length;p>c;c++)if(d=u[c],f=d.name,f&&!d.disabled)if(t&&o.clk&&"image"==d.type)o.clk==d&&(a.push({name:f,value:e(d).val(),type:d.type}),a.push({name:f+".x",value:o.clk_x},{name:f+".y",value:o.clk_y}));else if(m=e.fieldValue(d,!0),m&&m.constructor==Array)for(r&&r.push(d),l=0,h=m.length;h>l;l++)a.push({name:f,value:m[l]});else if(n.fileapi&&"file"==d.type){r&&r.push(d);var v=d.files;if(v.length)for(l=0;l<v.length;l++)a.push({name:f,value:v[l],type:d.type});else a.push({name:f,value:"",type:d.type})}else null!==m&&"undefined"!=typeof m&&(r&&r.push(d),a.push({name:f,value:m,type:d.type,required:d.required}));if(!t&&o.clk){var g=e(o.clk),x=g[0];f=x.name,f&&!x.disabled&&"image"==x.type&&(a.push({name:f,value:g.val()}),a.push({name:f+".x",value:o.clk_x},{name:f+".y",value:o.clk_y}))}return a},e.fn.formSerialize=function(t){return e.param(this.formToArray(t))},e.fn.fieldSerialize=function(t){var r=[];return this.each(function(){var a=this.name;if(a){var n=e.fieldValue(this,t);if(n&&n.constructor==Array)for(var i=0,o=n.length;o>i;i++)r.push({name:a,value:n[i]});else null!==n&&"undefined"!=typeof n&&r.push({name:this.name,value:n})}}),e.param(r)},e.fn.fieldValue=function(t){for(var r=[],a=0,n=this.length;n>a;a++){var i=this[a],o=e.fieldValue(i,t);null===o||"undefined"==typeof o||o.constructor==Array&&!o.length||(o.constructor==Array?e.merge(r,o):r.push(o))}return r},e.fieldValue=function(t,r){var a=t.name,n=t.type,i=t.tagName.toLowerCase();if(void 0===r&&(r=!0),r&&(!a||t.disabled||"reset"==n||"button"==n||("checkbox"==n||"radio"==n)&&!t.checked||("submit"==n||"image"==n)&&t.form&&t.form.clk!=t||"select"==i&&-1==t.selectedIndex))return null;if("select"==i){var o=t.selectedIndex;if(0>o)return null;for(var s=[],u=t.options,c="select-one"==n,l=c?o+1:u.length,f=c?o:0;l>f;f++){var m=u[f];if(m.selected){var d=m.value;if(d||(d=m.attributes&&m.attributes.value&&!m.attributes.value.specified?m.text:m.value),c)return d;s.push(d)}}return s}return e(t).val()},e.fn.clearForm=function(t){return this.each(function(){e("input,select,textarea",this).clearFields(t)})},e.fn.clearFields=e.fn.clearInputs=function(t){var r=/^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i;return this.each(function(){var a=this.type,n=this.tagName.toLowerCase();r.test(a)||"textarea"==n?this.value="":"checkbox"==a||"radio"==a?this.checked=!1:"select"==n?this.selectedIndex=-1:"file"==a?/MSIE/.test(navigator.userAgent)?e(this).replaceWith(e(this).clone(!0)):e(this).val(""):t&&(t===!0&&/hidden/.test(a)||"string"==typeof t&&e(this).is(t))&&(this.value="")})},e.fn.resetForm=function(){return this.each(function(){("function"==typeof this.reset||"object"==typeof this.reset&&!this.reset.nodeType)&&this.reset()})},e.fn.enable=function(e){return void 0===e&&(e=!0),this.each(function(){this.disabled=!e})},e.fn.selected=function(t){return void 0===t&&(t=!0),this.each(function(){var r=this.type;if("checkbox"==r||"radio"==r)this.checked=t;else if("option"==this.tagName.toLowerCase()){var a=e(this).parent("select");t&&a[0]&&"select-one"==a[0].type&&a.find("option").selected(!1),this.selected=t}})},e.fn.ajaxSubmit.debug=!1});

/*
* jquery.client
*/
!function(){var i={init:function(){this.browser=this.searchString(this.dataBrowser)||"An unknown browser",this.version=this.searchVersion(navigator.userAgent)||this.searchVersion(navigator.appVersion)||"an unknown version",this.OS=this.searchString(this.dataOS)||"an unknown OS"},searchString:function(i){for(var n=0;n<i.length;n++){var r=i[n].string,t=i[n].prop;if(this.versionSearchString=i[n].versionSearch||i[n].identity,r){if(-1!=r.indexOf(i[n].subString))return i[n].identity}else if(t)return i[n].identity}},searchVersion:function(i){var n=i.indexOf(this.versionSearchString);if(-1!=n)return parseFloat(i.substring(n+this.versionSearchString.length+1))},dataBrowser:[{string:navigator.userAgent,subString:"Chrome",identity:"Chrome"},{string:navigator.userAgent,subString:"OmniWeb",versionSearch:"OmniWeb/",identity:"OmniWeb"},{string:navigator.vendor,subString:"Apple",identity:"Safari",versionSearch:"Version"},{prop:window.opera,identity:"Opera"},{string:navigator.vendor,subString:"iCab",identity:"iCab"},{string:navigator.vendor,subString:"KDE",identity:"Konqueror"},{string:navigator.userAgent,subString:"Firefox",identity:"Firefox"},{string:navigator.vendor,subString:"Camino",identity:"Camino"},{string:navigator.userAgent,subString:"Netscape",identity:"Netscape"},{string:navigator.userAgent,subString:"MSIE",identity:"Explorer",versionSearch:"MSIE"},{string:navigator.userAgent,subString:"Gecko",identity:"Mozilla",versionSearch:"rv"},{string:navigator.userAgent,subString:"Mozilla",identity:"Netscape",versionSearch:"Mozilla"}],dataOS:[{string:navigator.platform,subString:"Win",identity:"Windows"},{string:navigator.platform,subString:"Mac",identity:"Mac"},{string:navigator.userAgent,subString:"iPhone",identity:"iPhone/iPod"},{string:navigator.platform,subString:"Linux",identity:"Linux"}]};i.init(),window.$.client={os:i.OS,browser:i.browser}}();

/* 
* Send upload notification 
* to selected users, or refresh page
*/
function notifyusers() {

    var locazio = location.pathname;
    var queri = location.search;
    queri = queri.replace('&response', '');
    queri = queri.replace('?response', '');
    queri = queri.replace('?del', '?nodel');
    queri = queri.replace('&del', '&nodel');
    if (queri == "") { queri = "?" } else { queri = queri+"&"; }

	var anyUserChecked = $('#userslist :checkbox:checked').length > 0;

	if (anyUserChecked == true) {

	    var now = $.now();
	    var userslist = $("#userslist").serialize();

	    $.ajax({
	        cache: false,
	        type: "POST",
	        url: "vfm-admin/ajax/sendupnotif.php?t="+now,
	        data: userslist

	    })
	    .done(function(msg) {
	    	setTimeout(function() {
	            location.href = locazio+queri+"response"
	        }, 800);
	    });

	} else {
		setTimeout(function() {
	        location.href = locazio+queri+"response"
	    }, 800);
	}
}
/*
* call resumable.js
*/
function resumableJsSetup($android, $target, $placeholder, $singleprogress) {
	$android = $android || 'no';
	$singleprogress = $singleprogress || false;

	var ua = navigator.userAgent.toLowerCase();
	var android = $android;

	var r = new Resumable({
		target						: 'vfm-admin/chunk.php?loc='+$target,
		simultaneousUploads 		: 3,
		prioritizeFirstAndLastChunk	: true,
		// chunkSize 					: 2*1024*1024, // default 1MB (the example sets 2Mb)
		// maxFiles 					: 1, // uncomment this to disable multiple uploading
		// maxFileSize 					: 10*1024*1024, // uncomment this to limit the max file size (the example sets 10Mb)
	    minFileSizeErrorCallback:function(file, errorCount) {
	        setTimeout(function() {
	            alert(file.fileName||file.name +' is not valid.');
	        }, 1000);
	    }
    });

    var percentVal = 0;
    var roundval = 0;

    if (r.support && android == 'no') {

        r.assignBrowse(document.getElementById('upchunk'));
        r.assignDrop(document.getElementById('uparea'));

        $("#fileToUpload").attr("placeholder", $placeholder);

        r.on('uploadStart', function(){
            $("#resumer").remove();
           	$("#upchunk").before("<button class=\"btn btn-primary\" id=\"resumer\"><i class=\"fa fa-pause\"></i></button>");
            window.onbeforeunload = function() {
                return 'Are you sure you want to leave?';
            }

	        $('#resumer').on('click', function(){
	        	r.pause();
	        });

        });
        
        r.on('pause', function(){
            $("#resumer").remove();
            $("#upchunk").before("<button class=\"btn btn-primary\" id=\"resumer\"><i class=\"fa fa-play\"></i></button>");
	        $('#resumer').on('click', function(){
	        	r.upload();
	        });
        });

        r.on('progress', function(){
            percentVal = r.progress()*100;
            roundval = percentVal.toFixed(1);
            $('.upbar p').html(roundval+'%');
            $(".upbar").width(percentVal+'%');
        });

        // upload progress for individual files
        if ($singleprogress == true) { 
            r.on('fileProgress', function(file){
                percentVal = file.progress(true)*100;
                $('.upbarfile p').html(file.fileName);
                $(".upbarfile").width(percentVal+'%');
            });
        }

        r.on('error', function(message, file){
            console.log(message, file);
        });

        r.on('fileAdded', function(file, event){
            r.upload();
        });

        // add file path 
        // to notification message
        r.on('fileSuccess', function(file, event){
            var newinput = '<input type="hidden" name="filename[]" value="'+file.fileName+'">';
            $("#userslist").append(newinput);
        });
        
        r.on('complete', function(){
            window.onbeforeunload = null;
            notifyusers();
        });

        // Drag & Drop
        $('#uparea').on(
            'dragstart dragenter dragover',
            function(e) {
                $(".overdrag").css('display','block');
        });
        $('.overdrag').on(
            'drop dragleave dragend mouseup',
            function(e) {
                $(".overdrag").css('display','none');
        });

    } else {

        // Resumable.js is not supported, fall back on the form.js method
        var ie = ((document.all) ? true : false);

        $("#upchunk").remove();
        $('#upformsubmit').prop('disabled', true).show();

        if (ie || ($.client.os === 'Windows' && $.client.browser === 'Safari' ) || android === 'yes') {
            
            // form.js is not supported ( < IE 10 or Safari on Windows), fall back on the old classic form method
            $('#upload_file').css('display','table-cell');
            $('.ie_hidden').remove();
            $(document).on('click', '#upformsubmit', function(e) {
                $('#fileToUpload').val('Loading....');
            });
        } else {

            $(document).on('click', '#fileToUpload', function() {
                $('.upload_file').trigger('click');
            });
            $(document).on('click', '#upformsubmit', function(e) {
                e.preventDefault();
                $('.upload_file').trigger('click');
            });
        }

        $(document).ready(function(){

            var progress = $('#progress-up');
            var probar = $('.upbar');
            var prop = $('.upbar p');

            $('#upForm').ajaxForm({
                beforeSend: function() {            
                    progress.css('opacity', 1);
                },
                uploadProgress: function(event, position, total, percentComplete) {
                    var percentVal = percentComplete ;
                    var roundval = percentComplete.toFixed(1);
                    
                    probar.width(percentVal);
                    prop.html(roundval);
                },
                success: function() {
                    var percentVal = '100';
                    probar.width(percentVal+'%');
                    prop.html(percentVal+'%');
                },
                complete: function(xhr) {
					notifyusers();
                }
            });
        });
    
        $('.btn-file :file').on('fileselect', function (event, numFiles, label) {
            var input = $(this).parents('.input-group').find(':text'),
            log = numFiles > 1 ? numFiles + ' files selected' : label;

	        // add file path 
	        // to notification message
		    var files = $(this)[0].files;
		    for (var i = 0; i < files.length; i++) {
		        var newinput = '<input type="hidden" name="filename[]" value="'+files[i].name+'">';
		        $("#userslist").append(newinput);
		    }
            if (input.length) {
                input.val(log);
                // auto start upload after select if browser is not IE
                if (!ie) {
                    $("#upForm").submit();
                } else {
                    $('#upformsubmit').prop('disabled', false);
                }
            }
        });
    }
}
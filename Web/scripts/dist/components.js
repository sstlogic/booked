(self.webpackChunkbooked_scheduler=self.webpackChunkbooked_scheduler||[]).push([[160],{20640:(e,t,r)=>{"use strict";var a=r(11742),n={"text/plain":"Text","text/html":"Url",default:"Text"};e.exports=function(e,t){var r,o,i,l,u,c,s=!1;t||(t={}),r=t.debug||!1;try{if(i=a(),l=document.createRange(),u=document.getSelection(),(c=document.createElement("span")).textContent=e,c.style.all="unset",c.style.position="fixed",c.style.top=0,c.style.clip="rect(0, 0, 0, 0)",c.style.whiteSpace="pre",c.style.webkitUserSelect="text",c.style.MozUserSelect="text",c.style.msUserSelect="text",c.style.userSelect="text",c.addEventListener("copy",(function(a){if(a.stopPropagation(),t.format)if(a.preventDefault(),void 0===a.clipboardData){r&&console.warn("unable to use e.clipboardData"),r&&console.warn("trying IE specific stuff"),window.clipboardData.clearData();var o=n[t.format]||n.default;window.clipboardData.setData(o,e)}else a.clipboardData.clearData(),a.clipboardData.setData(t.format,e);t.onCopy&&(a.preventDefault(),t.onCopy(a.clipboardData))})),document.body.appendChild(c),l.selectNodeContents(c),u.addRange(l),!document.execCommand("copy"))throw new Error("copy command was unsuccessful");s=!0}catch(a){r&&console.error("unable to copy using execCommand: ",a),r&&console.warn("trying IE specific stuff");try{window.clipboardData.setData(t.format||"text",e),t.onCopy&&t.onCopy(window.clipboardData),s=!0}catch(a){r&&console.error("unable to copy using clipboardData: ",a),r&&console.error("falling back to prompt"),o=function(e){var t=(/mac os x/i.test(navigator.userAgent)?"⌘":"Ctrl")+"+C";return e.replace(/#{\s*key\s*}/g,t)}("message"in t?t.message:"Copy to clipboard: #{key}, Enter"),window.prompt(o,e)}}finally{u&&("function"==typeof u.removeRange?u.removeRange(l):u.removeAllRanges()),c&&document.body.removeChild(c),i()}return s}},74300:(e,t,r)=>{"use strict";function a(e){return a="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},a(e)}Object.defineProperty(t,"__esModule",{value:!0}),t.CopyToClipboard=void 0;var n=l(r(67294)),o=l(r(20640)),i=["text","onCopy","options","children"];function l(e){return e&&e.__esModule?e:{default:e}}function u(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);t&&(a=a.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,a)}return r}function c(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?u(Object(r),!0).forEach((function(t){h(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):u(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}function s(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function d(e,t){for(var r=0;r<t.length;r++){var a=t[r];a.enumerable=a.enumerable||!1,a.configurable=!0,"value"in a&&(a.writable=!0),Object.defineProperty(e,a.key,a)}}function f(e,t){return f=Object.setPrototypeOf||function(e,t){return e.__proto__=t,e},f(e,t)}function p(e,t){if(t&&("object"===a(t)||"function"==typeof t))return t;if(void 0!==t)throw new TypeError("Derived constructors may only return object or undefined");return m(e)}function m(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}function b(e){return b=Object.setPrototypeOf?Object.getPrototypeOf:function(e){return e.__proto__||Object.getPrototypeOf(e)},b(e)}function h(e,t,r){return t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}var g=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),Object.defineProperty(e,"prototype",{writable:!1}),t&&f(e,t)}(g,e);var t,r,a,l,u=(a=g,l=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(e){return!1}}(),function(){var e,t=b(a);if(l){var r=b(this).constructor;e=Reflect.construct(t,arguments,r)}else e=t.apply(this,arguments);return p(this,e)});function g(){var e;s(this,g);for(var t=arguments.length,r=new Array(t),a=0;a<t;a++)r[a]=arguments[a];return h(m(e=u.call.apply(u,[this].concat(r))),"onClick",(function(t){var r=e.props,a=r.text,i=r.onCopy,l=r.children,u=r.options,c=n.default.Children.only(l),s=(0,o.default)(a,u);i&&i(a,s),c&&c.props&&"function"==typeof c.props.onClick&&c.props.onClick(t)})),e}return t=g,(r=[{key:"render",value:function(){var e=this.props,t=(e.text,e.onCopy,e.options,e.children),r=function(e,t){if(null==e)return{};var r,a,n=function(e,t){if(null==e)return{};var r,a,n={},o=Object.keys(e);for(a=0;a<o.length;a++)r=o[a],t.indexOf(r)>=0||(n[r]=e[r]);return n}(e,t);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);for(a=0;a<o.length;a++)r=o[a],t.indexOf(r)>=0||Object.prototype.propertyIsEnumerable.call(e,r)&&(n[r]=e[r])}return n}(e,i),a=n.default.Children.only(t);return n.default.cloneElement(a,c(c({},r),{},{onClick:this.onClick}))}}])&&d(t.prototype,r),Object.defineProperty(t,"prototype",{writable:!1}),g}(n.default.PureComponent);t.CopyToClipboard=g,h(g,"defaultProps",{onCopy:void 0,options:void 0})},74855:(e,t,r)=>{"use strict";var a=r(74300).CopyToClipboard;a.CopyToClipboard=a,e.exports=a},11742:e=>{e.exports=function(){var e=document.getSelection();if(!e.rangeCount)return function(){};for(var t=document.activeElement,r=[],a=0;a<e.rangeCount;a++)r.push(e.getRangeAt(a));switch(t.tagName.toUpperCase()){case"INPUT":case"TEXTAREA":t.blur();break;default:t=null}return e.removeAllRanges(),function(){"Caret"===e.type&&e.removeAllRanges(),e.rangeCount||r.forEach((function(t){e.addRange(t)})),t&&t.focus()}}},40972:function(e,t,r){"use strict";var a=this&&this.__awaiter||function(e,t,r,a){return new(r||(r=Promise))((function(n,o){function i(e){try{u(a.next(e))}catch(e){o(e)}}function l(e){try{u(a.throw(e))}catch(e){o(e)}}function u(e){var t;e.done?n(e.value):(t=e.value,t instanceof r?t:new r((function(e){e(t)}))).then(i,l)}u((a=a.apply(e,t||[])).next())}))};Object.defineProperty(t,"__esModule",{value:!0}),t.ResourceTreeApi=void 0;const n=r(81902);class o extends n.Api{getResourceGroupTree(e){return a(this,void 0,void 0,(function*(){return this.get("api/resources.php","tree",{sid:e||0})}))}}t.ResourceTreeApi=o},84228:function(e,t,r){"use strict";var a=this&&this.__createBinding||(Object.create?function(e,t,r,a){void 0===a&&(a=r);var n=Object.getOwnPropertyDescriptor(t,r);n&&!("get"in n?!t.__esModule:n.writable||n.configurable)||(n={enumerable:!0,get:function(){return t[r]}}),Object.defineProperty(e,a,n)}:function(e,t,r,a){void 0===a&&(a=r),e[a]=t[r]}),n=this&&this.__setModuleDefault||(Object.create?function(e,t){Object.defineProperty(e,"default",{enumerable:!0,value:t})}:function(e,t){e.default=t}),o=this&&this.__importStar||function(e){if(e&&e.__esModule)return e;var t={};if(null!=e)for(var r in e)"default"!==r&&Object.prototype.hasOwnProperty.call(e,r)&&a(t,e,r);return n(t,e),t},i=this&&this.__importDefault||function(e){return e&&e.__esModule?e:{default:e}};Object.defineProperty(t,"__esModule",{value:!0}),t.BookedCopyToClipboard=void 0;const l=o(r(67294)),u=i(r(74855));t.BookedCopyToClipboard=function(e){const{textToCopy:t,successText:r,onCopy:a,children:n}=e,[o,i]=(0,l.useState)(!1);return l.default.createElement("div",{className:"d-inline-block position-relative"},l.default.createElement(u.default,{text:t,onCopy:function(e,t){a&&a(e,t),t&&(i(!0),setTimeout((()=>i(!1)),3e3))}},n),o&&l.default.createElement("div",{className:"copied-success"},r||"Copied"))}},50971:function(e,t,r){"use strict";var a=this&&this.__createBinding||(Object.create?function(e,t,r,a){void 0===a&&(a=r);var n=Object.getOwnPropertyDescriptor(t,r);n&&!("get"in n?!t.__esModule:n.writable||n.configurable)||(n={enumerable:!0,get:function(){return t[r]}}),Object.defineProperty(e,a,n)}:function(e,t,r,a){void 0===a&&(a=r),e[a]=t[r]}),n=this&&this.__setModuleDefault||(Object.create?function(e,t){Object.defineProperty(e,"default",{enumerable:!0,value:t})}:function(e,t){e.default=t}),o=this&&this.__importStar||function(e){if(e&&e.__esModule)return e;var t={};if(null!=e)for(var r in e)"default"!==r&&Object.prototype.hasOwnProperty.call(e,r)&&a(t,e,r);return n(t,e),t};Object.defineProperty(t,"__esModule",{value:!0}),t.BookedCustomAttribute=void 0;const i=o(r(67294)),l=r(78929),u=r(81927),c=r(34297),s=r(68960);t.BookedCustomAttribute=function(e){const{isReadonly:t,isSearch:r,attribute:a,value:n,prefix:o,dateTimeFormat:d,lang:f,firstDayOfWeek:p}=e,[m,b]=(0,i.useState)(n||void 0);return t?i.default.createElement(l.CustomAttribute,{attribute:a,value:n||void 0,dateTimeFormat:d}):r?i.default.createElement(u.CustomAttributeSearch,{attribute:a,value:[m||""],comparison:s.ComparisonType.Equals,onChange:e=>b(e?e[0]:void 0),onComparisonChanged:()=>{},isBasicSearch:!0,id:`${o}attribute-search-${a.id}`,namePrefix:o,dateTimeFormat:d,lang:f}):i.default.createElement(c.CustomAttributeEdit,{attribute:a,value:m,onChange:e=>b(e||void 0),isValid:!0,disabled:!1,id:`${o}attribute-${a.id}`,namePrefix:o,dateTimeFormat:d,firstDayOfWeek:p,lang:f})}},81927:function(e,t,r){"use strict";var a=this&&this.__importDefault||function(e){return e&&e.__esModule?e:{default:e}};Object.defineProperty(t,"__esModule",{value:!0}),t.CustomAttributeSearch=void 0;const n=a(r(67294)),o=r(69989),i=r(51812),l=r(3717),u=r(19475),c=r(9305),s=r(44830);t.CustomAttributeSearch=function(e){const{attribute:t}=e;return t.type===o.AttributeTypeOptions.DateTime?n.default.createElement(s.CustomAttributeSearchDate,Object.assign({},e)):t.type===o.AttributeTypeOptions.Checkbox?n.default.createElement(u.CustomAttributeSearchCheckbox,Object.assign({},e)):[o.AttributeTypeOptions.SelectList,o.AttributeTypeOptions.MultiSelect].includes(t.type)?n.default.createElement(c.CustomAttributeSearchSelect,Object.assign({},e)):t.type===o.AttributeTypeOptions.MultiLineTextbox?n.default.createElement(l.CustomAttributeSearchTextarea,Object.assign({},e)):n.default.createElement(i.CustomAttributeSearchTextbox,Object.assign({},e))}},19475:function(e,t,r){"use strict";var a=this&&this.__importDefault||function(e){return e&&e.__esModule?e:{default:e}};Object.defineProperty(t,"__esModule",{value:!0}),t.CustomAttributeSearchCheckbox=void 0;const n=a(r(67294)),o=a(r(23157)),i=r(81468),l=r(1494),u=r(68960);t.CustomAttributeSearchCheckbox=function(e){const{t}=(0,i.useTranslation)(),r=[{label:t("Yes"),value:"1"},{label:t("No"),value:"0"}],a=e.id||`attribute-search-${e.attribute.id}`,c=`${e.namePrefix||""}psiattribute[${e.attribute.id}]`;return n.default.createElement("div",{className:"attribute"},n.default.createElement("label",{className:"form-label",htmlFor:a},e.attribute.label),n.default.createElement(o.default,{id:a,name:c,isClearable:!0,options:r,getOptionValue:e=>e.value,getOptionLabel:e=>e.label,value:r.filter((t=>t.value===e.value[0])),isDisabled:e.comparison!==u.ComparisonType.Equals,onChange:t=>{return r=null==t?void 0:t.value,void e.onChange([r||""]);var r}}),!e.isBasicSearch&&n.default.createElement(l.CustomAttributeSearchNoValueCheckbox,{attribute:e.attribute,comparison:e.comparison,onChange:e.onComparisonChanged}))}},44830:function(e,t,r){"use strict";var a=this&&this.__createBinding||(Object.create?function(e,t,r,a){void 0===a&&(a=r);var n=Object.getOwnPropertyDescriptor(t,r);n&&!("get"in n?!t.__esModule:n.writable||n.configurable)||(n={enumerable:!0,get:function(){return t[r]}}),Object.defineProperty(e,a,n)}:function(e,t,r,a){void 0===a&&(a=r),e[a]=t[r]}),n=this&&this.__setModuleDefault||(Object.create?function(e,t){Object.defineProperty(e,"default",{enumerable:!0,value:t})}:function(e,t){e.default=t}),o=this&&this.__importStar||function(e){if(e&&e.__esModule)return e;var t={};if(null!=e)for(var r in e)"default"!==r&&Object.prototype.hasOwnProperty.call(e,r)&&a(t,e,r);return n(t,e),t},i=this&&this.__importDefault||function(e){return e&&e.__esModule?e:{default:e}};Object.defineProperty(t,"__esModule",{value:!0}),t.CustomAttributeSearchDate=void 0;const l=o(r(67294)),u=i(r(9198)),c=r(73356);r(44308);const s=r(1494),d=r(68960);t.CustomAttributeSearchDate=function(e){const[t,r]=(0,l.useState)(function(){if(!e.value)return null;const t=Date.parse(e.value[0]);return Number.isNaN(t)?null:new Date(t)}()),a=e.id||`attribute-search-${e.attribute.id}`,n=`${e.namePrefix||""}psiattribute[${e.attribute.id}]`,o=e.dateTimeFormat&&e.dateTimeFormat.includes(" ")?e.dateTimeFormat.split(" ")[1]:void 0;function i(i=""){return l.default.createElement(u.default,{id:a,name:n,className:`form-control ${i}`,selected:t,onChange:t=>{(t instanceof Date||null===t)&&(r(t),e.onChange(t?[(0,c.format)(t,"yyyy/MM/dd HH:mm")]:[""]))},dateFormat:e.dateTimeFormat||"Pp",timeFormat:o,calendarStartDay:e.firstDayOfWeek,locale:e.lang,showTimeSelect:!0,disabled:e.comparison===d.ComparisonType.Empty,isClearable:!0})}return e.isBasicSearch?l.default.createElement("div",{className:"attribute"},l.default.createElement("label",{htmlFor:`attribute-${e.attribute.id}`,className:"form-label"},e.attribute.label),i()):l.default.createElement("div",{className:"attribute search-date attribute"},l.default.createElement("label",{htmlFor:a,className:"form-label"},e.attribute.label),l.default.createElement("div",{className:"input-group"},l.default.createElement("select",{className:"form-select select-comparison-type",id:e.id?`date-compare-attribute-search-${e.id}`:`date-compare-attribute-search-${e.attribute.id}`,disabled:e.comparison===d.ComparisonType.Empty,"aria-label":"Comparison Type",value:e.comparison,onChange:t=>e.onComparisonChanged(Number.parseInt(t.target.value))},l.default.createElement("option",{value:d.ComparisonType.Equals,title:"Equals"},"="),l.default.createElement("option",{value:d.ComparisonType.GreaterThan,title:"Greater Than"},">"),l.default.createElement("option",{value:d.ComparisonType.LessThan,title:"Less Than"},"<")),i("search-date")),l.default.createElement(s.CustomAttributeSearchNoValueCheckbox,{attribute:e.attribute,comparison:e.comparison,onChange:e.onComparisonChanged}))}},1494:function(e,t,r){"use strict";var a=this&&this.__importDefault||function(e){return e&&e.__esModule?e:{default:e}};Object.defineProperty(t,"__esModule",{value:!0}),t.CustomAttributeSearchNoValueCheckbox=void 0;const n=a(r(67294)),o=r(81468),i=r(68960);t.CustomAttributeSearchNoValueCheckbox=function(e){const{t}=(0,o.useTranslation)();return n.default.createElement("div",{className:"form-check"},n.default.createElement("input",{type:"checkbox",className:"form-check-input",id:`attribute-search-empty-${e.attribute.id}`,name:`psiattributeempty[${e.attribute.id}]`,checked:e.comparison===i.ComparisonType.Empty,onChange:t=>e.onChange(t.target.checked?i.ComparisonType.Empty:i.ComparisonType.Equals)}),n.default.createElement("label",{className:"form-check-label",htmlFor:`attribute-search-empty-${e.attribute.id}`},t("HasNoValue")))}},9305:function(e,t,r){"use strict";var a=this&&this.__importDefault||function(e){return e&&e.__esModule?e:{default:e}};Object.defineProperty(t,"__esModule",{value:!0}),t.CustomAttributeSearchSelect=void 0;const n=a(r(67294)),o=a(r(23157)),i=r(1494),l=r(68960);t.CustomAttributeSearchSelect=function(e){const t=e.attribute.possibleValues.map((e=>({label:e,value:e}))),r=e.id||`attribute-search-${e.attribute.id}`,a=`${e.namePrefix||""}psiattribute[${e.attribute.id}]`;let u=!1;return e.isBasicSearch||void 0!==e.allowMultipleValues&&!e.allowMultipleValues||(u=!0),n.default.createElement("div",{className:"attribute"},n.default.createElement("label",{className:"form-label",htmlFor:r},e.attribute.label),n.default.createElement(o.default,{id:r,name:a,isClearable:!0,isMulti:u,options:t,getOptionValue:e=>e.value,getOptionLabel:e=>e.label,value:t.filter((t=>e.value.includes(t.value))),isDisabled:!e.isBasicSearch&&e.comparison!==l.ComparisonType.Equals,onChange:t=>{var r;r=u&&null!==t?t.map((e=>e.value)):t?[t.value]:null,e.onChange(null===r?[]:r)}}),!e.isBasicSearch&&n.default.createElement(i.CustomAttributeSearchNoValueCheckbox,{attribute:e.attribute,comparison:e.comparison,onChange:e.onComparisonChanged}))}},3717:function(e,t,r){"use strict";var a=this&&this.__importDefault||function(e){return e&&e.__esModule?e:{default:e}};Object.defineProperty(t,"__esModule",{value:!0}),t.CustomAttributeSearchTextarea=void 0;const n=a(r(67294)),o=r(1494),i=r(68960);t.CustomAttributeSearchTextarea=function(e){const t=e.id||`attribute-search-${e.attribute.id}`,r=`${e.namePrefix||""}psiattribute[${e.attribute.id}]`;return n.default.createElement("div",{className:"attribute clearable"},n.default.createElement("label",{className:"form-label",htmlFor:t},e.attribute.label),n.default.createElement("textarea",{id:t,name:r,className:"form-control",value:e.value[0]||"",disabled:e.comparison!==i.ComparisonType.Equals,onChange:t=>{return r=t.target.value,void e.onChange([r]);var r}}),n.default.createElement("i",{className:"clearable__clear",style:{display:""===e.value[0]?"none":"block"},onClick:t=>{e.onChange([""]),t.preventDefault(),t.stopPropagation()}},"×"),!e.isBasicSearch&&n.default.createElement(o.CustomAttributeSearchNoValueCheckbox,{attribute:e.attribute,comparison:e.comparison,onChange:e.onComparisonChanged}))}},51812:function(e,t,r){"use strict";var a=this&&this.__importDefault||function(e){return e&&e.__esModule?e:{default:e}};Object.defineProperty(t,"__esModule",{value:!0}),t.CustomAttributeSearchTextbox=void 0;const n=a(r(67294)),o=r(1494),i=r(68960);t.CustomAttributeSearchTextbox=function(e){function t(t){e.onChange([t])}const r=e.id||`attribute-search-${e.attribute.id}`,a=`${e.namePrefix||""}psiattribute[${e.attribute.id}]`;function l(o){return n.default.createElement(n.default.Fragment,null,n.default.createElement("input",{id:r,name:a,type:"text",className:`form-control ${o}`,value:e.value[0]||"",disabled:e.comparison===i.ComparisonType.Empty,onChange:e=>t(e.target.value)}),n.default.createElement("i",{className:"clearable__clear",style:{display:""===e.value[0]?"none":"block"},onClick:e=>{t(""),e.preventDefault(),e.stopPropagation()}},"×"))}return e.isBasicSearch?n.default.createElement("div",{className:"attribute clearable"},n.default.createElement("label",{className:"form-label",htmlFor:r},e.attribute.label),l("")):n.default.createElement("div",{className:"attribute"},n.default.createElement("label",{className:"form-label",htmlFor:r},e.attribute.label),n.default.createElement("div",{className:"input-group"},n.default.createElement("select",{className:"form-select select-comparison-type",id:e.id?`text-compare-attribute-search-${e.id}`:`text-compare-attribute-search-${e.attribute.id}`,disabled:e.comparison===i.ComparisonType.Empty,"aria-label":"Comparison Type",value:e.comparison,onChange:t=>e.onComparisonChanged(Number.parseInt(t.target.value))},n.default.createElement("option",{value:i.ComparisonType.Equals,title:"Equals"},"="),n.default.createElement("option",{value:i.ComparisonType.Contains,title:"Contains"},"⊂")),n.default.createElement("div",{className:"flex-grow-1"},n.default.createElement("div",{className:"clearable"}," ",l("search-text")))),n.default.createElement(o.CustomAttributeSearchNoValueCheckbox,{attribute:e.attribute,comparison:e.comparison,onChange:e.onComparisonChanged}))}},97267:function(e,t,r){"use strict";var a=this&&this.__createBinding||(Object.create?function(e,t,r,a){void 0===a&&(a=r);var n=Object.getOwnPropertyDescriptor(t,r);n&&!("get"in n?!t.__esModule:n.writable||n.configurable)||(n={enumerable:!0,get:function(){return t[r]}}),Object.defineProperty(e,a,n)}:function(e,t,r,a){void 0===a&&(a=r),e[a]=t[r]}),n=this&&this.__setModuleDefault||(Object.create?function(e,t){Object.defineProperty(e,"default",{enumerable:!0,value:t})}:function(e,t){e.default=t}),o=this&&this.__importStar||function(e){if(e&&e.__esModule)return e;var t={};if(null!=e)for(var r in e)"default"!==r&&Object.prototype.hasOwnProperty.call(e,r)&&a(t,e,r);return n(t,e),t};Object.defineProperty(t,"__esModule",{value:!0}),t.ResourceBrowserComponent=t.ResourceBrowser=t.useResourceBrowser=void 0;const i=o(r(67294)),l=o(r(43410)),u=r(989),c=r(40972);function s(e){const[t,r]=(0,i.useState)([]),[a,n]=(0,i.useState)([]),[o,u]=(0,i.useState)(),[c,s]=(0,i.useState)([]);return(0,i.useEffect)((()=>{e.api.getResourceGroupTree().then((t=>{if(r(t.resources),n(t.groups),t.groups.length>0){let r;e.defaultGroupId&&(r=function(e,t){var r;const a=[e],n=[e];for(;n.length>0;){const e=n.shift(),o=null===(r=t.find((t=>t.id===e)))||void 0===r?void 0:r.parentId;if(o){const e=Number.parseInt(o.toString());n.push(e),a.push(e)}}return a}(e.defaultGroupId,t.groups),s(r)),u(function(e,t,r){const a=e.filter((e=>null===e.parentId)).map((e=>`g${e.id}`)),n={};return n.root={id:"root",hasChildren:a.length>0,children:a,isExpanded:!0},e.forEach((a=>{n[`g${a.id}`]=function(a){const n=e.filter((e=>e.parentId===a.id)).map((e=>`g${e.id}`)),o=t.filter((e=>e.resourceGroupIds.includes(a.id))).map((e=>`r${e.id}g${a.id}`));return{id:`g${a.id}`,data:{title:a.name,isGroup:!0,id:a.id},isExpanded:(i=a.id,void 0!==r&&r.includes(i)),hasChildren:n.length>0||o.length>0,children:[...n,...o]};var i}(a)})),t.forEach((e=>{e.resourceGroupIds.forEach((t=>{n[`r${e.id}g${t}`]=function(e,t){return{id:`r${e.id}g${t}`,data:{title:e.name,isGroup:!1,id:e.id},isExpanded:!1,hasChildren:!1,children:[]}}(e,t)}))})),{rootId:"root",items:n}}(t.groups,t.resources,r))}return t})).catch((e=>console.error(e)))}),[]),{treeData:o,onExpand:function(e){if(o){const t=Number.parseInt(e.toString().replace("g",""));s([...c,t]),u((0,l.mutateTree)(o,e,{isExpanded:!0}))}},onCollapse:function(e){if(o){const t=Number.parseInt(e.toString().replace("g",""));s(c.filter((e=>e!==t))),u((0,l.mutateTree)(o,e,{isExpanded:!1}))}},onClickItem:function(r){if(r.data.isGroup){const t=a.find((e=>e.id===r.data.id));t&&e.onGroupSelected(t)}else{const a=t.find((e=>e.id===r.data.id));a&&e.onResourceSelected(a)}}}}function d(e){const{treeData:t,onExpand:r,onCollapse:a,onClickItem:n}=s(e);return t?i.default.createElement(i.default.Fragment,null,i.default.createElement("div",null,i.default.createElement(l.default,{isDragEnabled:!1,tree:t,renderItem:({item:e,provided:t})=>{const o=e.data.isGroup?"group":"resource";return i.default.createElement("div",Object.assign({ref:t.innerRef},t.draggableProps,t.dragHandleProps,{style:(l=t.draggableProps.style,Object.assign({overflow:"auto"},l)),className:`resource-group-tree-item ${o}`}),i.default.createElement(i.default.Fragment,null,(e=>e.children&&e.children.length>0?e.isExpanded?i.default.createElement("span",{role:"button",className:"bi bi-chevron-down resource-group-tree-navigation",onClick:()=>a(e.id)}):i.default.createElement("span",{role:"button",className:"bi bi-chevron-right resource-group-tree-navigation",onClick:()=>r(e.id)}):i.default.createElement("span",{role:"button",className:"bi bi-dot resource-group-tree-navigation"}))(e),i.default.createElement("div",{className:"d-inline-block"},i.default.createElement("button",{className:`btn btn-link ${o}`,onClick:()=>n(e)},e.data?e.data.title:""))));var l},onExpand:r,onCollapse:a,offsetPerLevel:15}))):i.default.createElement(i.default.Fragment,null)}t.useResourceBrowser=s,t.ResourceBrowser=d,t.ResourceBrowserComponent=function(e){const t=new c.ResourceTreeApi(e.path,e.csrf);return i.default.createElement(u.WebComponentWrapper,Object.assign({},e),i.default.createElement(d,Object.assign({},e,{api:t})))}},26002:function(e,t,r){"use strict";var a=this&&this.__createBinding||(Object.create?function(e,t,r,a){void 0===a&&(a=r);var n=Object.getOwnPropertyDescriptor(t,r);n&&!("get"in n?!t.__esModule:n.writable||n.configurable)||(n={enumerable:!0,get:function(){return t[r]}}),Object.defineProperty(e,a,n)}:function(e,t,r,a){void 0===a&&(a=r),e[a]=t[r]}),n=this&&this.__setModuleDefault||(Object.create?function(e,t){Object.defineProperty(e,"default",{enumerable:!0,value:t})}:function(e,t){e.default=t}),o=this&&this.__importStar||function(e){if(e&&e.__esModule)return e;var t={};if(null!=e)for(var r in e)"default"!==r&&Object.prototype.hasOwnProperty.call(e,r)&&a(t,e,r);return n(t,e),t},i=this&&this.__importDefault||function(e){return e&&e.__esModule?e:{default:e}};Object.defineProperty(t,"__esModule",{value:!0}),t.ResourcePicker=t.useResourcePicker=void 0;const l=o(r(67294)),u=o(r(43410)),c=i(r(23157)),s=r(81468),d=r(61630);function f(e){const{page:t,defaultResourceIds:r,api:a,resourceGroups:n,scheduleId:o,unavailableResourceIds:i,onResourcesChanged:c,checkboxThreshold:s}=e,[f,p]=(0,l.useState)([]),[m,b]=(0,l.useState)([]),[h,g]=(0,l.useState)(),[v,y]=(0,l.useState)((0,d.getUISettings)().resourcePickerVisibleSection[t]||0),C=(0,d.getUISettings)().resourcePickerExpandedGroups[t]||[],E=s||30;function _(e){if(!m.some((t=>t.id===e.id))){const t=[...m,e];b(t),c&&c(t)}}function O(e){const t=m.filter((t=>t.id!==e.id));b(t),c&&c(t)}function k(e,t){p(e),t.length>0&&g(function(e,t){const r=e.filter((e=>null===e.parentId)).map((e=>`g${e.id}`)),a={};return a.root={id:"root",hasChildren:r.length>0,children:r,isExpanded:!0},e.forEach((r=>{a[`g${r.id}`]=function(r){const a=e.filter((e=>e.parentId===r.id)).map((e=>`g${e.id}`)),n=t.filter((e=>e.resourceGroupIds.includes(r.id))).map((e=>`r${e.id}g${r.id}`));return{id:`g${r.id}`,data:{title:r.name,isGroup:!0,id:r.id},isExpanded:(o=r.id,void 0!==C&&C.includes(o)),hasChildren:a.length>0||n.length>0,children:[...a,...n]};var o}(r)})),t.forEach((e=>{e.resourceGroupIds.forEach((t=>{a[`r${e.id}g${t}`]=function(e,t){return{id:`r${e.id}g${t}`,data:{title:e.name,isGroup:!1,id:e.id,color:e.color,textColor:e.textColor},isExpanded:!1,hasChildren:!1,children:[]}}(e,t)}))})),{rootId:"root",items:a}}(t,e))}return(0,l.useEffect)((()=>{a&&o&&a.getResourceGroupTree(o).then((e=>(k(e.resources,e.groups),b(e.resources.filter((e=>r.includes(e.id)))),e))).catch((e=>{a.logError({action:"getResourceGroupTree",context:{scheduleId:o},error:e})})),e.resources&&n&&(k(e.resources,n),b(e.resources.filter((e=>r.includes(e.id)))))}),[]),{resources:f,treeData:h,visibleSection:v,allowTreeToggle:f.length>E&&void 0!==h,selectedResources:m,showCheckboxList:f.length<=E,isUnavailable:function(e){return null!=i&&i.includes(e)},onResourceSelected:_,onResourceRemoved:O,onVisibleSectionChanged:function(e){(0,d.persistUISettings)({resourcePickerVisibleSection:Object.assign(Object.assign({},(0,d.getUISettings)().resourcePickerVisibleSection),{[t]:e})}),y(e)},onExpand:function(e){if(h){const r=Number.parseInt(e.toString().replace("g",""));if(void 0===C)(0,d.persistUISettings)({resourcePickerExpandedGroups:Object.assign(Object.assign({},(0,d.getUISettings)().resourcePickerExpandedGroups),{[t]:[r]})});else{const e=[...C,r];(0,d.persistUISettings)({resourcePickerExpandedGroups:Object.assign(Object.assign({},(0,d.getUISettings)().resourcePickerExpandedGroups),{[t]:e})})}g((0,u.mutateTree)(h,e,{isExpanded:!0}))}},onCollapse:function(e){if(h){if(void 0!==C){const r=Number.parseInt(e.toString().replace("g",""));(0,d.persistUISettings)({resourcePickerExpandedGroups:Object.assign(Object.assign({},(0,d.getUISettings)().resourcePickerExpandedGroups),{[t]:C.filter((e=>e!==r))})})}g((0,u.mutateTree)(h,e,{isExpanded:!1}))}},isTreeItemChecked:function e(t){if(!h)return{checked:!1,indeterminate:!1};if(t.data.isGroup){if(0===t.children.length)return{checked:!1,indeterminate:!1};const r=t.children.every((t=>e(h.items[t]).checked));return{checked:r,indeterminate:t.children.some((t=>e(h.items[t]).checked))&&!r}}return{checked:m.some((e=>e.id===t.data.id)),indeterminate:!1}},onChangeTreeItem:function(e,t){if(h)if(e.data.isGroup){const r=[];!function e(t,r){f.filter((e=>e.resourceGroupIds.includes(t.data.id))).forEach((e=>{r.push(e)})),t.children.forEach((t=>{const a=h.items[t];a.data.isGroup&&e(a,r)}))}(e,r);let a=[];a=t?[...m,...r]:m.filter((e=>!r.some((t=>t.id===e.id)))),b(a),c&&c(a)}else{const r=f.find((t=>t.id===e.data.id));t?_(r):O(r)}}}}t.useResourcePicker=f,t.ResourcePicker=function(e){const{t}=(0,s.useTranslation)(),{treeData:r,resources:a,showCheckboxList:n,allowTreeToggle:o,selectedResources:i,visibleSection:d,isUnavailable:p,onVisibleSectionChanged:m,onResourceSelected:b,onResourceRemoved:h,onExpand:g,onCollapse:v,isTreeItemChecked:y,onChangeTreeItem:C}=f(e);function E(e){return d===e?"active":""}function _(e){return e.color?{backgroundColor:e.color,color:e.textColor||"#000"}:{}}return n?l.default.createElement("div",{className:"resource-picker"},a.map((e=>l.default.createElement("div",{key:e.id,className:"form-check"},l.default.createElement("label",{htmlFor:`resource-filter-${e.id}`,className:"form-check-label reservation-resource "+(p(e.id)?"unavailable-resource":""),style:_(e)},e.name),l.default.createElement("input",{type:"checkbox",className:"form-check-input",name:"rid[]",id:`resource-filter-${e.id}`,checked:i.some((t=>t.id===e.id)),value:e.id,onChange:t=>{t.target.checked?b(e):h(e)}}))))):l.default.createElement("div",{className:"resource-picker"},o&&l.default.createElement("div",null,l.default.createElement("ul",{className:"nav nav-tabs mb-2"},l.default.createElement("li",{className:"nav-item"},l.default.createElement("button",{type:"button",className:`nav-link ${E(0)}`,"aria-current":"page",onClick:()=>m(0)},t("Resources"))),l.default.createElement("li",{className:"nav-item"},l.default.createElement("button",{type:"button",className:`nav-link ${E(1)}`,onClick:()=>m(1)},t("Groups"))))),0===d&&l.default.createElement("div",null,l.default.createElement(c.default,{options:a.filter((e=>!i.some((t=>t.id===e.id)))),getOptionLabel:e=>e.name,getOptionValue:e=>e.id.toString(),onChange:e=>{e&&b(e)},isClearable:!0,placeholder:t("Resources"),menuPortalTarget:document.body,className:"mb-2",styles:{menuPortal:e=>Object.assign(Object.assign({},e),{zIndex:9999}),option:(e,{data:t})=>p(t.id)?Object.assign(Object.assign({},e),{color:"#AA3939",textDecoration:"line-through"}):Object.assign(Object.assign({},e),((e="transparent")=>({alignItems:"center",display:"flex",":before":{backgroundColor:e,borderRadius:10,content:'" "',display:"block",marginRight:8,height:10,width:10}}))(t.color))},value:null}),i.map((e=>l.default.createElement("div",{className:"selected-resource",key:e.id},l.default.createElement("button",{type:"button",className:"btn btn-link remove-button",title:t("Remove"),onClick:()=>h(e)},l.default.createElement("span",{className:"bi bi-x"})),l.default.createElement("div",{className:"form-check-label reservation-resource "+(p(e.id)?"unavailable-resource":""),style:_(e)},e.name),l.default.createElement("input",{type:"checkbox",className:"no-show",name:"rid[]",id:`resource-filter-${e.id}`,checked:!0,value:e.id,readOnly:!0}))))),1===d&&void 0!==r&&l.default.createElement("div",null,l.default.createElement(u.default,{isDragEnabled:!1,tree:r,renderItem:({item:e,provided:t})=>{const r=e.data.isGroup?"group":"resource",a=!e.data.isGroup&&p(e.data.id)?"unavailable-resource":"",n=e.data.isGroup?"reservation-group":"reservation-resource",o=y(e);let i={};return e.data.color&&(i={backgroundColor:e.data.color,color:e.data.textColor||"#000"}),l.default.createElement("div",Object.assign({ref:t.innerRef},t.draggableProps,t.dragHandleProps,{style:(u=t.draggableProps.style,Object.assign({overflow:"auto"},u)),className:`resource-group-tree-item ${r}`}),l.default.createElement(l.default.Fragment,null,(e=>e.children&&e.children.length>0?e.isExpanded?l.default.createElement("span",{role:"button",className:"bi bi-chevron-down resource-group-tree-navigation",onClick:()=>v(e.id)}):l.default.createElement("span",{role:"button",className:"bi bi-chevron-right resource-group-tree-navigation",onClick:()=>g(e.id)}):l.default.createElement("span",{role:"button",className:"resource-group-tree-navigation ms-3"}))(e),l.default.createElement("div",{className:"form-check d-inline-block"},l.default.createElement("input",{className:"form-check-input",type:"checkbox",value:e.data.id,id:`group-chk-${e.id}`,checked:o.checked,onChange:t=>C(e,t.target.checked),ref:e=>e&&(e.indeterminate=o.indeterminate)}),l.default.createElement("label",{className:`form-check-label ${n} ${a}`,htmlFor:`group-chk-${e.id}`,style:i},l.default.createElement("div",null,e.data?e.data.title:"")))));var u},onExpand:g,onCollapse:v,offsetPerLevel:15}),i.map((e=>l.default.createElement("input",{key:e.id,type:"hidden",name:"rid[]",value:e.id,readOnly:!0})))))}},18756:function(e,t,r){"use strict";var a=this&&this.__importDefault||function(e){return e&&e.__esModule?e:{default:e}};Object.defineProperty(t,"__esModule",{value:!0}),t.ResourcePickerComponent=void 0;const n=a(r(67294)),o=r(40972),i=r(989),l=r(26002);t.ResourcePickerComponent=function(e){const t=new o.ResourceTreeApi(e.path,e.csrf);return n.default.createElement(i.WebComponentWrapper,Object.assign({},e),n.default.createElement(l.ResourcePicker,Object.assign({},e,{api:t})))}},70527:function(e,t,r){"use strict";var a=this&&this.__createBinding||(Object.create?function(e,t,r,a){void 0===a&&(a=r);var n=Object.getOwnPropertyDescriptor(t,r);n&&!("get"in n?!t.__esModule:n.writable||n.configurable)||(n={enumerable:!0,get:function(){return t[r]}}),Object.defineProperty(e,a,n)}:function(e,t,r,a){void 0===a&&(a=r),e[a]=t[r]}),n=this&&this.__setModuleDefault||(Object.create?function(e,t){Object.defineProperty(e,"default",{enumerable:!0,value:t})}:function(e,t){e.default=t}),o=this&&this.__importStar||function(e){if(e&&e.__esModule)return e;var t={};if(null!=e)for(var r in e)"default"!==r&&Object.prototype.hasOwnProperty.call(e,r)&&a(t,e,r);return n(t,e),t},i=this&&this.__awaiter||function(e,t,r,a){return new(r||(r=Promise))((function(n,o){function i(e){try{u(a.next(e))}catch(e){o(e)}}function l(e){try{u(a.throw(e))}catch(e){o(e)}}function u(e){var t;e.done?n(e.value):(t=e.value,t instanceof r?t:new r((function(e){e(t)}))).then(i,l)}u((a=a.apply(e,t||[])).next())}))},l=this&&this.__importDefault||function(e){return e&&e.__esModule?e:{default:e}};Object.defineProperty(t,"__esModule",{value:!0}),t.UsersAutocomplete=void 0;const u=o(r(67294)),c=l(r(23157)),s=r(81902);class d extends s.Api{constructor(e,t,r){super(e,t),this.users=r}getUsers(e,t=!1){return i(this,void 0,void 0,(function*(){return 0===e.length&&this.users?this.users:this.get("api/user-autocomplete.php","users",{term:e,includeInactive:t})}))}}t.UsersAutocomplete=function(e){const t=new d(e.path,e.csrf,e.users),[r,a]=(0,u.useState)([]),[n,o]=(0,u.useState)(!1),[i,l]=(0,u.useState)();return(0,u.useEffect)((()=>{o(!0),t.getUsers("",e.includeInactive).then((t=>(a(t),l(t.find((t=>t.id===e.selectedId))),o(!1),t))).catch((e=>console.error(e)))}),[]),u.default.createElement(u.default.Fragment,null,e.label&&u.default.createElement("label",{className:"form-label",htmlFor:e.id},e.label),u.default.createElement(c.default,{id:e.id,placeholder:e.placeholder,options:r,isLoading:n,isClearable:!0,getOptionLabel:e=>{const t=e.fullName;return""!==e.email?`${t} (${e.email})`:t},getOptionValue:e=>e.id.toString(),onChange:t=>{l(t||void 0),e.onChange(t)},value:i}))}},989:function(e,t,r){"use strict";var a=this&&this.__createBinding||(Object.create?function(e,t,r,a){void 0===a&&(a=r);var n=Object.getOwnPropertyDescriptor(t,r);n&&!("get"in n?!t.__esModule:n.writable||n.configurable)||(n={enumerable:!0,get:function(){return t[r]}}),Object.defineProperty(e,a,n)}:function(e,t,r,a){void 0===a&&(a=r),e[a]=t[r]}),n=this&&this.__setModuleDefault||(Object.create?function(e,t){Object.defineProperty(e,"default",{enumerable:!0,value:t})}:function(e,t){e.default=t}),o=this&&this.__importStar||function(e){if(e&&e.__esModule)return e;var t={};if(null!=e)for(var r in e)"default"!==r&&Object.prototype.hasOwnProperty.call(e,r)&&a(t,e,r);return n(t,e),t},i=this&&this.__importDefault||function(e){return e&&e.__esModule?e:{default:e}};Object.defineProperty(t,"__esModule",{value:!0}),t.WebComponentWrapper=void 0;const l=o(r(67294)),u=i(r(23768)),c=r(51073);t.WebComponentWrapper=function(e){return(0,u.default)({lang:e.lang,basePath:e.path,version:e.version}),l.default.createElement(l.Suspense,{fallback:l.default.createElement("div",null,"Loading...")},l.default.createElement(c.AppSettingsProvider,Object.assign({},e),e.children))}},60535:function(e,t,r){"use strict";var a=this&&this.__importDefault||function(e){return e&&e.__esModule?e:{default:e}};Object.defineProperty(t,"__esModule",{value:!0});const n=a(r(67294)),o=r(20745),i=r(88281),l=r(97267),u=r(70527),c=r(18756),s=r(84228),d=r(50971);r.g.React=n.default,r.g.createRoot=o.createRoot,r.g.ReactComponents={BookedDatePicker:i.BookedDatePicker,ResourcePicker:c.ResourcePickerComponent,UsersAutocomplete:u.UsersAutocomplete,ResourceBrowser:l.ResourceBrowserComponent,BookedCopyToClipboard:s.BookedCopyToClipboard,BookedCustomAttribute:d.BookedCustomAttribute}}},e=>{e.O(0,[115,410,406,877],(()=>(60535,e(e.s=60535)))),e.O()}]);
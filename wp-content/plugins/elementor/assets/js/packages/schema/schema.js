/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/zod/v3/ZodError.js":
/*!*****************************************!*\
  !*** ./node_modules/zod/v3/ZodError.js ***!
  \*****************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ZodError: function() { return /* binding */ ZodError; },
/* harmony export */   ZodIssueCode: function() { return /* binding */ ZodIssueCode; },
/* harmony export */   quotelessJson: function() { return /* binding */ quotelessJson; }
/* harmony export */ });
/* harmony import */ var _helpers_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./helpers/util.js */ "./node_modules/zod/v3/helpers/util.js");

const ZodIssueCode = _helpers_util_js__WEBPACK_IMPORTED_MODULE_0__.util.arrayToEnum([
    "invalid_type",
    "invalid_literal",
    "custom",
    "invalid_union",
    "invalid_union_discriminator",
    "invalid_enum_value",
    "unrecognized_keys",
    "invalid_arguments",
    "invalid_return_type",
    "invalid_date",
    "invalid_string",
    "too_small",
    "too_big",
    "invalid_intersection_types",
    "not_multiple_of",
    "not_finite",
]);
const quotelessJson = (obj) => {
    const json = JSON.stringify(obj, null, 2);
    return json.replace(/"([^"]+)":/g, "$1:");
};
class ZodError extends Error {
    get errors() {
        return this.issues;
    }
    constructor(issues) {
        super();
        this.issues = [];
        this.addIssue = (sub) => {
            this.issues = [...this.issues, sub];
        };
        this.addIssues = (subs = []) => {
            this.issues = [...this.issues, ...subs];
        };
        const actualProto = new.target.prototype;
        if (Object.setPrototypeOf) {
            // eslint-disable-next-line ban/ban
            Object.setPrototypeOf(this, actualProto);
        }
        else {
            this.__proto__ = actualProto;
        }
        this.name = "ZodError";
        this.issues = issues;
    }
    format(_mapper) {
        const mapper = _mapper ||
            function (issue) {
                return issue.message;
            };
        const fieldErrors = { _errors: [] };
        const processError = (error) => {
            for (const issue of error.issues) {
                if (issue.code === "invalid_union") {
                    issue.unionErrors.map(processError);
                }
                else if (issue.code === "invalid_return_type") {
                    processError(issue.returnTypeError);
                }
                else if (issue.code === "invalid_arguments") {
                    processError(issue.argumentsError);
                }
                else if (issue.path.length === 0) {
                    fieldErrors._errors.push(mapper(issue));
                }
                else {
                    let curr = fieldErrors;
                    let i = 0;
                    while (i < issue.path.length) {
                        const el = issue.path[i];
                        const terminal = i === issue.path.length - 1;
                        if (!terminal) {
                            curr[el] = curr[el] || { _errors: [] };
                            // if (typeof el === "string") {
                            //   curr[el] = curr[el] || { _errors: [] };
                            // } else if (typeof el === "number") {
                            //   const errorArray: any = [];
                            //   errorArray._errors = [];
                            //   curr[el] = curr[el] || errorArray;
                            // }
                        }
                        else {
                            curr[el] = curr[el] || { _errors: [] };
                            curr[el]._errors.push(mapper(issue));
                        }
                        curr = curr[el];
                        i++;
                    }
                }
            }
        };
        processError(this);
        return fieldErrors;
    }
    static assert(value) {
        if (!(value instanceof ZodError)) {
            throw new Error(`Not a ZodError: ${value}`);
        }
    }
    toString() {
        return this.message;
    }
    get message() {
        return JSON.stringify(this.issues, _helpers_util_js__WEBPACK_IMPORTED_MODULE_0__.util.jsonStringifyReplacer, 2);
    }
    get isEmpty() {
        return this.issues.length === 0;
    }
    flatten(mapper = (issue) => issue.message) {
        const fieldErrors = {};
        const formErrors = [];
        for (const sub of this.issues) {
            if (sub.path.length > 0) {
                const firstEl = sub.path[0];
                fieldErrors[firstEl] = fieldErrors[firstEl] || [];
                fieldErrors[firstEl].push(mapper(sub));
            }
            else {
                formErrors.push(mapper(sub));
            }
        }
        return { formErrors, fieldErrors };
    }
    get formErrors() {
        return this.flatten();
    }
}
ZodError.create = (issues) => {
    const error = new ZodError(issues);
    return error;
};


/***/ }),

/***/ "./node_modules/zod/v3/errors.js":
/*!***************************************!*\
  !*** ./node_modules/zod/v3/errors.js ***!
  \***************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   defaultErrorMap: function() { return /* reexport safe */ _locales_en_js__WEBPACK_IMPORTED_MODULE_0__["default"]; },
/* harmony export */   getErrorMap: function() { return /* binding */ getErrorMap; },
/* harmony export */   setErrorMap: function() { return /* binding */ setErrorMap; }
/* harmony export */ });
/* harmony import */ var _locales_en_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./locales/en.js */ "./node_modules/zod/v3/locales/en.js");

let overrideErrorMap = _locales_en_js__WEBPACK_IMPORTED_MODULE_0__["default"];

function setErrorMap(map) {
    overrideErrorMap = map;
}
function getErrorMap() {
    return overrideErrorMap;
}


/***/ }),

/***/ "./node_modules/zod/v3/external.js":
/*!*****************************************!*\
  !*** ./node_modules/zod/v3/external.js ***!
  \*****************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BRAND: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.BRAND; },
/* harmony export */   DIRTY: function() { return /* reexport safe */ _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_1__.DIRTY; },
/* harmony export */   EMPTY_PATH: function() { return /* reexport safe */ _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_1__.EMPTY_PATH; },
/* harmony export */   INVALID: function() { return /* reexport safe */ _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_1__.INVALID; },
/* harmony export */   NEVER: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.NEVER; },
/* harmony export */   OK: function() { return /* reexport safe */ _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_1__.OK; },
/* harmony export */   ParseStatus: function() { return /* reexport safe */ _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_1__.ParseStatus; },
/* harmony export */   Schema: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.Schema; },
/* harmony export */   ZodAny: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodAny; },
/* harmony export */   ZodArray: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodArray; },
/* harmony export */   ZodBigInt: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodBigInt; },
/* harmony export */   ZodBoolean: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodBoolean; },
/* harmony export */   ZodBranded: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodBranded; },
/* harmony export */   ZodCatch: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodCatch; },
/* harmony export */   ZodDate: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodDate; },
/* harmony export */   ZodDefault: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodDefault; },
/* harmony export */   ZodDiscriminatedUnion: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodDiscriminatedUnion; },
/* harmony export */   ZodEffects: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodEffects; },
/* harmony export */   ZodEnum: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodEnum; },
/* harmony export */   ZodError: function() { return /* reexport safe */ _ZodError_js__WEBPACK_IMPORTED_MODULE_4__.ZodError; },
/* harmony export */   ZodFirstPartyTypeKind: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodFirstPartyTypeKind; },
/* harmony export */   ZodFunction: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodFunction; },
/* harmony export */   ZodIntersection: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodIntersection; },
/* harmony export */   ZodIssueCode: function() { return /* reexport safe */ _ZodError_js__WEBPACK_IMPORTED_MODULE_4__.ZodIssueCode; },
/* harmony export */   ZodLazy: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodLazy; },
/* harmony export */   ZodLiteral: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodLiteral; },
/* harmony export */   ZodMap: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodMap; },
/* harmony export */   ZodNaN: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodNaN; },
/* harmony export */   ZodNativeEnum: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodNativeEnum; },
/* harmony export */   ZodNever: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodNever; },
/* harmony export */   ZodNull: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodNull; },
/* harmony export */   ZodNullable: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodNullable; },
/* harmony export */   ZodNumber: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodNumber; },
/* harmony export */   ZodObject: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodObject; },
/* harmony export */   ZodOptional: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodOptional; },
/* harmony export */   ZodParsedType: function() { return /* reexport safe */ _helpers_util_js__WEBPACK_IMPORTED_MODULE_2__.ZodParsedType; },
/* harmony export */   ZodPipeline: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodPipeline; },
/* harmony export */   ZodPromise: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodPromise; },
/* harmony export */   ZodReadonly: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodReadonly; },
/* harmony export */   ZodRecord: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodRecord; },
/* harmony export */   ZodSchema: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodSchema; },
/* harmony export */   ZodSet: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodSet; },
/* harmony export */   ZodString: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodString; },
/* harmony export */   ZodSymbol: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodSymbol; },
/* harmony export */   ZodTransformer: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodTransformer; },
/* harmony export */   ZodTuple: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodTuple; },
/* harmony export */   ZodType: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodType; },
/* harmony export */   ZodUndefined: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodUndefined; },
/* harmony export */   ZodUnion: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodUnion; },
/* harmony export */   ZodUnknown: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodUnknown; },
/* harmony export */   ZodVoid: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ZodVoid; },
/* harmony export */   addIssueToContext: function() { return /* reexport safe */ _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_1__.addIssueToContext; },
/* harmony export */   any: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.any; },
/* harmony export */   array: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.array; },
/* harmony export */   bigint: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.bigint; },
/* harmony export */   boolean: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.boolean; },
/* harmony export */   coerce: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.coerce; },
/* harmony export */   custom: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.custom; },
/* harmony export */   date: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.date; },
/* harmony export */   datetimeRegex: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.datetimeRegex; },
/* harmony export */   defaultErrorMap: function() { return /* reexport safe */ _errors_js__WEBPACK_IMPORTED_MODULE_0__.defaultErrorMap; },
/* harmony export */   discriminatedUnion: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.discriminatedUnion; },
/* harmony export */   effect: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.effect; },
/* harmony export */   "enum": function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__["enum"]; },
/* harmony export */   "function": function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__["function"]; },
/* harmony export */   getErrorMap: function() { return /* reexport safe */ _errors_js__WEBPACK_IMPORTED_MODULE_0__.getErrorMap; },
/* harmony export */   getParsedType: function() { return /* reexport safe */ _helpers_util_js__WEBPACK_IMPORTED_MODULE_2__.getParsedType; },
/* harmony export */   "instanceof": function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__["instanceof"]; },
/* harmony export */   intersection: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.intersection; },
/* harmony export */   isAborted: function() { return /* reexport safe */ _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_1__.isAborted; },
/* harmony export */   isAsync: function() { return /* reexport safe */ _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_1__.isAsync; },
/* harmony export */   isDirty: function() { return /* reexport safe */ _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_1__.isDirty; },
/* harmony export */   isValid: function() { return /* reexport safe */ _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_1__.isValid; },
/* harmony export */   late: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.late; },
/* harmony export */   lazy: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.lazy; },
/* harmony export */   literal: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.literal; },
/* harmony export */   makeIssue: function() { return /* reexport safe */ _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_1__.makeIssue; },
/* harmony export */   map: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.map; },
/* harmony export */   nan: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.nan; },
/* harmony export */   nativeEnum: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.nativeEnum; },
/* harmony export */   never: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.never; },
/* harmony export */   "null": function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__["null"]; },
/* harmony export */   nullable: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.nullable; },
/* harmony export */   number: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.number; },
/* harmony export */   object: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.object; },
/* harmony export */   objectUtil: function() { return /* reexport safe */ _helpers_util_js__WEBPACK_IMPORTED_MODULE_2__.objectUtil; },
/* harmony export */   oboolean: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.oboolean; },
/* harmony export */   onumber: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.onumber; },
/* harmony export */   optional: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.optional; },
/* harmony export */   ostring: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.ostring; },
/* harmony export */   pipeline: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.pipeline; },
/* harmony export */   preprocess: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.preprocess; },
/* harmony export */   promise: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.promise; },
/* harmony export */   quotelessJson: function() { return /* reexport safe */ _ZodError_js__WEBPACK_IMPORTED_MODULE_4__.quotelessJson; },
/* harmony export */   record: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.record; },
/* harmony export */   set: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.set; },
/* harmony export */   setErrorMap: function() { return /* reexport safe */ _errors_js__WEBPACK_IMPORTED_MODULE_0__.setErrorMap; },
/* harmony export */   strictObject: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.strictObject; },
/* harmony export */   string: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.string; },
/* harmony export */   symbol: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.symbol; },
/* harmony export */   transformer: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.transformer; },
/* harmony export */   tuple: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.tuple; },
/* harmony export */   undefined: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.undefined; },
/* harmony export */   union: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.union; },
/* harmony export */   unknown: function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__.unknown; },
/* harmony export */   util: function() { return /* reexport safe */ _helpers_util_js__WEBPACK_IMPORTED_MODULE_2__.util; },
/* harmony export */   "void": function() { return /* reexport safe */ _types_js__WEBPACK_IMPORTED_MODULE_3__["void"]; }
/* harmony export */ });
/* harmony import */ var _errors_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./errors.js */ "./node_modules/zod/v3/errors.js");
/* harmony import */ var _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./helpers/parseUtil.js */ "./node_modules/zod/v3/helpers/parseUtil.js");
/* harmony import */ var _helpers_util_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./helpers/util.js */ "./node_modules/zod/v3/helpers/util.js");
/* harmony import */ var _types_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./types.js */ "./node_modules/zod/v3/types.js");
/* harmony import */ var _ZodError_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./ZodError.js */ "./node_modules/zod/v3/ZodError.js");








/***/ }),

/***/ "./node_modules/zod/v3/helpers/errorUtil.js":
/*!**************************************************!*\
  !*** ./node_modules/zod/v3/helpers/errorUtil.js ***!
  \**************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   errorUtil: function() { return /* binding */ errorUtil; }
/* harmony export */ });
var errorUtil;
(function (errorUtil) {
    errorUtil.errToObj = (message) => typeof message === "string" ? { message } : message || {};
    // biome-ignore lint:
    errorUtil.toString = (message) => typeof message === "string" ? message : message?.message;
})(errorUtil || (errorUtil = {}));


/***/ }),

/***/ "./node_modules/zod/v3/helpers/parseUtil.js":
/*!**************************************************!*\
  !*** ./node_modules/zod/v3/helpers/parseUtil.js ***!
  \**************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DIRTY: function() { return /* binding */ DIRTY; },
/* harmony export */   EMPTY_PATH: function() { return /* binding */ EMPTY_PATH; },
/* harmony export */   INVALID: function() { return /* binding */ INVALID; },
/* harmony export */   OK: function() { return /* binding */ OK; },
/* harmony export */   ParseStatus: function() { return /* binding */ ParseStatus; },
/* harmony export */   addIssueToContext: function() { return /* binding */ addIssueToContext; },
/* harmony export */   isAborted: function() { return /* binding */ isAborted; },
/* harmony export */   isAsync: function() { return /* binding */ isAsync; },
/* harmony export */   isDirty: function() { return /* binding */ isDirty; },
/* harmony export */   isValid: function() { return /* binding */ isValid; },
/* harmony export */   makeIssue: function() { return /* binding */ makeIssue; }
/* harmony export */ });
/* harmony import */ var _errors_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../errors.js */ "./node_modules/zod/v3/errors.js");
/* harmony import */ var _locales_en_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../locales/en.js */ "./node_modules/zod/v3/locales/en.js");


const makeIssue = (params) => {
    const { data, path, errorMaps, issueData } = params;
    const fullPath = [...path, ...(issueData.path || [])];
    const fullIssue = {
        ...issueData,
        path: fullPath,
    };
    if (issueData.message !== undefined) {
        return {
            ...issueData,
            path: fullPath,
            message: issueData.message,
        };
    }
    let errorMessage = "";
    const maps = errorMaps
        .filter((m) => !!m)
        .slice()
        .reverse();
    for (const map of maps) {
        errorMessage = map(fullIssue, { data, defaultError: errorMessage }).message;
    }
    return {
        ...issueData,
        path: fullPath,
        message: errorMessage,
    };
};
const EMPTY_PATH = [];
function addIssueToContext(ctx, issueData) {
    const overrideMap = (0,_errors_js__WEBPACK_IMPORTED_MODULE_0__.getErrorMap)();
    const issue = makeIssue({
        issueData: issueData,
        data: ctx.data,
        path: ctx.path,
        errorMaps: [
            ctx.common.contextualErrorMap, // contextual error map is first priority
            ctx.schemaErrorMap, // then schema-bound map if available
            overrideMap, // then global override map
            overrideMap === _locales_en_js__WEBPACK_IMPORTED_MODULE_1__["default"] ? undefined : _locales_en_js__WEBPACK_IMPORTED_MODULE_1__["default"], // then global default map
        ].filter((x) => !!x),
    });
    ctx.common.issues.push(issue);
}
class ParseStatus {
    constructor() {
        this.value = "valid";
    }
    dirty() {
        if (this.value === "valid")
            this.value = "dirty";
    }
    abort() {
        if (this.value !== "aborted")
            this.value = "aborted";
    }
    static mergeArray(status, results) {
        const arrayValue = [];
        for (const s of results) {
            if (s.status === "aborted")
                return INVALID;
            if (s.status === "dirty")
                status.dirty();
            arrayValue.push(s.value);
        }
        return { status: status.value, value: arrayValue };
    }
    static async mergeObjectAsync(status, pairs) {
        const syncPairs = [];
        for (const pair of pairs) {
            const key = await pair.key;
            const value = await pair.value;
            syncPairs.push({
                key,
                value,
            });
        }
        return ParseStatus.mergeObjectSync(status, syncPairs);
    }
    static mergeObjectSync(status, pairs) {
        const finalObject = {};
        for (const pair of pairs) {
            const { key, value } = pair;
            if (key.status === "aborted")
                return INVALID;
            if (value.status === "aborted")
                return INVALID;
            if (key.status === "dirty")
                status.dirty();
            if (value.status === "dirty")
                status.dirty();
            if (key.value !== "__proto__" && (typeof value.value !== "undefined" || pair.alwaysSet)) {
                finalObject[key.value] = value.value;
            }
        }
        return { status: status.value, value: finalObject };
    }
}
const INVALID = Object.freeze({
    status: "aborted",
});
const DIRTY = (value) => ({ status: "dirty", value });
const OK = (value) => ({ status: "valid", value });
const isAborted = (x) => x.status === "aborted";
const isDirty = (x) => x.status === "dirty";
const isValid = (x) => x.status === "valid";
const isAsync = (x) => typeof Promise !== "undefined" && x instanceof Promise;


/***/ }),

/***/ "./node_modules/zod/v3/helpers/util.js":
/*!*********************************************!*\
  !*** ./node_modules/zod/v3/helpers/util.js ***!
  \*********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ZodParsedType: function() { return /* binding */ ZodParsedType; },
/* harmony export */   getParsedType: function() { return /* binding */ getParsedType; },
/* harmony export */   objectUtil: function() { return /* binding */ objectUtil; },
/* harmony export */   util: function() { return /* binding */ util; }
/* harmony export */ });
var util;
(function (util) {
    util.assertEqual = (_) => { };
    function assertIs(_arg) { }
    util.assertIs = assertIs;
    function assertNever(_x) {
        throw new Error();
    }
    util.assertNever = assertNever;
    util.arrayToEnum = (items) => {
        const obj = {};
        for (const item of items) {
            obj[item] = item;
        }
        return obj;
    };
    util.getValidEnumValues = (obj) => {
        const validKeys = util.objectKeys(obj).filter((k) => typeof obj[obj[k]] !== "number");
        const filtered = {};
        for (const k of validKeys) {
            filtered[k] = obj[k];
        }
        return util.objectValues(filtered);
    };
    util.objectValues = (obj) => {
        return util.objectKeys(obj).map(function (e) {
            return obj[e];
        });
    };
    util.objectKeys = typeof Object.keys === "function" // eslint-disable-line ban/ban
        ? (obj) => Object.keys(obj) // eslint-disable-line ban/ban
        : (object) => {
            const keys = [];
            for (const key in object) {
                if (Object.prototype.hasOwnProperty.call(object, key)) {
                    keys.push(key);
                }
            }
            return keys;
        };
    util.find = (arr, checker) => {
        for (const item of arr) {
            if (checker(item))
                return item;
        }
        return undefined;
    };
    util.isInteger = typeof Number.isInteger === "function"
        ? (val) => Number.isInteger(val) // eslint-disable-line ban/ban
        : (val) => typeof val === "number" && Number.isFinite(val) && Math.floor(val) === val;
    function joinValues(array, separator = " | ") {
        return array.map((val) => (typeof val === "string" ? `'${val}'` : val)).join(separator);
    }
    util.joinValues = joinValues;
    util.jsonStringifyReplacer = (_, value) => {
        if (typeof value === "bigint") {
            return value.toString();
        }
        return value;
    };
})(util || (util = {}));
var objectUtil;
(function (objectUtil) {
    objectUtil.mergeShapes = (first, second) => {
        return {
            ...first,
            ...second, // second overwrites first
        };
    };
})(objectUtil || (objectUtil = {}));
const ZodParsedType = util.arrayToEnum([
    "string",
    "nan",
    "number",
    "integer",
    "float",
    "boolean",
    "date",
    "bigint",
    "symbol",
    "function",
    "undefined",
    "null",
    "array",
    "object",
    "unknown",
    "promise",
    "void",
    "never",
    "map",
    "set",
]);
const getParsedType = (data) => {
    const t = typeof data;
    switch (t) {
        case "undefined":
            return ZodParsedType.undefined;
        case "string":
            return ZodParsedType.string;
        case "number":
            return Number.isNaN(data) ? ZodParsedType.nan : ZodParsedType.number;
        case "boolean":
            return ZodParsedType.boolean;
        case "function":
            return ZodParsedType.function;
        case "bigint":
            return ZodParsedType.bigint;
        case "symbol":
            return ZodParsedType.symbol;
        case "object":
            if (Array.isArray(data)) {
                return ZodParsedType.array;
            }
            if (data === null) {
                return ZodParsedType.null;
            }
            if (data.then && typeof data.then === "function" && data.catch && typeof data.catch === "function") {
                return ZodParsedType.promise;
            }
            if (typeof Map !== "undefined" && data instanceof Map) {
                return ZodParsedType.map;
            }
            if (typeof Set !== "undefined" && data instanceof Set) {
                return ZodParsedType.set;
            }
            if (typeof Date !== "undefined" && data instanceof Date) {
                return ZodParsedType.date;
            }
            return ZodParsedType.object;
        default:
            return ZodParsedType.unknown;
    }
};


/***/ }),

/***/ "./node_modules/zod/v3/locales/en.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v3/locales/en.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _ZodError_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../ZodError.js */ "./node_modules/zod/v3/ZodError.js");
/* harmony import */ var _helpers_util_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../helpers/util.js */ "./node_modules/zod/v3/helpers/util.js");


const errorMap = (issue, _ctx) => {
    let message;
    switch (issue.code) {
        case _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type:
            if (issue.received === _helpers_util_js__WEBPACK_IMPORTED_MODULE_1__.ZodParsedType.undefined) {
                message = "Required";
            }
            else {
                message = `Expected ${issue.expected}, received ${issue.received}`;
            }
            break;
        case _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_literal:
            message = `Invalid literal value, expected ${JSON.stringify(issue.expected, _helpers_util_js__WEBPACK_IMPORTED_MODULE_1__.util.jsonStringifyReplacer)}`;
            break;
        case _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.unrecognized_keys:
            message = `Unrecognized key(s) in object: ${_helpers_util_js__WEBPACK_IMPORTED_MODULE_1__.util.joinValues(issue.keys, ", ")}`;
            break;
        case _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_union:
            message = `Invalid input`;
            break;
        case _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_union_discriminator:
            message = `Invalid discriminator value. Expected ${_helpers_util_js__WEBPACK_IMPORTED_MODULE_1__.util.joinValues(issue.options)}`;
            break;
        case _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_enum_value:
            message = `Invalid enum value. Expected ${_helpers_util_js__WEBPACK_IMPORTED_MODULE_1__.util.joinValues(issue.options)}, received '${issue.received}'`;
            break;
        case _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_arguments:
            message = `Invalid function arguments`;
            break;
        case _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_return_type:
            message = `Invalid function return type`;
            break;
        case _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_date:
            message = `Invalid date`;
            break;
        case _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string:
            if (typeof issue.validation === "object") {
                if ("includes" in issue.validation) {
                    message = `Invalid input: must include "${issue.validation.includes}"`;
                    if (typeof issue.validation.position === "number") {
                        message = `${message} at one or more positions greater than or equal to ${issue.validation.position}`;
                    }
                }
                else if ("startsWith" in issue.validation) {
                    message = `Invalid input: must start with "${issue.validation.startsWith}"`;
                }
                else if ("endsWith" in issue.validation) {
                    message = `Invalid input: must end with "${issue.validation.endsWith}"`;
                }
                else {
                    _helpers_util_js__WEBPACK_IMPORTED_MODULE_1__.util.assertNever(issue.validation);
                }
            }
            else if (issue.validation !== "regex") {
                message = `Invalid ${issue.validation}`;
            }
            else {
                message = "Invalid";
            }
            break;
        case _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_small:
            if (issue.type === "array")
                message = `Array must contain ${issue.exact ? "exactly" : issue.inclusive ? `at least` : `more than`} ${issue.minimum} element(s)`;
            else if (issue.type === "string")
                message = `String must contain ${issue.exact ? "exactly" : issue.inclusive ? `at least` : `over`} ${issue.minimum} character(s)`;
            else if (issue.type === "number")
                message = `Number must be ${issue.exact ? `exactly equal to ` : issue.inclusive ? `greater than or equal to ` : `greater than `}${issue.minimum}`;
            else if (issue.type === "bigint")
                message = `Number must be ${issue.exact ? `exactly equal to ` : issue.inclusive ? `greater than or equal to ` : `greater than `}${issue.minimum}`;
            else if (issue.type === "date")
                message = `Date must be ${issue.exact ? `exactly equal to ` : issue.inclusive ? `greater than or equal to ` : `greater than `}${new Date(Number(issue.minimum))}`;
            else
                message = "Invalid input";
            break;
        case _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_big:
            if (issue.type === "array")
                message = `Array must contain ${issue.exact ? `exactly` : issue.inclusive ? `at most` : `less than`} ${issue.maximum} element(s)`;
            else if (issue.type === "string")
                message = `String must contain ${issue.exact ? `exactly` : issue.inclusive ? `at most` : `under`} ${issue.maximum} character(s)`;
            else if (issue.type === "number")
                message = `Number must be ${issue.exact ? `exactly` : issue.inclusive ? `less than or equal to` : `less than`} ${issue.maximum}`;
            else if (issue.type === "bigint")
                message = `BigInt must be ${issue.exact ? `exactly` : issue.inclusive ? `less than or equal to` : `less than`} ${issue.maximum}`;
            else if (issue.type === "date")
                message = `Date must be ${issue.exact ? `exactly` : issue.inclusive ? `smaller than or equal to` : `smaller than`} ${new Date(Number(issue.maximum))}`;
            else
                message = "Invalid input";
            break;
        case _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.custom:
            message = `Invalid input`;
            break;
        case _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_intersection_types:
            message = `Intersection results could not be merged`;
            break;
        case _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.not_multiple_of:
            message = `Number must be a multiple of ${issue.multipleOf}`;
            break;
        case _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.not_finite:
            message = "Number must be finite";
            break;
        default:
            message = _ctx.defaultError;
            _helpers_util_js__WEBPACK_IMPORTED_MODULE_1__.util.assertNever(issue);
    }
    return { message };
};
/* harmony default export */ __webpack_exports__["default"] = (errorMap);


/***/ }),

/***/ "./node_modules/zod/v3/types.js":
/*!**************************************!*\
  !*** ./node_modules/zod/v3/types.js ***!
  \**************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BRAND: function() { return /* binding */ BRAND; },
/* harmony export */   NEVER: function() { return /* binding */ NEVER; },
/* harmony export */   Schema: function() { return /* binding */ ZodType; },
/* harmony export */   ZodAny: function() { return /* binding */ ZodAny; },
/* harmony export */   ZodArray: function() { return /* binding */ ZodArray; },
/* harmony export */   ZodBigInt: function() { return /* binding */ ZodBigInt; },
/* harmony export */   ZodBoolean: function() { return /* binding */ ZodBoolean; },
/* harmony export */   ZodBranded: function() { return /* binding */ ZodBranded; },
/* harmony export */   ZodCatch: function() { return /* binding */ ZodCatch; },
/* harmony export */   ZodDate: function() { return /* binding */ ZodDate; },
/* harmony export */   ZodDefault: function() { return /* binding */ ZodDefault; },
/* harmony export */   ZodDiscriminatedUnion: function() { return /* binding */ ZodDiscriminatedUnion; },
/* harmony export */   ZodEffects: function() { return /* binding */ ZodEffects; },
/* harmony export */   ZodEnum: function() { return /* binding */ ZodEnum; },
/* harmony export */   ZodFirstPartyTypeKind: function() { return /* binding */ ZodFirstPartyTypeKind; },
/* harmony export */   ZodFunction: function() { return /* binding */ ZodFunction; },
/* harmony export */   ZodIntersection: function() { return /* binding */ ZodIntersection; },
/* harmony export */   ZodLazy: function() { return /* binding */ ZodLazy; },
/* harmony export */   ZodLiteral: function() { return /* binding */ ZodLiteral; },
/* harmony export */   ZodMap: function() { return /* binding */ ZodMap; },
/* harmony export */   ZodNaN: function() { return /* binding */ ZodNaN; },
/* harmony export */   ZodNativeEnum: function() { return /* binding */ ZodNativeEnum; },
/* harmony export */   ZodNever: function() { return /* binding */ ZodNever; },
/* harmony export */   ZodNull: function() { return /* binding */ ZodNull; },
/* harmony export */   ZodNullable: function() { return /* binding */ ZodNullable; },
/* harmony export */   ZodNumber: function() { return /* binding */ ZodNumber; },
/* harmony export */   ZodObject: function() { return /* binding */ ZodObject; },
/* harmony export */   ZodOptional: function() { return /* binding */ ZodOptional; },
/* harmony export */   ZodPipeline: function() { return /* binding */ ZodPipeline; },
/* harmony export */   ZodPromise: function() { return /* binding */ ZodPromise; },
/* harmony export */   ZodReadonly: function() { return /* binding */ ZodReadonly; },
/* harmony export */   ZodRecord: function() { return /* binding */ ZodRecord; },
/* harmony export */   ZodSchema: function() { return /* binding */ ZodType; },
/* harmony export */   ZodSet: function() { return /* binding */ ZodSet; },
/* harmony export */   ZodString: function() { return /* binding */ ZodString; },
/* harmony export */   ZodSymbol: function() { return /* binding */ ZodSymbol; },
/* harmony export */   ZodTransformer: function() { return /* binding */ ZodEffects; },
/* harmony export */   ZodTuple: function() { return /* binding */ ZodTuple; },
/* harmony export */   ZodType: function() { return /* binding */ ZodType; },
/* harmony export */   ZodUndefined: function() { return /* binding */ ZodUndefined; },
/* harmony export */   ZodUnion: function() { return /* binding */ ZodUnion; },
/* harmony export */   ZodUnknown: function() { return /* binding */ ZodUnknown; },
/* harmony export */   ZodVoid: function() { return /* binding */ ZodVoid; },
/* harmony export */   any: function() { return /* binding */ anyType; },
/* harmony export */   array: function() { return /* binding */ arrayType; },
/* harmony export */   bigint: function() { return /* binding */ bigIntType; },
/* harmony export */   boolean: function() { return /* binding */ booleanType; },
/* harmony export */   coerce: function() { return /* binding */ coerce; },
/* harmony export */   custom: function() { return /* binding */ custom; },
/* harmony export */   date: function() { return /* binding */ dateType; },
/* harmony export */   datetimeRegex: function() { return /* binding */ datetimeRegex; },
/* harmony export */   discriminatedUnion: function() { return /* binding */ discriminatedUnionType; },
/* harmony export */   effect: function() { return /* binding */ effectsType; },
/* harmony export */   "enum": function() { return /* binding */ enumType; },
/* harmony export */   "function": function() { return /* binding */ functionType; },
/* harmony export */   "instanceof": function() { return /* binding */ instanceOfType; },
/* harmony export */   intersection: function() { return /* binding */ intersectionType; },
/* harmony export */   late: function() { return /* binding */ late; },
/* harmony export */   lazy: function() { return /* binding */ lazyType; },
/* harmony export */   literal: function() { return /* binding */ literalType; },
/* harmony export */   map: function() { return /* binding */ mapType; },
/* harmony export */   nan: function() { return /* binding */ nanType; },
/* harmony export */   nativeEnum: function() { return /* binding */ nativeEnumType; },
/* harmony export */   never: function() { return /* binding */ neverType; },
/* harmony export */   "null": function() { return /* binding */ nullType; },
/* harmony export */   nullable: function() { return /* binding */ nullableType; },
/* harmony export */   number: function() { return /* binding */ numberType; },
/* harmony export */   object: function() { return /* binding */ objectType; },
/* harmony export */   oboolean: function() { return /* binding */ oboolean; },
/* harmony export */   onumber: function() { return /* binding */ onumber; },
/* harmony export */   optional: function() { return /* binding */ optionalType; },
/* harmony export */   ostring: function() { return /* binding */ ostring; },
/* harmony export */   pipeline: function() { return /* binding */ pipelineType; },
/* harmony export */   preprocess: function() { return /* binding */ preprocessType; },
/* harmony export */   promise: function() { return /* binding */ promiseType; },
/* harmony export */   record: function() { return /* binding */ recordType; },
/* harmony export */   set: function() { return /* binding */ setType; },
/* harmony export */   strictObject: function() { return /* binding */ strictObjectType; },
/* harmony export */   string: function() { return /* binding */ stringType; },
/* harmony export */   symbol: function() { return /* binding */ symbolType; },
/* harmony export */   transformer: function() { return /* binding */ effectsType; },
/* harmony export */   tuple: function() { return /* binding */ tupleType; },
/* harmony export */   undefined: function() { return /* binding */ undefinedType; },
/* harmony export */   union: function() { return /* binding */ unionType; },
/* harmony export */   unknown: function() { return /* binding */ unknownType; },
/* harmony export */   "void": function() { return /* binding */ voidType; }
/* harmony export */ });
/* harmony import */ var _ZodError_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ZodError.js */ "./node_modules/zod/v3/ZodError.js");
/* harmony import */ var _errors_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./errors.js */ "./node_modules/zod/v3/errors.js");
/* harmony import */ var _errors_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./errors.js */ "./node_modules/zod/v3/locales/en.js");
/* harmony import */ var _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./helpers/errorUtil.js */ "./node_modules/zod/v3/helpers/errorUtil.js");
/* harmony import */ var _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./helpers/parseUtil.js */ "./node_modules/zod/v3/helpers/parseUtil.js");
/* harmony import */ var _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./helpers/util.js */ "./node_modules/zod/v3/helpers/util.js");





class ParseInputLazyPath {
    constructor(parent, value, path, key) {
        this._cachedPath = [];
        this.parent = parent;
        this.data = value;
        this._path = path;
        this._key = key;
    }
    get path() {
        if (!this._cachedPath.length) {
            if (Array.isArray(this._key)) {
                this._cachedPath.push(...this._path, ...this._key);
            }
            else {
                this._cachedPath.push(...this._path, this._key);
            }
        }
        return this._cachedPath;
    }
}
const handleResult = (ctx, result) => {
    if ((0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.isValid)(result)) {
        return { success: true, data: result.value };
    }
    else {
        if (!ctx.common.issues.length) {
            throw new Error("Validation failed but no issues detected.");
        }
        return {
            success: false,
            get error() {
                if (this._error)
                    return this._error;
                const error = new _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodError(ctx.common.issues);
                this._error = error;
                return this._error;
            },
        };
    }
};
function processCreateParams(params) {
    if (!params)
        return {};
    const { errorMap, invalid_type_error, required_error, description } = params;
    if (errorMap && (invalid_type_error || required_error)) {
        throw new Error(`Can't use "invalid_type_error" or "required_error" in conjunction with custom error map.`);
    }
    if (errorMap)
        return { errorMap: errorMap, description };
    const customMap = (iss, ctx) => {
        const { message } = params;
        if (iss.code === "invalid_enum_value") {
            return { message: message ?? ctx.defaultError };
        }
        if (typeof ctx.data === "undefined") {
            return { message: message ?? required_error ?? ctx.defaultError };
        }
        if (iss.code !== "invalid_type")
            return { message: ctx.defaultError };
        return { message: message ?? invalid_type_error ?? ctx.defaultError };
    };
    return { errorMap: customMap, description };
}
class ZodType {
    get description() {
        return this._def.description;
    }
    _getType(input) {
        return (0,_helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.getParsedType)(input.data);
    }
    _getOrReturnCtx(input, ctx) {
        return (ctx || {
            common: input.parent.common,
            data: input.data,
            parsedType: (0,_helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.getParsedType)(input.data),
            schemaErrorMap: this._def.errorMap,
            path: input.path,
            parent: input.parent,
        });
    }
    _processInputParams(input) {
        return {
            status: new _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.ParseStatus(),
            ctx: {
                common: input.parent.common,
                data: input.data,
                parsedType: (0,_helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.getParsedType)(input.data),
                schemaErrorMap: this._def.errorMap,
                path: input.path,
                parent: input.parent,
            },
        };
    }
    _parseSync(input) {
        const result = this._parse(input);
        if ((0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.isAsync)(result)) {
            throw new Error("Synchronous parse encountered promise.");
        }
        return result;
    }
    _parseAsync(input) {
        const result = this._parse(input);
        return Promise.resolve(result);
    }
    parse(data, params) {
        const result = this.safeParse(data, params);
        if (result.success)
            return result.data;
        throw result.error;
    }
    safeParse(data, params) {
        const ctx = {
            common: {
                issues: [],
                async: params?.async ?? false,
                contextualErrorMap: params?.errorMap,
            },
            path: params?.path || [],
            schemaErrorMap: this._def.errorMap,
            parent: null,
            data,
            parsedType: (0,_helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.getParsedType)(data),
        };
        const result = this._parseSync({ data, path: ctx.path, parent: ctx });
        return handleResult(ctx, result);
    }
    "~validate"(data) {
        const ctx = {
            common: {
                issues: [],
                async: !!this["~standard"].async,
            },
            path: [],
            schemaErrorMap: this._def.errorMap,
            parent: null,
            data,
            parsedType: (0,_helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.getParsedType)(data),
        };
        if (!this["~standard"].async) {
            try {
                const result = this._parseSync({ data, path: [], parent: ctx });
                return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.isValid)(result)
                    ? {
                        value: result.value,
                    }
                    : {
                        issues: ctx.common.issues,
                    };
            }
            catch (err) {
                if (err?.message?.toLowerCase()?.includes("encountered")) {
                    this["~standard"].async = true;
                }
                ctx.common = {
                    issues: [],
                    async: true,
                };
            }
        }
        return this._parseAsync({ data, path: [], parent: ctx }).then((result) => (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.isValid)(result)
            ? {
                value: result.value,
            }
            : {
                issues: ctx.common.issues,
            });
    }
    async parseAsync(data, params) {
        const result = await this.safeParseAsync(data, params);
        if (result.success)
            return result.data;
        throw result.error;
    }
    async safeParseAsync(data, params) {
        const ctx = {
            common: {
                issues: [],
                contextualErrorMap: params?.errorMap,
                async: true,
            },
            path: params?.path || [],
            schemaErrorMap: this._def.errorMap,
            parent: null,
            data,
            parsedType: (0,_helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.getParsedType)(data),
        };
        const maybeAsyncResult = this._parse({ data, path: ctx.path, parent: ctx });
        const result = await ((0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.isAsync)(maybeAsyncResult) ? maybeAsyncResult : Promise.resolve(maybeAsyncResult));
        return handleResult(ctx, result);
    }
    refine(check, message) {
        const getIssueProperties = (val) => {
            if (typeof message === "string" || typeof message === "undefined") {
                return { message };
            }
            else if (typeof message === "function") {
                return message(val);
            }
            else {
                return message;
            }
        };
        return this._refinement((val, ctx) => {
            const result = check(val);
            const setError = () => ctx.addIssue({
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.custom,
                ...getIssueProperties(val),
            });
            if (typeof Promise !== "undefined" && result instanceof Promise) {
                return result.then((data) => {
                    if (!data) {
                        setError();
                        return false;
                    }
                    else {
                        return true;
                    }
                });
            }
            if (!result) {
                setError();
                return false;
            }
            else {
                return true;
            }
        });
    }
    refinement(check, refinementData) {
        return this._refinement((val, ctx) => {
            if (!check(val)) {
                ctx.addIssue(typeof refinementData === "function" ? refinementData(val, ctx) : refinementData);
                return false;
            }
            else {
                return true;
            }
        });
    }
    _refinement(refinement) {
        return new ZodEffects({
            schema: this,
            typeName: ZodFirstPartyTypeKind.ZodEffects,
            effect: { type: "refinement", refinement },
        });
    }
    superRefine(refinement) {
        return this._refinement(refinement);
    }
    constructor(def) {
        /** Alias of safeParseAsync */
        this.spa = this.safeParseAsync;
        this._def = def;
        this.parse = this.parse.bind(this);
        this.safeParse = this.safeParse.bind(this);
        this.parseAsync = this.parseAsync.bind(this);
        this.safeParseAsync = this.safeParseAsync.bind(this);
        this.spa = this.spa.bind(this);
        this.refine = this.refine.bind(this);
        this.refinement = this.refinement.bind(this);
        this.superRefine = this.superRefine.bind(this);
        this.optional = this.optional.bind(this);
        this.nullable = this.nullable.bind(this);
        this.nullish = this.nullish.bind(this);
        this.array = this.array.bind(this);
        this.promise = this.promise.bind(this);
        this.or = this.or.bind(this);
        this.and = this.and.bind(this);
        this.transform = this.transform.bind(this);
        this.brand = this.brand.bind(this);
        this.default = this.default.bind(this);
        this.catch = this.catch.bind(this);
        this.describe = this.describe.bind(this);
        this.pipe = this.pipe.bind(this);
        this.readonly = this.readonly.bind(this);
        this.isNullable = this.isNullable.bind(this);
        this.isOptional = this.isOptional.bind(this);
        this["~standard"] = {
            version: 1,
            vendor: "zod",
            validate: (data) => this["~validate"](data),
        };
    }
    optional() {
        return ZodOptional.create(this, this._def);
    }
    nullable() {
        return ZodNullable.create(this, this._def);
    }
    nullish() {
        return this.nullable().optional();
    }
    array() {
        return ZodArray.create(this);
    }
    promise() {
        return ZodPromise.create(this, this._def);
    }
    or(option) {
        return ZodUnion.create([this, option], this._def);
    }
    and(incoming) {
        return ZodIntersection.create(this, incoming, this._def);
    }
    transform(transform) {
        return new ZodEffects({
            ...processCreateParams(this._def),
            schema: this,
            typeName: ZodFirstPartyTypeKind.ZodEffects,
            effect: { type: "transform", transform },
        });
    }
    default(def) {
        const defaultValueFunc = typeof def === "function" ? def : () => def;
        return new ZodDefault({
            ...processCreateParams(this._def),
            innerType: this,
            defaultValue: defaultValueFunc,
            typeName: ZodFirstPartyTypeKind.ZodDefault,
        });
    }
    brand() {
        return new ZodBranded({
            typeName: ZodFirstPartyTypeKind.ZodBranded,
            type: this,
            ...processCreateParams(this._def),
        });
    }
    catch(def) {
        const catchValueFunc = typeof def === "function" ? def : () => def;
        return new ZodCatch({
            ...processCreateParams(this._def),
            innerType: this,
            catchValue: catchValueFunc,
            typeName: ZodFirstPartyTypeKind.ZodCatch,
        });
    }
    describe(description) {
        const This = this.constructor;
        return new This({
            ...this._def,
            description,
        });
    }
    pipe(target) {
        return ZodPipeline.create(this, target);
    }
    readonly() {
        return ZodReadonly.create(this);
    }
    isOptional() {
        return this.safeParse(undefined).success;
    }
    isNullable() {
        return this.safeParse(null).success;
    }
}
const cuidRegex = /^c[^\s-]{8,}$/i;
const cuid2Regex = /^[0-9a-z]+$/;
const ulidRegex = /^[0-9A-HJKMNP-TV-Z]{26}$/i;
// const uuidRegex =
//   /^([a-f0-9]{8}-[a-f0-9]{4}-[1-5][a-f0-9]{3}-[a-f0-9]{4}-[a-f0-9]{12}|00000000-0000-0000-0000-000000000000)$/i;
const uuidRegex = /^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/i;
const nanoidRegex = /^[a-z0-9_-]{21}$/i;
const jwtRegex = /^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]*$/;
const durationRegex = /^[-+]?P(?!$)(?:(?:[-+]?\d+Y)|(?:[-+]?\d+[.,]\d+Y$))?(?:(?:[-+]?\d+M)|(?:[-+]?\d+[.,]\d+M$))?(?:(?:[-+]?\d+W)|(?:[-+]?\d+[.,]\d+W$))?(?:(?:[-+]?\d+D)|(?:[-+]?\d+[.,]\d+D$))?(?:T(?=[\d+-])(?:(?:[-+]?\d+H)|(?:[-+]?\d+[.,]\d+H$))?(?:(?:[-+]?\d+M)|(?:[-+]?\d+[.,]\d+M$))?(?:[-+]?\d+(?:[.,]\d+)?S)?)??$/;
// from https://stackoverflow.com/a/46181/1550155
// old version: too slow, didn't support unicode
// const emailRegex = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i;
//old email regex
// const emailRegex = /^(([^<>()[\].,;:\s@"]+(\.[^<>()[\].,;:\s@"]+)*)|(".+"))@((?!-)([^<>()[\].,;:\s@"]+\.)+[^<>()[\].,;:\s@"]{1,})[^-<>()[\].,;:\s@"]$/i;
// eslint-disable-next-line
// const emailRegex =
//   /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[(((25[0-5])|(2[0-4][0-9])|(1[0-9]{2})|([0-9]{1,2}))\.){3}((25[0-5])|(2[0-4][0-9])|(1[0-9]{2})|([0-9]{1,2}))\])|(\[IPv6:(([a-f0-9]{1,4}:){7}|::([a-f0-9]{1,4}:){0,6}|([a-f0-9]{1,4}:){1}:([a-f0-9]{1,4}:){0,5}|([a-f0-9]{1,4}:){2}:([a-f0-9]{1,4}:){0,4}|([a-f0-9]{1,4}:){3}:([a-f0-9]{1,4}:){0,3}|([a-f0-9]{1,4}:){4}:([a-f0-9]{1,4}:){0,2}|([a-f0-9]{1,4}:){5}:([a-f0-9]{1,4}:){0,1})([a-f0-9]{1,4}|(((25[0-5])|(2[0-4][0-9])|(1[0-9]{2})|([0-9]{1,2}))\.){3}((25[0-5])|(2[0-4][0-9])|(1[0-9]{2})|([0-9]{1,2})))\])|([A-Za-z0-9]([A-Za-z0-9-]*[A-Za-z0-9])*(\.[A-Za-z]{2,})+))$/;
// const emailRegex =
//   /^[a-zA-Z0-9\.\!\#\$\%\&\'\*\+\/\=\?\^\_\`\{\|\}\~\-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
// const emailRegex =
//   /^(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$/i;
const emailRegex = /^(?!\.)(?!.*\.\.)([A-Z0-9_'+\-\.]*)[A-Z0-9_+-]@([A-Z0-9][A-Z0-9\-]*\.)+[A-Z]{2,}$/i;
// const emailRegex =
//   /^[a-z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-z0-9-]+(?:\.[a-z0-9\-]+)*$/i;
// from https://thekevinscott.com/emojis-in-javascript/#writing-a-regular-expression
const _emojiRegex = `^(\\p{Extended_Pictographic}|\\p{Emoji_Component})+$`;
let emojiRegex;
// faster, simpler, safer
const ipv4Regex = /^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])$/;
const ipv4CidrRegex = /^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\/(3[0-2]|[12]?[0-9])$/;
// const ipv6Regex =
// /^(([a-f0-9]{1,4}:){7}|::([a-f0-9]{1,4}:){0,6}|([a-f0-9]{1,4}:){1}:([a-f0-9]{1,4}:){0,5}|([a-f0-9]{1,4}:){2}:([a-f0-9]{1,4}:){0,4}|([a-f0-9]{1,4}:){3}:([a-f0-9]{1,4}:){0,3}|([a-f0-9]{1,4}:){4}:([a-f0-9]{1,4}:){0,2}|([a-f0-9]{1,4}:){5}:([a-f0-9]{1,4}:){0,1})([a-f0-9]{1,4}|(((25[0-5])|(2[0-4][0-9])|(1[0-9]{2})|([0-9]{1,2}))\.){3}((25[0-5])|(2[0-4][0-9])|(1[0-9]{2})|([0-9]{1,2})))$/;
const ipv6Regex = /^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$/;
const ipv6CidrRegex = /^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))\/(12[0-8]|1[01][0-9]|[1-9]?[0-9])$/;
// https://stackoverflow.com/questions/7860392/determine-if-string-is-in-base64-using-javascript
const base64Regex = /^([0-9a-zA-Z+/]{4})*(([0-9a-zA-Z+/]{2}==)|([0-9a-zA-Z+/]{3}=))?$/;
// https://base64.guru/standards/base64url
const base64urlRegex = /^([0-9a-zA-Z-_]{4})*(([0-9a-zA-Z-_]{2}(==)?)|([0-9a-zA-Z-_]{3}(=)?))?$/;
// simple
// const dateRegexSource = `\\d{4}-\\d{2}-\\d{2}`;
// no leap year validation
// const dateRegexSource = `\\d{4}-((0[13578]|10|12)-31|(0[13-9]|1[0-2])-30|(0[1-9]|1[0-2])-(0[1-9]|1\\d|2\\d))`;
// with leap year validation
const dateRegexSource = `((\\d\\d[2468][048]|\\d\\d[13579][26]|\\d\\d0[48]|[02468][048]00|[13579][26]00)-02-29|\\d{4}-((0[13578]|1[02])-(0[1-9]|[12]\\d|3[01])|(0[469]|11)-(0[1-9]|[12]\\d|30)|(02)-(0[1-9]|1\\d|2[0-8])))`;
const dateRegex = new RegExp(`^${dateRegexSource}$`);
function timeRegexSource(args) {
    let secondsRegexSource = `[0-5]\\d`;
    if (args.precision) {
        secondsRegexSource = `${secondsRegexSource}\\.\\d{${args.precision}}`;
    }
    else if (args.precision == null) {
        secondsRegexSource = `${secondsRegexSource}(\\.\\d+)?`;
    }
    const secondsQuantifier = args.precision ? "+" : "?"; // require seconds if precision is nonzero
    return `([01]\\d|2[0-3]):[0-5]\\d(:${secondsRegexSource})${secondsQuantifier}`;
}
function timeRegex(args) {
    return new RegExp(`^${timeRegexSource(args)}$`);
}
// Adapted from https://stackoverflow.com/a/3143231
function datetimeRegex(args) {
    let regex = `${dateRegexSource}T${timeRegexSource(args)}`;
    const opts = [];
    opts.push(args.local ? `Z?` : `Z`);
    if (args.offset)
        opts.push(`([+-]\\d{2}:?\\d{2})`);
    regex = `${regex}(${opts.join("|")})`;
    return new RegExp(`^${regex}$`);
}
function isValidIP(ip, version) {
    if ((version === "v4" || !version) && ipv4Regex.test(ip)) {
        return true;
    }
    if ((version === "v6" || !version) && ipv6Regex.test(ip)) {
        return true;
    }
    return false;
}
function isValidJWT(jwt, alg) {
    if (!jwtRegex.test(jwt))
        return false;
    try {
        const [header] = jwt.split(".");
        if (!header)
            return false;
        // Convert base64url to base64
        const base64 = header
            .replace(/-/g, "+")
            .replace(/_/g, "/")
            .padEnd(header.length + ((4 - (header.length % 4)) % 4), "=");
        const decoded = JSON.parse(atob(base64));
        if (typeof decoded !== "object" || decoded === null)
            return false;
        if ("typ" in decoded && decoded?.typ !== "JWT")
            return false;
        if (!decoded.alg)
            return false;
        if (alg && decoded.alg !== alg)
            return false;
        return true;
    }
    catch {
        return false;
    }
}
function isValidCidr(ip, version) {
    if ((version === "v4" || !version) && ipv4CidrRegex.test(ip)) {
        return true;
    }
    if ((version === "v6" || !version) && ipv6CidrRegex.test(ip)) {
        return true;
    }
    return false;
}
class ZodString extends ZodType {
    _parse(input) {
        if (this._def.coerce) {
            input.data = String(input.data);
        }
        const parsedType = this._getType(input);
        if (parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.string) {
            const ctx = this._getOrReturnCtx(input);
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.string,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        const status = new _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.ParseStatus();
        let ctx = undefined;
        for (const check of this._def.checks) {
            if (check.kind === "min") {
                if (input.data.length < check.value) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_small,
                        minimum: check.value,
                        type: "string",
                        inclusive: true,
                        exact: false,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "max") {
                if (input.data.length > check.value) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_big,
                        maximum: check.value,
                        type: "string",
                        inclusive: true,
                        exact: false,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "length") {
                const tooBig = input.data.length > check.value;
                const tooSmall = input.data.length < check.value;
                if (tooBig || tooSmall) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    if (tooBig) {
                        (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                            code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_big,
                            maximum: check.value,
                            type: "string",
                            inclusive: true,
                            exact: true,
                            message: check.message,
                        });
                    }
                    else if (tooSmall) {
                        (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                            code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_small,
                            minimum: check.value,
                            type: "string",
                            inclusive: true,
                            exact: true,
                            message: check.message,
                        });
                    }
                    status.dirty();
                }
            }
            else if (check.kind === "email") {
                if (!emailRegex.test(input.data)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        validation: "email",
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "emoji") {
                if (!emojiRegex) {
                    emojiRegex = new RegExp(_emojiRegex, "u");
                }
                if (!emojiRegex.test(input.data)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        validation: "emoji",
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "uuid") {
                if (!uuidRegex.test(input.data)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        validation: "uuid",
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "nanoid") {
                if (!nanoidRegex.test(input.data)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        validation: "nanoid",
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "cuid") {
                if (!cuidRegex.test(input.data)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        validation: "cuid",
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "cuid2") {
                if (!cuid2Regex.test(input.data)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        validation: "cuid2",
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "ulid") {
                if (!ulidRegex.test(input.data)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        validation: "ulid",
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "url") {
                try {
                    new URL(input.data);
                }
                catch {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        validation: "url",
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "regex") {
                check.regex.lastIndex = 0;
                const testResult = check.regex.test(input.data);
                if (!testResult) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        validation: "regex",
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "trim") {
                input.data = input.data.trim();
            }
            else if (check.kind === "includes") {
                if (!input.data.includes(check.value, check.position)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        validation: { includes: check.value, position: check.position },
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "toLowerCase") {
                input.data = input.data.toLowerCase();
            }
            else if (check.kind === "toUpperCase") {
                input.data = input.data.toUpperCase();
            }
            else if (check.kind === "startsWith") {
                if (!input.data.startsWith(check.value)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        validation: { startsWith: check.value },
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "endsWith") {
                if (!input.data.endsWith(check.value)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        validation: { endsWith: check.value },
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "datetime") {
                const regex = datetimeRegex(check);
                if (!regex.test(input.data)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        validation: "datetime",
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "date") {
                const regex = dateRegex;
                if (!regex.test(input.data)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        validation: "date",
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "time") {
                const regex = timeRegex(check);
                if (!regex.test(input.data)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        validation: "time",
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "duration") {
                if (!durationRegex.test(input.data)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        validation: "duration",
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "ip") {
                if (!isValidIP(input.data, check.version)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        validation: "ip",
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "jwt") {
                if (!isValidJWT(input.data, check.alg)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        validation: "jwt",
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "cidr") {
                if (!isValidCidr(input.data, check.version)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        validation: "cidr",
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "base64") {
                if (!base64Regex.test(input.data)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        validation: "base64",
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "base64url") {
                if (!base64urlRegex.test(input.data)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        validation: "base64url",
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else {
                _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.assertNever(check);
            }
        }
        return { status: status.value, value: input.data };
    }
    _regex(regex, validation, message) {
        return this.refinement((data) => regex.test(data), {
            validation,
            code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_string,
            ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message),
        });
    }
    _addCheck(check) {
        return new ZodString({
            ...this._def,
            checks: [...this._def.checks, check],
        });
    }
    email(message) {
        return this._addCheck({ kind: "email", ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message) });
    }
    url(message) {
        return this._addCheck({ kind: "url", ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message) });
    }
    emoji(message) {
        return this._addCheck({ kind: "emoji", ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message) });
    }
    uuid(message) {
        return this._addCheck({ kind: "uuid", ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message) });
    }
    nanoid(message) {
        return this._addCheck({ kind: "nanoid", ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message) });
    }
    cuid(message) {
        return this._addCheck({ kind: "cuid", ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message) });
    }
    cuid2(message) {
        return this._addCheck({ kind: "cuid2", ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message) });
    }
    ulid(message) {
        return this._addCheck({ kind: "ulid", ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message) });
    }
    base64(message) {
        return this._addCheck({ kind: "base64", ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message) });
    }
    base64url(message) {
        // base64url encoding is a modification of base64 that can safely be used in URLs and filenames
        return this._addCheck({
            kind: "base64url",
            ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message),
        });
    }
    jwt(options) {
        return this._addCheck({ kind: "jwt", ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(options) });
    }
    ip(options) {
        return this._addCheck({ kind: "ip", ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(options) });
    }
    cidr(options) {
        return this._addCheck({ kind: "cidr", ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(options) });
    }
    datetime(options) {
        if (typeof options === "string") {
            return this._addCheck({
                kind: "datetime",
                precision: null,
                offset: false,
                local: false,
                message: options,
            });
        }
        return this._addCheck({
            kind: "datetime",
            precision: typeof options?.precision === "undefined" ? null : options?.precision,
            offset: options?.offset ?? false,
            local: options?.local ?? false,
            ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(options?.message),
        });
    }
    date(message) {
        return this._addCheck({ kind: "date", message });
    }
    time(options) {
        if (typeof options === "string") {
            return this._addCheck({
                kind: "time",
                precision: null,
                message: options,
            });
        }
        return this._addCheck({
            kind: "time",
            precision: typeof options?.precision === "undefined" ? null : options?.precision,
            ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(options?.message),
        });
    }
    duration(message) {
        return this._addCheck({ kind: "duration", ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message) });
    }
    regex(regex, message) {
        return this._addCheck({
            kind: "regex",
            regex: regex,
            ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message),
        });
    }
    includes(value, options) {
        return this._addCheck({
            kind: "includes",
            value: value,
            position: options?.position,
            ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(options?.message),
        });
    }
    startsWith(value, message) {
        return this._addCheck({
            kind: "startsWith",
            value: value,
            ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message),
        });
    }
    endsWith(value, message) {
        return this._addCheck({
            kind: "endsWith",
            value: value,
            ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message),
        });
    }
    min(minLength, message) {
        return this._addCheck({
            kind: "min",
            value: minLength,
            ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message),
        });
    }
    max(maxLength, message) {
        return this._addCheck({
            kind: "max",
            value: maxLength,
            ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message),
        });
    }
    length(len, message) {
        return this._addCheck({
            kind: "length",
            value: len,
            ..._helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message),
        });
    }
    /**
     * Equivalent to `.min(1)`
     */
    nonempty(message) {
        return this.min(1, _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message));
    }
    trim() {
        return new ZodString({
            ...this._def,
            checks: [...this._def.checks, { kind: "trim" }],
        });
    }
    toLowerCase() {
        return new ZodString({
            ...this._def,
            checks: [...this._def.checks, { kind: "toLowerCase" }],
        });
    }
    toUpperCase() {
        return new ZodString({
            ...this._def,
            checks: [...this._def.checks, { kind: "toUpperCase" }],
        });
    }
    get isDatetime() {
        return !!this._def.checks.find((ch) => ch.kind === "datetime");
    }
    get isDate() {
        return !!this._def.checks.find((ch) => ch.kind === "date");
    }
    get isTime() {
        return !!this._def.checks.find((ch) => ch.kind === "time");
    }
    get isDuration() {
        return !!this._def.checks.find((ch) => ch.kind === "duration");
    }
    get isEmail() {
        return !!this._def.checks.find((ch) => ch.kind === "email");
    }
    get isURL() {
        return !!this._def.checks.find((ch) => ch.kind === "url");
    }
    get isEmoji() {
        return !!this._def.checks.find((ch) => ch.kind === "emoji");
    }
    get isUUID() {
        return !!this._def.checks.find((ch) => ch.kind === "uuid");
    }
    get isNANOID() {
        return !!this._def.checks.find((ch) => ch.kind === "nanoid");
    }
    get isCUID() {
        return !!this._def.checks.find((ch) => ch.kind === "cuid");
    }
    get isCUID2() {
        return !!this._def.checks.find((ch) => ch.kind === "cuid2");
    }
    get isULID() {
        return !!this._def.checks.find((ch) => ch.kind === "ulid");
    }
    get isIP() {
        return !!this._def.checks.find((ch) => ch.kind === "ip");
    }
    get isCIDR() {
        return !!this._def.checks.find((ch) => ch.kind === "cidr");
    }
    get isBase64() {
        return !!this._def.checks.find((ch) => ch.kind === "base64");
    }
    get isBase64url() {
        // base64url encoding is a modification of base64 that can safely be used in URLs and filenames
        return !!this._def.checks.find((ch) => ch.kind === "base64url");
    }
    get minLength() {
        let min = null;
        for (const ch of this._def.checks) {
            if (ch.kind === "min") {
                if (min === null || ch.value > min)
                    min = ch.value;
            }
        }
        return min;
    }
    get maxLength() {
        let max = null;
        for (const ch of this._def.checks) {
            if (ch.kind === "max") {
                if (max === null || ch.value < max)
                    max = ch.value;
            }
        }
        return max;
    }
}
ZodString.create = (params) => {
    return new ZodString({
        checks: [],
        typeName: ZodFirstPartyTypeKind.ZodString,
        coerce: params?.coerce ?? false,
        ...processCreateParams(params),
    });
};
// https://stackoverflow.com/questions/3966484/why-does-modulus-operator-return-fractional-number-in-javascript/31711034#31711034
function floatSafeRemainder(val, step) {
    const valDecCount = (val.toString().split(".")[1] || "").length;
    const stepDecCount = (step.toString().split(".")[1] || "").length;
    const decCount = valDecCount > stepDecCount ? valDecCount : stepDecCount;
    const valInt = Number.parseInt(val.toFixed(decCount).replace(".", ""));
    const stepInt = Number.parseInt(step.toFixed(decCount).replace(".", ""));
    return (valInt % stepInt) / 10 ** decCount;
}
class ZodNumber extends ZodType {
    constructor() {
        super(...arguments);
        this.min = this.gte;
        this.max = this.lte;
        this.step = this.multipleOf;
    }
    _parse(input) {
        if (this._def.coerce) {
            input.data = Number(input.data);
        }
        const parsedType = this._getType(input);
        if (parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.number) {
            const ctx = this._getOrReturnCtx(input);
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.number,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        let ctx = undefined;
        const status = new _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.ParseStatus();
        for (const check of this._def.checks) {
            if (check.kind === "int") {
                if (!_helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.isInteger(input.data)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                        expected: "integer",
                        received: "float",
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "min") {
                const tooSmall = check.inclusive ? input.data < check.value : input.data <= check.value;
                if (tooSmall) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_small,
                        minimum: check.value,
                        type: "number",
                        inclusive: check.inclusive,
                        exact: false,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "max") {
                const tooBig = check.inclusive ? input.data > check.value : input.data >= check.value;
                if (tooBig) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_big,
                        maximum: check.value,
                        type: "number",
                        inclusive: check.inclusive,
                        exact: false,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "multipleOf") {
                if (floatSafeRemainder(input.data, check.value) !== 0) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.not_multiple_of,
                        multipleOf: check.value,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "finite") {
                if (!Number.isFinite(input.data)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.not_finite,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else {
                _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.assertNever(check);
            }
        }
        return { status: status.value, value: input.data };
    }
    gte(value, message) {
        return this.setLimit("min", value, true, _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message));
    }
    gt(value, message) {
        return this.setLimit("min", value, false, _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message));
    }
    lte(value, message) {
        return this.setLimit("max", value, true, _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message));
    }
    lt(value, message) {
        return this.setLimit("max", value, false, _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message));
    }
    setLimit(kind, value, inclusive, message) {
        return new ZodNumber({
            ...this._def,
            checks: [
                ...this._def.checks,
                {
                    kind,
                    value,
                    inclusive,
                    message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
                },
            ],
        });
    }
    _addCheck(check) {
        return new ZodNumber({
            ...this._def,
            checks: [...this._def.checks, check],
        });
    }
    int(message) {
        return this._addCheck({
            kind: "int",
            message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
        });
    }
    positive(message) {
        return this._addCheck({
            kind: "min",
            value: 0,
            inclusive: false,
            message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
        });
    }
    negative(message) {
        return this._addCheck({
            kind: "max",
            value: 0,
            inclusive: false,
            message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
        });
    }
    nonpositive(message) {
        return this._addCheck({
            kind: "max",
            value: 0,
            inclusive: true,
            message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
        });
    }
    nonnegative(message) {
        return this._addCheck({
            kind: "min",
            value: 0,
            inclusive: true,
            message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
        });
    }
    multipleOf(value, message) {
        return this._addCheck({
            kind: "multipleOf",
            value: value,
            message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
        });
    }
    finite(message) {
        return this._addCheck({
            kind: "finite",
            message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
        });
    }
    safe(message) {
        return this._addCheck({
            kind: "min",
            inclusive: true,
            value: Number.MIN_SAFE_INTEGER,
            message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
        })._addCheck({
            kind: "max",
            inclusive: true,
            value: Number.MAX_SAFE_INTEGER,
            message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
        });
    }
    get minValue() {
        let min = null;
        for (const ch of this._def.checks) {
            if (ch.kind === "min") {
                if (min === null || ch.value > min)
                    min = ch.value;
            }
        }
        return min;
    }
    get maxValue() {
        let max = null;
        for (const ch of this._def.checks) {
            if (ch.kind === "max") {
                if (max === null || ch.value < max)
                    max = ch.value;
            }
        }
        return max;
    }
    get isInt() {
        return !!this._def.checks.find((ch) => ch.kind === "int" || (ch.kind === "multipleOf" && _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.isInteger(ch.value)));
    }
    get isFinite() {
        let max = null;
        let min = null;
        for (const ch of this._def.checks) {
            if (ch.kind === "finite" || ch.kind === "int" || ch.kind === "multipleOf") {
                return true;
            }
            else if (ch.kind === "min") {
                if (min === null || ch.value > min)
                    min = ch.value;
            }
            else if (ch.kind === "max") {
                if (max === null || ch.value < max)
                    max = ch.value;
            }
        }
        return Number.isFinite(min) && Number.isFinite(max);
    }
}
ZodNumber.create = (params) => {
    return new ZodNumber({
        checks: [],
        typeName: ZodFirstPartyTypeKind.ZodNumber,
        coerce: params?.coerce || false,
        ...processCreateParams(params),
    });
};
class ZodBigInt extends ZodType {
    constructor() {
        super(...arguments);
        this.min = this.gte;
        this.max = this.lte;
    }
    _parse(input) {
        if (this._def.coerce) {
            try {
                input.data = BigInt(input.data);
            }
            catch {
                return this._getInvalidInput(input);
            }
        }
        const parsedType = this._getType(input);
        if (parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.bigint) {
            return this._getInvalidInput(input);
        }
        let ctx = undefined;
        const status = new _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.ParseStatus();
        for (const check of this._def.checks) {
            if (check.kind === "min") {
                const tooSmall = check.inclusive ? input.data < check.value : input.data <= check.value;
                if (tooSmall) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_small,
                        type: "bigint",
                        minimum: check.value,
                        inclusive: check.inclusive,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "max") {
                const tooBig = check.inclusive ? input.data > check.value : input.data >= check.value;
                if (tooBig) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_big,
                        type: "bigint",
                        maximum: check.value,
                        inclusive: check.inclusive,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "multipleOf") {
                if (input.data % check.value !== BigInt(0)) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.not_multiple_of,
                        multipleOf: check.value,
                        message: check.message,
                    });
                    status.dirty();
                }
            }
            else {
                _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.assertNever(check);
            }
        }
        return { status: status.value, value: input.data };
    }
    _getInvalidInput(input) {
        const ctx = this._getOrReturnCtx(input);
        (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
            code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
            expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.bigint,
            received: ctx.parsedType,
        });
        return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
    }
    gte(value, message) {
        return this.setLimit("min", value, true, _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message));
    }
    gt(value, message) {
        return this.setLimit("min", value, false, _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message));
    }
    lte(value, message) {
        return this.setLimit("max", value, true, _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message));
    }
    lt(value, message) {
        return this.setLimit("max", value, false, _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message));
    }
    setLimit(kind, value, inclusive, message) {
        return new ZodBigInt({
            ...this._def,
            checks: [
                ...this._def.checks,
                {
                    kind,
                    value,
                    inclusive,
                    message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
                },
            ],
        });
    }
    _addCheck(check) {
        return new ZodBigInt({
            ...this._def,
            checks: [...this._def.checks, check],
        });
    }
    positive(message) {
        return this._addCheck({
            kind: "min",
            value: BigInt(0),
            inclusive: false,
            message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
        });
    }
    negative(message) {
        return this._addCheck({
            kind: "max",
            value: BigInt(0),
            inclusive: false,
            message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
        });
    }
    nonpositive(message) {
        return this._addCheck({
            kind: "max",
            value: BigInt(0),
            inclusive: true,
            message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
        });
    }
    nonnegative(message) {
        return this._addCheck({
            kind: "min",
            value: BigInt(0),
            inclusive: true,
            message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
        });
    }
    multipleOf(value, message) {
        return this._addCheck({
            kind: "multipleOf",
            value,
            message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
        });
    }
    get minValue() {
        let min = null;
        for (const ch of this._def.checks) {
            if (ch.kind === "min") {
                if (min === null || ch.value > min)
                    min = ch.value;
            }
        }
        return min;
    }
    get maxValue() {
        let max = null;
        for (const ch of this._def.checks) {
            if (ch.kind === "max") {
                if (max === null || ch.value < max)
                    max = ch.value;
            }
        }
        return max;
    }
}
ZodBigInt.create = (params) => {
    return new ZodBigInt({
        checks: [],
        typeName: ZodFirstPartyTypeKind.ZodBigInt,
        coerce: params?.coerce ?? false,
        ...processCreateParams(params),
    });
};
class ZodBoolean extends ZodType {
    _parse(input) {
        if (this._def.coerce) {
            input.data = Boolean(input.data);
        }
        const parsedType = this._getType(input);
        if (parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.boolean) {
            const ctx = this._getOrReturnCtx(input);
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.boolean,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.OK)(input.data);
    }
}
ZodBoolean.create = (params) => {
    return new ZodBoolean({
        typeName: ZodFirstPartyTypeKind.ZodBoolean,
        coerce: params?.coerce || false,
        ...processCreateParams(params),
    });
};
class ZodDate extends ZodType {
    _parse(input) {
        if (this._def.coerce) {
            input.data = new Date(input.data);
        }
        const parsedType = this._getType(input);
        if (parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.date) {
            const ctx = this._getOrReturnCtx(input);
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.date,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        if (Number.isNaN(input.data.getTime())) {
            const ctx = this._getOrReturnCtx(input);
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_date,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        const status = new _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.ParseStatus();
        let ctx = undefined;
        for (const check of this._def.checks) {
            if (check.kind === "min") {
                if (input.data.getTime() < check.value) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_small,
                        message: check.message,
                        inclusive: true,
                        exact: false,
                        minimum: check.value,
                        type: "date",
                    });
                    status.dirty();
                }
            }
            else if (check.kind === "max") {
                if (input.data.getTime() > check.value) {
                    ctx = this._getOrReturnCtx(input, ctx);
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_big,
                        message: check.message,
                        inclusive: true,
                        exact: false,
                        maximum: check.value,
                        type: "date",
                    });
                    status.dirty();
                }
            }
            else {
                _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.assertNever(check);
            }
        }
        return {
            status: status.value,
            value: new Date(input.data.getTime()),
        };
    }
    _addCheck(check) {
        return new ZodDate({
            ...this._def,
            checks: [...this._def.checks, check],
        });
    }
    min(minDate, message) {
        return this._addCheck({
            kind: "min",
            value: minDate.getTime(),
            message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
        });
    }
    max(maxDate, message) {
        return this._addCheck({
            kind: "max",
            value: maxDate.getTime(),
            message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message),
        });
    }
    get minDate() {
        let min = null;
        for (const ch of this._def.checks) {
            if (ch.kind === "min") {
                if (min === null || ch.value > min)
                    min = ch.value;
            }
        }
        return min != null ? new Date(min) : null;
    }
    get maxDate() {
        let max = null;
        for (const ch of this._def.checks) {
            if (ch.kind === "max") {
                if (max === null || ch.value < max)
                    max = ch.value;
            }
        }
        return max != null ? new Date(max) : null;
    }
}
ZodDate.create = (params) => {
    return new ZodDate({
        checks: [],
        coerce: params?.coerce || false,
        typeName: ZodFirstPartyTypeKind.ZodDate,
        ...processCreateParams(params),
    });
};
class ZodSymbol extends ZodType {
    _parse(input) {
        const parsedType = this._getType(input);
        if (parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.symbol) {
            const ctx = this._getOrReturnCtx(input);
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.symbol,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.OK)(input.data);
    }
}
ZodSymbol.create = (params) => {
    return new ZodSymbol({
        typeName: ZodFirstPartyTypeKind.ZodSymbol,
        ...processCreateParams(params),
    });
};
class ZodUndefined extends ZodType {
    _parse(input) {
        const parsedType = this._getType(input);
        if (parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.undefined) {
            const ctx = this._getOrReturnCtx(input);
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.undefined,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.OK)(input.data);
    }
}
ZodUndefined.create = (params) => {
    return new ZodUndefined({
        typeName: ZodFirstPartyTypeKind.ZodUndefined,
        ...processCreateParams(params),
    });
};
class ZodNull extends ZodType {
    _parse(input) {
        const parsedType = this._getType(input);
        if (parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.null) {
            const ctx = this._getOrReturnCtx(input);
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.null,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.OK)(input.data);
    }
}
ZodNull.create = (params) => {
    return new ZodNull({
        typeName: ZodFirstPartyTypeKind.ZodNull,
        ...processCreateParams(params),
    });
};
class ZodAny extends ZodType {
    constructor() {
        super(...arguments);
        // to prevent instances of other classes from extending ZodAny. this causes issues with catchall in ZodObject.
        this._any = true;
    }
    _parse(input) {
        return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.OK)(input.data);
    }
}
ZodAny.create = (params) => {
    return new ZodAny({
        typeName: ZodFirstPartyTypeKind.ZodAny,
        ...processCreateParams(params),
    });
};
class ZodUnknown extends ZodType {
    constructor() {
        super(...arguments);
        // required
        this._unknown = true;
    }
    _parse(input) {
        return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.OK)(input.data);
    }
}
ZodUnknown.create = (params) => {
    return new ZodUnknown({
        typeName: ZodFirstPartyTypeKind.ZodUnknown,
        ...processCreateParams(params),
    });
};
class ZodNever extends ZodType {
    _parse(input) {
        const ctx = this._getOrReturnCtx(input);
        (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
            code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
            expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.never,
            received: ctx.parsedType,
        });
        return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
    }
}
ZodNever.create = (params) => {
    return new ZodNever({
        typeName: ZodFirstPartyTypeKind.ZodNever,
        ...processCreateParams(params),
    });
};
class ZodVoid extends ZodType {
    _parse(input) {
        const parsedType = this._getType(input);
        if (parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.undefined) {
            const ctx = this._getOrReturnCtx(input);
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.void,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.OK)(input.data);
    }
}
ZodVoid.create = (params) => {
    return new ZodVoid({
        typeName: ZodFirstPartyTypeKind.ZodVoid,
        ...processCreateParams(params),
    });
};
class ZodArray extends ZodType {
    _parse(input) {
        const { ctx, status } = this._processInputParams(input);
        const def = this._def;
        if (ctx.parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.array) {
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.array,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        if (def.exactLength !== null) {
            const tooBig = ctx.data.length > def.exactLength.value;
            const tooSmall = ctx.data.length < def.exactLength.value;
            if (tooBig || tooSmall) {
                (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                    code: tooBig ? _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_big : _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_small,
                    minimum: (tooSmall ? def.exactLength.value : undefined),
                    maximum: (tooBig ? def.exactLength.value : undefined),
                    type: "array",
                    inclusive: true,
                    exact: true,
                    message: def.exactLength.message,
                });
                status.dirty();
            }
        }
        if (def.minLength !== null) {
            if (ctx.data.length < def.minLength.value) {
                (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                    code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_small,
                    minimum: def.minLength.value,
                    type: "array",
                    inclusive: true,
                    exact: false,
                    message: def.minLength.message,
                });
                status.dirty();
            }
        }
        if (def.maxLength !== null) {
            if (ctx.data.length > def.maxLength.value) {
                (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                    code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_big,
                    maximum: def.maxLength.value,
                    type: "array",
                    inclusive: true,
                    exact: false,
                    message: def.maxLength.message,
                });
                status.dirty();
            }
        }
        if (ctx.common.async) {
            return Promise.all([...ctx.data].map((item, i) => {
                return def.type._parseAsync(new ParseInputLazyPath(ctx, item, ctx.path, i));
            })).then((result) => {
                return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.ParseStatus.mergeArray(status, result);
            });
        }
        const result = [...ctx.data].map((item, i) => {
            return def.type._parseSync(new ParseInputLazyPath(ctx, item, ctx.path, i));
        });
        return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.ParseStatus.mergeArray(status, result);
    }
    get element() {
        return this._def.type;
    }
    min(minLength, message) {
        return new ZodArray({
            ...this._def,
            minLength: { value: minLength, message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message) },
        });
    }
    max(maxLength, message) {
        return new ZodArray({
            ...this._def,
            maxLength: { value: maxLength, message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message) },
        });
    }
    length(len, message) {
        return new ZodArray({
            ...this._def,
            exactLength: { value: len, message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message) },
        });
    }
    nonempty(message) {
        return this.min(1, message);
    }
}
ZodArray.create = (schema, params) => {
    return new ZodArray({
        type: schema,
        minLength: null,
        maxLength: null,
        exactLength: null,
        typeName: ZodFirstPartyTypeKind.ZodArray,
        ...processCreateParams(params),
    });
};
function deepPartialify(schema) {
    if (schema instanceof ZodObject) {
        const newShape = {};
        for (const key in schema.shape) {
            const fieldSchema = schema.shape[key];
            newShape[key] = ZodOptional.create(deepPartialify(fieldSchema));
        }
        return new ZodObject({
            ...schema._def,
            shape: () => newShape,
        });
    }
    else if (schema instanceof ZodArray) {
        return new ZodArray({
            ...schema._def,
            type: deepPartialify(schema.element),
        });
    }
    else if (schema instanceof ZodOptional) {
        return ZodOptional.create(deepPartialify(schema.unwrap()));
    }
    else if (schema instanceof ZodNullable) {
        return ZodNullable.create(deepPartialify(schema.unwrap()));
    }
    else if (schema instanceof ZodTuple) {
        return ZodTuple.create(schema.items.map((item) => deepPartialify(item)));
    }
    else {
        return schema;
    }
}
class ZodObject extends ZodType {
    constructor() {
        super(...arguments);
        this._cached = null;
        /**
         * @deprecated In most cases, this is no longer needed - unknown properties are now silently stripped.
         * If you want to pass through unknown properties, use `.passthrough()` instead.
         */
        this.nonstrict = this.passthrough;
        // extend<
        //   Augmentation extends ZodRawShape,
        //   NewOutput extends util.flatten<{
        //     [k in keyof Augmentation | keyof Output]: k extends keyof Augmentation
        //       ? Augmentation[k]["_output"]
        //       : k extends keyof Output
        //       ? Output[k]
        //       : never;
        //   }>,
        //   NewInput extends util.flatten<{
        //     [k in keyof Augmentation | keyof Input]: k extends keyof Augmentation
        //       ? Augmentation[k]["_input"]
        //       : k extends keyof Input
        //       ? Input[k]
        //       : never;
        //   }>
        // >(
        //   augmentation: Augmentation
        // ): ZodObject<
        //   extendShape<T, Augmentation>,
        //   UnknownKeys,
        //   Catchall,
        //   NewOutput,
        //   NewInput
        // > {
        //   return new ZodObject({
        //     ...this._def,
        //     shape: () => ({
        //       ...this._def.shape(),
        //       ...augmentation,
        //     }),
        //   }) as any;
        // }
        /**
         * @deprecated Use `.extend` instead
         *  */
        this.augment = this.extend;
    }
    _getCached() {
        if (this._cached !== null)
            return this._cached;
        const shape = this._def.shape();
        const keys = _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.objectKeys(shape);
        this._cached = { shape, keys };
        return this._cached;
    }
    _parse(input) {
        const parsedType = this._getType(input);
        if (parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.object) {
            const ctx = this._getOrReturnCtx(input);
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.object,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        const { status, ctx } = this._processInputParams(input);
        const { shape, keys: shapeKeys } = this._getCached();
        const extraKeys = [];
        if (!(this._def.catchall instanceof ZodNever && this._def.unknownKeys === "strip")) {
            for (const key in ctx.data) {
                if (!shapeKeys.includes(key)) {
                    extraKeys.push(key);
                }
            }
        }
        const pairs = [];
        for (const key of shapeKeys) {
            const keyValidator = shape[key];
            const value = ctx.data[key];
            pairs.push({
                key: { status: "valid", value: key },
                value: keyValidator._parse(new ParseInputLazyPath(ctx, value, ctx.path, key)),
                alwaysSet: key in ctx.data,
            });
        }
        if (this._def.catchall instanceof ZodNever) {
            const unknownKeys = this._def.unknownKeys;
            if (unknownKeys === "passthrough") {
                for (const key of extraKeys) {
                    pairs.push({
                        key: { status: "valid", value: key },
                        value: { status: "valid", value: ctx.data[key] },
                    });
                }
            }
            else if (unknownKeys === "strict") {
                if (extraKeys.length > 0) {
                    (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                        code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.unrecognized_keys,
                        keys: extraKeys,
                    });
                    status.dirty();
                }
            }
            else if (unknownKeys === "strip") {
            }
            else {
                throw new Error(`Internal ZodObject error: invalid unknownKeys value.`);
            }
        }
        else {
            // run catchall validation
            const catchall = this._def.catchall;
            for (const key of extraKeys) {
                const value = ctx.data[key];
                pairs.push({
                    key: { status: "valid", value: key },
                    value: catchall._parse(new ParseInputLazyPath(ctx, value, ctx.path, key) //, ctx.child(key), value, getParsedType(value)
                    ),
                    alwaysSet: key in ctx.data,
                });
            }
        }
        if (ctx.common.async) {
            return Promise.resolve()
                .then(async () => {
                const syncPairs = [];
                for (const pair of pairs) {
                    const key = await pair.key;
                    const value = await pair.value;
                    syncPairs.push({
                        key,
                        value,
                        alwaysSet: pair.alwaysSet,
                    });
                }
                return syncPairs;
            })
                .then((syncPairs) => {
                return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.ParseStatus.mergeObjectSync(status, syncPairs);
            });
        }
        else {
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.ParseStatus.mergeObjectSync(status, pairs);
        }
    }
    get shape() {
        return this._def.shape();
    }
    strict(message) {
        _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj;
        return new ZodObject({
            ...this._def,
            unknownKeys: "strict",
            ...(message !== undefined
                ? {
                    errorMap: (issue, ctx) => {
                        const defaultError = this._def.errorMap?.(issue, ctx).message ?? ctx.defaultError;
                        if (issue.code === "unrecognized_keys")
                            return {
                                message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.errToObj(message).message ?? defaultError,
                            };
                        return {
                            message: defaultError,
                        };
                    },
                }
                : {}),
        });
    }
    strip() {
        return new ZodObject({
            ...this._def,
            unknownKeys: "strip",
        });
    }
    passthrough() {
        return new ZodObject({
            ...this._def,
            unknownKeys: "passthrough",
        });
    }
    // const AugmentFactory =
    //   <Def extends ZodObjectDef>(def: Def) =>
    //   <Augmentation extends ZodRawShape>(
    //     augmentation: Augmentation
    //   ): ZodObject<
    //     extendShape<ReturnType<Def["shape"]>, Augmentation>,
    //     Def["unknownKeys"],
    //     Def["catchall"]
    //   > => {
    //     return new ZodObject({
    //       ...def,
    //       shape: () => ({
    //         ...def.shape(),
    //         ...augmentation,
    //       }),
    //     }) as any;
    //   };
    extend(augmentation) {
        return new ZodObject({
            ...this._def,
            shape: () => ({
                ...this._def.shape(),
                ...augmentation,
            }),
        });
    }
    /**
     * Prior to zod@1.0.12 there was a bug in the
     * inferred type of merged objects. Please
     * upgrade if you are experiencing issues.
     */
    merge(merging) {
        const merged = new ZodObject({
            unknownKeys: merging._def.unknownKeys,
            catchall: merging._def.catchall,
            shape: () => ({
                ...this._def.shape(),
                ...merging._def.shape(),
            }),
            typeName: ZodFirstPartyTypeKind.ZodObject,
        });
        return merged;
    }
    // merge<
    //   Incoming extends AnyZodObject,
    //   Augmentation extends Incoming["shape"],
    //   NewOutput extends {
    //     [k in keyof Augmentation | keyof Output]: k extends keyof Augmentation
    //       ? Augmentation[k]["_output"]
    //       : k extends keyof Output
    //       ? Output[k]
    //       : never;
    //   },
    //   NewInput extends {
    //     [k in keyof Augmentation | keyof Input]: k extends keyof Augmentation
    //       ? Augmentation[k]["_input"]
    //       : k extends keyof Input
    //       ? Input[k]
    //       : never;
    //   }
    // >(
    //   merging: Incoming
    // ): ZodObject<
    //   extendShape<T, ReturnType<Incoming["_def"]["shape"]>>,
    //   Incoming["_def"]["unknownKeys"],
    //   Incoming["_def"]["catchall"],
    //   NewOutput,
    //   NewInput
    // > {
    //   const merged: any = new ZodObject({
    //     unknownKeys: merging._def.unknownKeys,
    //     catchall: merging._def.catchall,
    //     shape: () =>
    //       objectUtil.mergeShapes(this._def.shape(), merging._def.shape()),
    //     typeName: ZodFirstPartyTypeKind.ZodObject,
    //   }) as any;
    //   return merged;
    // }
    setKey(key, schema) {
        return this.augment({ [key]: schema });
    }
    // merge<Incoming extends AnyZodObject>(
    //   merging: Incoming
    // ): //ZodObject<T & Incoming["_shape"], UnknownKeys, Catchall> = (merging) => {
    // ZodObject<
    //   extendShape<T, ReturnType<Incoming["_def"]["shape"]>>,
    //   Incoming["_def"]["unknownKeys"],
    //   Incoming["_def"]["catchall"]
    // > {
    //   // const mergedShape = objectUtil.mergeShapes(
    //   //   this._def.shape(),
    //   //   merging._def.shape()
    //   // );
    //   const merged: any = new ZodObject({
    //     unknownKeys: merging._def.unknownKeys,
    //     catchall: merging._def.catchall,
    //     shape: () =>
    //       objectUtil.mergeShapes(this._def.shape(), merging._def.shape()),
    //     typeName: ZodFirstPartyTypeKind.ZodObject,
    //   }) as any;
    //   return merged;
    // }
    catchall(index) {
        return new ZodObject({
            ...this._def,
            catchall: index,
        });
    }
    pick(mask) {
        const shape = {};
        for (const key of _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.objectKeys(mask)) {
            if (mask[key] && this.shape[key]) {
                shape[key] = this.shape[key];
            }
        }
        return new ZodObject({
            ...this._def,
            shape: () => shape,
        });
    }
    omit(mask) {
        const shape = {};
        for (const key of _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.objectKeys(this.shape)) {
            if (!mask[key]) {
                shape[key] = this.shape[key];
            }
        }
        return new ZodObject({
            ...this._def,
            shape: () => shape,
        });
    }
    /**
     * @deprecated
     */
    deepPartial() {
        return deepPartialify(this);
    }
    partial(mask) {
        const newShape = {};
        for (const key of _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.objectKeys(this.shape)) {
            const fieldSchema = this.shape[key];
            if (mask && !mask[key]) {
                newShape[key] = fieldSchema;
            }
            else {
                newShape[key] = fieldSchema.optional();
            }
        }
        return new ZodObject({
            ...this._def,
            shape: () => newShape,
        });
    }
    required(mask) {
        const newShape = {};
        for (const key of _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.objectKeys(this.shape)) {
            if (mask && !mask[key]) {
                newShape[key] = this.shape[key];
            }
            else {
                const fieldSchema = this.shape[key];
                let newField = fieldSchema;
                while (newField instanceof ZodOptional) {
                    newField = newField._def.innerType;
                }
                newShape[key] = newField;
            }
        }
        return new ZodObject({
            ...this._def,
            shape: () => newShape,
        });
    }
    keyof() {
        return createZodEnum(_helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.objectKeys(this.shape));
    }
}
ZodObject.create = (shape, params) => {
    return new ZodObject({
        shape: () => shape,
        unknownKeys: "strip",
        catchall: ZodNever.create(),
        typeName: ZodFirstPartyTypeKind.ZodObject,
        ...processCreateParams(params),
    });
};
ZodObject.strictCreate = (shape, params) => {
    return new ZodObject({
        shape: () => shape,
        unknownKeys: "strict",
        catchall: ZodNever.create(),
        typeName: ZodFirstPartyTypeKind.ZodObject,
        ...processCreateParams(params),
    });
};
ZodObject.lazycreate = (shape, params) => {
    return new ZodObject({
        shape,
        unknownKeys: "strip",
        catchall: ZodNever.create(),
        typeName: ZodFirstPartyTypeKind.ZodObject,
        ...processCreateParams(params),
    });
};
class ZodUnion extends ZodType {
    _parse(input) {
        const { ctx } = this._processInputParams(input);
        const options = this._def.options;
        function handleResults(results) {
            // return first issue-free validation if it exists
            for (const result of results) {
                if (result.result.status === "valid") {
                    return result.result;
                }
            }
            for (const result of results) {
                if (result.result.status === "dirty") {
                    // add issues from dirty option
                    ctx.common.issues.push(...result.ctx.common.issues);
                    return result.result;
                }
            }
            // return invalid
            const unionErrors = results.map((result) => new _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodError(result.ctx.common.issues));
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_union,
                unionErrors,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        if (ctx.common.async) {
            return Promise.all(options.map(async (option) => {
                const childCtx = {
                    ...ctx,
                    common: {
                        ...ctx.common,
                        issues: [],
                    },
                    parent: null,
                };
                return {
                    result: await option._parseAsync({
                        data: ctx.data,
                        path: ctx.path,
                        parent: childCtx,
                    }),
                    ctx: childCtx,
                };
            })).then(handleResults);
        }
        else {
            let dirty = undefined;
            const issues = [];
            for (const option of options) {
                const childCtx = {
                    ...ctx,
                    common: {
                        ...ctx.common,
                        issues: [],
                    },
                    parent: null,
                };
                const result = option._parseSync({
                    data: ctx.data,
                    path: ctx.path,
                    parent: childCtx,
                });
                if (result.status === "valid") {
                    return result;
                }
                else if (result.status === "dirty" && !dirty) {
                    dirty = { result, ctx: childCtx };
                }
                if (childCtx.common.issues.length) {
                    issues.push(childCtx.common.issues);
                }
            }
            if (dirty) {
                ctx.common.issues.push(...dirty.ctx.common.issues);
                return dirty.result;
            }
            const unionErrors = issues.map((issues) => new _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodError(issues));
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_union,
                unionErrors,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
    }
    get options() {
        return this._def.options;
    }
}
ZodUnion.create = (types, params) => {
    return new ZodUnion({
        options: types,
        typeName: ZodFirstPartyTypeKind.ZodUnion,
        ...processCreateParams(params),
    });
};
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
//////////                                 //////////
//////////      ZodDiscriminatedUnion      //////////
//////////                                 //////////
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
const getDiscriminator = (type) => {
    if (type instanceof ZodLazy) {
        return getDiscriminator(type.schema);
    }
    else if (type instanceof ZodEffects) {
        return getDiscriminator(type.innerType());
    }
    else if (type instanceof ZodLiteral) {
        return [type.value];
    }
    else if (type instanceof ZodEnum) {
        return type.options;
    }
    else if (type instanceof ZodNativeEnum) {
        // eslint-disable-next-line ban/ban
        return _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.objectValues(type.enum);
    }
    else if (type instanceof ZodDefault) {
        return getDiscriminator(type._def.innerType);
    }
    else if (type instanceof ZodUndefined) {
        return [undefined];
    }
    else if (type instanceof ZodNull) {
        return [null];
    }
    else if (type instanceof ZodOptional) {
        return [undefined, ...getDiscriminator(type.unwrap())];
    }
    else if (type instanceof ZodNullable) {
        return [null, ...getDiscriminator(type.unwrap())];
    }
    else if (type instanceof ZodBranded) {
        return getDiscriminator(type.unwrap());
    }
    else if (type instanceof ZodReadonly) {
        return getDiscriminator(type.unwrap());
    }
    else if (type instanceof ZodCatch) {
        return getDiscriminator(type._def.innerType);
    }
    else {
        return [];
    }
};
class ZodDiscriminatedUnion extends ZodType {
    _parse(input) {
        const { ctx } = this._processInputParams(input);
        if (ctx.parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.object) {
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.object,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        const discriminator = this.discriminator;
        const discriminatorValue = ctx.data[discriminator];
        const option = this.optionsMap.get(discriminatorValue);
        if (!option) {
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_union_discriminator,
                options: Array.from(this.optionsMap.keys()),
                path: [discriminator],
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        if (ctx.common.async) {
            return option._parseAsync({
                data: ctx.data,
                path: ctx.path,
                parent: ctx,
            });
        }
        else {
            return option._parseSync({
                data: ctx.data,
                path: ctx.path,
                parent: ctx,
            });
        }
    }
    get discriminator() {
        return this._def.discriminator;
    }
    get options() {
        return this._def.options;
    }
    get optionsMap() {
        return this._def.optionsMap;
    }
    /**
     * The constructor of the discriminated union schema. Its behaviour is very similar to that of the normal z.union() constructor.
     * However, it only allows a union of objects, all of which need to share a discriminator property. This property must
     * have a different value for each object in the union.
     * @param discriminator the name of the discriminator property
     * @param types an array of object schemas
     * @param params
     */
    static create(discriminator, options, params) {
        // Get all the valid discriminator values
        const optionsMap = new Map();
        // try {
        for (const type of options) {
            const discriminatorValues = getDiscriminator(type.shape[discriminator]);
            if (!discriminatorValues.length) {
                throw new Error(`A discriminator value for key \`${discriminator}\` could not be extracted from all schema options`);
            }
            for (const value of discriminatorValues) {
                if (optionsMap.has(value)) {
                    throw new Error(`Discriminator property ${String(discriminator)} has duplicate value ${String(value)}`);
                }
                optionsMap.set(value, type);
            }
        }
        return new ZodDiscriminatedUnion({
            typeName: ZodFirstPartyTypeKind.ZodDiscriminatedUnion,
            discriminator,
            options,
            optionsMap,
            ...processCreateParams(params),
        });
    }
}
function mergeValues(a, b) {
    const aType = (0,_helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.getParsedType)(a);
    const bType = (0,_helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.getParsedType)(b);
    if (a === b) {
        return { valid: true, data: a };
    }
    else if (aType === _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.object && bType === _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.object) {
        const bKeys = _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.objectKeys(b);
        const sharedKeys = _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.objectKeys(a).filter((key) => bKeys.indexOf(key) !== -1);
        const newObj = { ...a, ...b };
        for (const key of sharedKeys) {
            const sharedValue = mergeValues(a[key], b[key]);
            if (!sharedValue.valid) {
                return { valid: false };
            }
            newObj[key] = sharedValue.data;
        }
        return { valid: true, data: newObj };
    }
    else if (aType === _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.array && bType === _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.array) {
        if (a.length !== b.length) {
            return { valid: false };
        }
        const newArray = [];
        for (let index = 0; index < a.length; index++) {
            const itemA = a[index];
            const itemB = b[index];
            const sharedValue = mergeValues(itemA, itemB);
            if (!sharedValue.valid) {
                return { valid: false };
            }
            newArray.push(sharedValue.data);
        }
        return { valid: true, data: newArray };
    }
    else if (aType === _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.date && bType === _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.date && +a === +b) {
        return { valid: true, data: a };
    }
    else {
        return { valid: false };
    }
}
class ZodIntersection extends ZodType {
    _parse(input) {
        const { status, ctx } = this._processInputParams(input);
        const handleParsed = (parsedLeft, parsedRight) => {
            if ((0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.isAborted)(parsedLeft) || (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.isAborted)(parsedRight)) {
                return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
            }
            const merged = mergeValues(parsedLeft.value, parsedRight.value);
            if (!merged.valid) {
                (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                    code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_intersection_types,
                });
                return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
            }
            if ((0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.isDirty)(parsedLeft) || (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.isDirty)(parsedRight)) {
                status.dirty();
            }
            return { status: status.value, value: merged.data };
        };
        if (ctx.common.async) {
            return Promise.all([
                this._def.left._parseAsync({
                    data: ctx.data,
                    path: ctx.path,
                    parent: ctx,
                }),
                this._def.right._parseAsync({
                    data: ctx.data,
                    path: ctx.path,
                    parent: ctx,
                }),
            ]).then(([left, right]) => handleParsed(left, right));
        }
        else {
            return handleParsed(this._def.left._parseSync({
                data: ctx.data,
                path: ctx.path,
                parent: ctx,
            }), this._def.right._parseSync({
                data: ctx.data,
                path: ctx.path,
                parent: ctx,
            }));
        }
    }
}
ZodIntersection.create = (left, right, params) => {
    return new ZodIntersection({
        left: left,
        right: right,
        typeName: ZodFirstPartyTypeKind.ZodIntersection,
        ...processCreateParams(params),
    });
};
// type ZodTupleItems = [ZodTypeAny, ...ZodTypeAny[]];
class ZodTuple extends ZodType {
    _parse(input) {
        const { status, ctx } = this._processInputParams(input);
        if (ctx.parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.array) {
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.array,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        if (ctx.data.length < this._def.items.length) {
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_small,
                minimum: this._def.items.length,
                inclusive: true,
                exact: false,
                type: "array",
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        const rest = this._def.rest;
        if (!rest && ctx.data.length > this._def.items.length) {
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_big,
                maximum: this._def.items.length,
                inclusive: true,
                exact: false,
                type: "array",
            });
            status.dirty();
        }
        const items = [...ctx.data]
            .map((item, itemIndex) => {
            const schema = this._def.items[itemIndex] || this._def.rest;
            if (!schema)
                return null;
            return schema._parse(new ParseInputLazyPath(ctx, item, ctx.path, itemIndex));
        })
            .filter((x) => !!x); // filter nulls
        if (ctx.common.async) {
            return Promise.all(items).then((results) => {
                return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.ParseStatus.mergeArray(status, results);
            });
        }
        else {
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.ParseStatus.mergeArray(status, items);
        }
    }
    get items() {
        return this._def.items;
    }
    rest(rest) {
        return new ZodTuple({
            ...this._def,
            rest,
        });
    }
}
ZodTuple.create = (schemas, params) => {
    if (!Array.isArray(schemas)) {
        throw new Error("You must pass an array of schemas to z.tuple([ ... ])");
    }
    return new ZodTuple({
        items: schemas,
        typeName: ZodFirstPartyTypeKind.ZodTuple,
        rest: null,
        ...processCreateParams(params),
    });
};
class ZodRecord extends ZodType {
    get keySchema() {
        return this._def.keyType;
    }
    get valueSchema() {
        return this._def.valueType;
    }
    _parse(input) {
        const { status, ctx } = this._processInputParams(input);
        if (ctx.parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.object) {
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.object,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        const pairs = [];
        const keyType = this._def.keyType;
        const valueType = this._def.valueType;
        for (const key in ctx.data) {
            pairs.push({
                key: keyType._parse(new ParseInputLazyPath(ctx, key, ctx.path, key)),
                value: valueType._parse(new ParseInputLazyPath(ctx, ctx.data[key], ctx.path, key)),
                alwaysSet: key in ctx.data,
            });
        }
        if (ctx.common.async) {
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.ParseStatus.mergeObjectAsync(status, pairs);
        }
        else {
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.ParseStatus.mergeObjectSync(status, pairs);
        }
    }
    get element() {
        return this._def.valueType;
    }
    static create(first, second, third) {
        if (second instanceof ZodType) {
            return new ZodRecord({
                keyType: first,
                valueType: second,
                typeName: ZodFirstPartyTypeKind.ZodRecord,
                ...processCreateParams(third),
            });
        }
        return new ZodRecord({
            keyType: ZodString.create(),
            valueType: first,
            typeName: ZodFirstPartyTypeKind.ZodRecord,
            ...processCreateParams(second),
        });
    }
}
class ZodMap extends ZodType {
    get keySchema() {
        return this._def.keyType;
    }
    get valueSchema() {
        return this._def.valueType;
    }
    _parse(input) {
        const { status, ctx } = this._processInputParams(input);
        if (ctx.parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.map) {
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.map,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        const keyType = this._def.keyType;
        const valueType = this._def.valueType;
        const pairs = [...ctx.data.entries()].map(([key, value], index) => {
            return {
                key: keyType._parse(new ParseInputLazyPath(ctx, key, ctx.path, [index, "key"])),
                value: valueType._parse(new ParseInputLazyPath(ctx, value, ctx.path, [index, "value"])),
            };
        });
        if (ctx.common.async) {
            const finalMap = new Map();
            return Promise.resolve().then(async () => {
                for (const pair of pairs) {
                    const key = await pair.key;
                    const value = await pair.value;
                    if (key.status === "aborted" || value.status === "aborted") {
                        return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
                    }
                    if (key.status === "dirty" || value.status === "dirty") {
                        status.dirty();
                    }
                    finalMap.set(key.value, value.value);
                }
                return { status: status.value, value: finalMap };
            });
        }
        else {
            const finalMap = new Map();
            for (const pair of pairs) {
                const key = pair.key;
                const value = pair.value;
                if (key.status === "aborted" || value.status === "aborted") {
                    return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
                }
                if (key.status === "dirty" || value.status === "dirty") {
                    status.dirty();
                }
                finalMap.set(key.value, value.value);
            }
            return { status: status.value, value: finalMap };
        }
    }
}
ZodMap.create = (keyType, valueType, params) => {
    return new ZodMap({
        valueType,
        keyType,
        typeName: ZodFirstPartyTypeKind.ZodMap,
        ...processCreateParams(params),
    });
};
class ZodSet extends ZodType {
    _parse(input) {
        const { status, ctx } = this._processInputParams(input);
        if (ctx.parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.set) {
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.set,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        const def = this._def;
        if (def.minSize !== null) {
            if (ctx.data.size < def.minSize.value) {
                (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                    code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_small,
                    minimum: def.minSize.value,
                    type: "set",
                    inclusive: true,
                    exact: false,
                    message: def.minSize.message,
                });
                status.dirty();
            }
        }
        if (def.maxSize !== null) {
            if (ctx.data.size > def.maxSize.value) {
                (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                    code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.too_big,
                    maximum: def.maxSize.value,
                    type: "set",
                    inclusive: true,
                    exact: false,
                    message: def.maxSize.message,
                });
                status.dirty();
            }
        }
        const valueType = this._def.valueType;
        function finalizeSet(elements) {
            const parsedSet = new Set();
            for (const element of elements) {
                if (element.status === "aborted")
                    return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
                if (element.status === "dirty")
                    status.dirty();
                parsedSet.add(element.value);
            }
            return { status: status.value, value: parsedSet };
        }
        const elements = [...ctx.data.values()].map((item, i) => valueType._parse(new ParseInputLazyPath(ctx, item, ctx.path, i)));
        if (ctx.common.async) {
            return Promise.all(elements).then((elements) => finalizeSet(elements));
        }
        else {
            return finalizeSet(elements);
        }
    }
    min(minSize, message) {
        return new ZodSet({
            ...this._def,
            minSize: { value: minSize, message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message) },
        });
    }
    max(maxSize, message) {
        return new ZodSet({
            ...this._def,
            maxSize: { value: maxSize, message: _helpers_errorUtil_js__WEBPACK_IMPORTED_MODULE_3__.errorUtil.toString(message) },
        });
    }
    size(size, message) {
        return this.min(size, message).max(size, message);
    }
    nonempty(message) {
        return this.min(1, message);
    }
}
ZodSet.create = (valueType, params) => {
    return new ZodSet({
        valueType,
        minSize: null,
        maxSize: null,
        typeName: ZodFirstPartyTypeKind.ZodSet,
        ...processCreateParams(params),
    });
};
class ZodFunction extends ZodType {
    constructor() {
        super(...arguments);
        this.validate = this.implement;
    }
    _parse(input) {
        const { ctx } = this._processInputParams(input);
        if (ctx.parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.function) {
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.function,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        function makeArgsIssue(args, error) {
            return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.makeIssue)({
                data: args,
                path: ctx.path,
                errorMaps: [ctx.common.contextualErrorMap, ctx.schemaErrorMap, (0,_errors_js__WEBPACK_IMPORTED_MODULE_1__.getErrorMap)(), _errors_js__WEBPACK_IMPORTED_MODULE_2__["default"]].filter((x) => !!x),
                issueData: {
                    code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_arguments,
                    argumentsError: error,
                },
            });
        }
        function makeReturnsIssue(returns, error) {
            return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.makeIssue)({
                data: returns,
                path: ctx.path,
                errorMaps: [ctx.common.contextualErrorMap, ctx.schemaErrorMap, (0,_errors_js__WEBPACK_IMPORTED_MODULE_1__.getErrorMap)(), _errors_js__WEBPACK_IMPORTED_MODULE_2__["default"]].filter((x) => !!x),
                issueData: {
                    code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_return_type,
                    returnTypeError: error,
                },
            });
        }
        const params = { errorMap: ctx.common.contextualErrorMap };
        const fn = ctx.data;
        if (this._def.returns instanceof ZodPromise) {
            // Would love a way to avoid disabling this rule, but we need
            // an alias (using an arrow function was what caused 2651).
            // eslint-disable-next-line @typescript-eslint/no-this-alias
            const me = this;
            return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.OK)(async function (...args) {
                const error = new _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodError([]);
                const parsedArgs = await me._def.args.parseAsync(args, params).catch((e) => {
                    error.addIssue(makeArgsIssue(args, e));
                    throw error;
                });
                const result = await Reflect.apply(fn, this, parsedArgs);
                const parsedReturns = await me._def.returns._def.type
                    .parseAsync(result, params)
                    .catch((e) => {
                    error.addIssue(makeReturnsIssue(result, e));
                    throw error;
                });
                return parsedReturns;
            });
        }
        else {
            // Would love a way to avoid disabling this rule, but we need
            // an alias (using an arrow function was what caused 2651).
            // eslint-disable-next-line @typescript-eslint/no-this-alias
            const me = this;
            return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.OK)(function (...args) {
                const parsedArgs = me._def.args.safeParse(args, params);
                if (!parsedArgs.success) {
                    throw new _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodError([makeArgsIssue(args, parsedArgs.error)]);
                }
                const result = Reflect.apply(fn, this, parsedArgs.data);
                const parsedReturns = me._def.returns.safeParse(result, params);
                if (!parsedReturns.success) {
                    throw new _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodError([makeReturnsIssue(result, parsedReturns.error)]);
                }
                return parsedReturns.data;
            });
        }
    }
    parameters() {
        return this._def.args;
    }
    returnType() {
        return this._def.returns;
    }
    args(...items) {
        return new ZodFunction({
            ...this._def,
            args: ZodTuple.create(items).rest(ZodUnknown.create()),
        });
    }
    returns(returnType) {
        return new ZodFunction({
            ...this._def,
            returns: returnType,
        });
    }
    implement(func) {
        const validatedFunc = this.parse(func);
        return validatedFunc;
    }
    strictImplement(func) {
        const validatedFunc = this.parse(func);
        return validatedFunc;
    }
    static create(args, returns, params) {
        return new ZodFunction({
            args: (args ? args : ZodTuple.create([]).rest(ZodUnknown.create())),
            returns: returns || ZodUnknown.create(),
            typeName: ZodFirstPartyTypeKind.ZodFunction,
            ...processCreateParams(params),
        });
    }
}
class ZodLazy extends ZodType {
    get schema() {
        return this._def.getter();
    }
    _parse(input) {
        const { ctx } = this._processInputParams(input);
        const lazySchema = this._def.getter();
        return lazySchema._parse({ data: ctx.data, path: ctx.path, parent: ctx });
    }
}
ZodLazy.create = (getter, params) => {
    return new ZodLazy({
        getter: getter,
        typeName: ZodFirstPartyTypeKind.ZodLazy,
        ...processCreateParams(params),
    });
};
class ZodLiteral extends ZodType {
    _parse(input) {
        if (input.data !== this._def.value) {
            const ctx = this._getOrReturnCtx(input);
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                received: ctx.data,
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_literal,
                expected: this._def.value,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        return { status: "valid", value: input.data };
    }
    get value() {
        return this._def.value;
    }
}
ZodLiteral.create = (value, params) => {
    return new ZodLiteral({
        value: value,
        typeName: ZodFirstPartyTypeKind.ZodLiteral,
        ...processCreateParams(params),
    });
};
function createZodEnum(values, params) {
    return new ZodEnum({
        values,
        typeName: ZodFirstPartyTypeKind.ZodEnum,
        ...processCreateParams(params),
    });
}
class ZodEnum extends ZodType {
    _parse(input) {
        if (typeof input.data !== "string") {
            const ctx = this._getOrReturnCtx(input);
            const expectedValues = this._def.values;
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.joinValues(expectedValues),
                received: ctx.parsedType,
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        if (!this._cache) {
            this._cache = new Set(this._def.values);
        }
        if (!this._cache.has(input.data)) {
            const ctx = this._getOrReturnCtx(input);
            const expectedValues = this._def.values;
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                received: ctx.data,
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_enum_value,
                options: expectedValues,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.OK)(input.data);
    }
    get options() {
        return this._def.values;
    }
    get enum() {
        const enumValues = {};
        for (const val of this._def.values) {
            enumValues[val] = val;
        }
        return enumValues;
    }
    get Values() {
        const enumValues = {};
        for (const val of this._def.values) {
            enumValues[val] = val;
        }
        return enumValues;
    }
    get Enum() {
        const enumValues = {};
        for (const val of this._def.values) {
            enumValues[val] = val;
        }
        return enumValues;
    }
    extract(values, newDef = this._def) {
        return ZodEnum.create(values, {
            ...this._def,
            ...newDef,
        });
    }
    exclude(values, newDef = this._def) {
        return ZodEnum.create(this.options.filter((opt) => !values.includes(opt)), {
            ...this._def,
            ...newDef,
        });
    }
}
ZodEnum.create = createZodEnum;
class ZodNativeEnum extends ZodType {
    _parse(input) {
        const nativeEnumValues = _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.getValidEnumValues(this._def.values);
        const ctx = this._getOrReturnCtx(input);
        if (ctx.parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.string && ctx.parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.number) {
            const expectedValues = _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.objectValues(nativeEnumValues);
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.joinValues(expectedValues),
                received: ctx.parsedType,
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        if (!this._cache) {
            this._cache = new Set(_helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.getValidEnumValues(this._def.values));
        }
        if (!this._cache.has(input.data)) {
            const expectedValues = _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.objectValues(nativeEnumValues);
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                received: ctx.data,
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_enum_value,
                options: expectedValues,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.OK)(input.data);
    }
    get enum() {
        return this._def.values;
    }
}
ZodNativeEnum.create = (values, params) => {
    return new ZodNativeEnum({
        values: values,
        typeName: ZodFirstPartyTypeKind.ZodNativeEnum,
        ...processCreateParams(params),
    });
};
class ZodPromise extends ZodType {
    unwrap() {
        return this._def.type;
    }
    _parse(input) {
        const { ctx } = this._processInputParams(input);
        if (ctx.parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.promise && ctx.common.async === false) {
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.promise,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        const promisified = ctx.parsedType === _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.promise ? ctx.data : Promise.resolve(ctx.data);
        return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.OK)(promisified.then((data) => {
            return this._def.type.parseAsync(data, {
                path: ctx.path,
                errorMap: ctx.common.contextualErrorMap,
            });
        }));
    }
}
ZodPromise.create = (schema, params) => {
    return new ZodPromise({
        type: schema,
        typeName: ZodFirstPartyTypeKind.ZodPromise,
        ...processCreateParams(params),
    });
};
class ZodEffects extends ZodType {
    innerType() {
        return this._def.schema;
    }
    sourceType() {
        return this._def.schema._def.typeName === ZodFirstPartyTypeKind.ZodEffects
            ? this._def.schema.sourceType()
            : this._def.schema;
    }
    _parse(input) {
        const { status, ctx } = this._processInputParams(input);
        const effect = this._def.effect || null;
        const checkCtx = {
            addIssue: (arg) => {
                (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, arg);
                if (arg.fatal) {
                    status.abort();
                }
                else {
                    status.dirty();
                }
            },
            get path() {
                return ctx.path;
            },
        };
        checkCtx.addIssue = checkCtx.addIssue.bind(checkCtx);
        if (effect.type === "preprocess") {
            const processed = effect.transform(ctx.data, checkCtx);
            if (ctx.common.async) {
                return Promise.resolve(processed).then(async (processed) => {
                    if (status.value === "aborted")
                        return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
                    const result = await this._def.schema._parseAsync({
                        data: processed,
                        path: ctx.path,
                        parent: ctx,
                    });
                    if (result.status === "aborted")
                        return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
                    if (result.status === "dirty")
                        return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.DIRTY)(result.value);
                    if (status.value === "dirty")
                        return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.DIRTY)(result.value);
                    return result;
                });
            }
            else {
                if (status.value === "aborted")
                    return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
                const result = this._def.schema._parseSync({
                    data: processed,
                    path: ctx.path,
                    parent: ctx,
                });
                if (result.status === "aborted")
                    return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
                if (result.status === "dirty")
                    return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.DIRTY)(result.value);
                if (status.value === "dirty")
                    return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.DIRTY)(result.value);
                return result;
            }
        }
        if (effect.type === "refinement") {
            const executeRefinement = (acc) => {
                const result = effect.refinement(acc, checkCtx);
                if (ctx.common.async) {
                    return Promise.resolve(result);
                }
                if (result instanceof Promise) {
                    throw new Error("Async refinement encountered during synchronous parse operation. Use .parseAsync instead.");
                }
                return acc;
            };
            if (ctx.common.async === false) {
                const inner = this._def.schema._parseSync({
                    data: ctx.data,
                    path: ctx.path,
                    parent: ctx,
                });
                if (inner.status === "aborted")
                    return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
                if (inner.status === "dirty")
                    status.dirty();
                // return value is ignored
                executeRefinement(inner.value);
                return { status: status.value, value: inner.value };
            }
            else {
                return this._def.schema._parseAsync({ data: ctx.data, path: ctx.path, parent: ctx }).then((inner) => {
                    if (inner.status === "aborted")
                        return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
                    if (inner.status === "dirty")
                        status.dirty();
                    return executeRefinement(inner.value).then(() => {
                        return { status: status.value, value: inner.value };
                    });
                });
            }
        }
        if (effect.type === "transform") {
            if (ctx.common.async === false) {
                const base = this._def.schema._parseSync({
                    data: ctx.data,
                    path: ctx.path,
                    parent: ctx,
                });
                if (!(0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.isValid)(base))
                    return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
                const result = effect.transform(base.value, checkCtx);
                if (result instanceof Promise) {
                    throw new Error(`Asynchronous transform encountered during synchronous parse operation. Use .parseAsync instead.`);
                }
                return { status: status.value, value: result };
            }
            else {
                return this._def.schema._parseAsync({ data: ctx.data, path: ctx.path, parent: ctx }).then((base) => {
                    if (!(0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.isValid)(base))
                        return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
                    return Promise.resolve(effect.transform(base.value, checkCtx)).then((result) => ({
                        status: status.value,
                        value: result,
                    }));
                });
            }
        }
        _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.util.assertNever(effect);
    }
}
ZodEffects.create = (schema, effect, params) => {
    return new ZodEffects({
        schema,
        typeName: ZodFirstPartyTypeKind.ZodEffects,
        effect,
        ...processCreateParams(params),
    });
};
ZodEffects.createWithPreprocess = (preprocess, schema, params) => {
    return new ZodEffects({
        schema,
        effect: { type: "preprocess", transform: preprocess },
        typeName: ZodFirstPartyTypeKind.ZodEffects,
        ...processCreateParams(params),
    });
};

class ZodOptional extends ZodType {
    _parse(input) {
        const parsedType = this._getType(input);
        if (parsedType === _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.undefined) {
            return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.OK)(undefined);
        }
        return this._def.innerType._parse(input);
    }
    unwrap() {
        return this._def.innerType;
    }
}
ZodOptional.create = (type, params) => {
    return new ZodOptional({
        innerType: type,
        typeName: ZodFirstPartyTypeKind.ZodOptional,
        ...processCreateParams(params),
    });
};
class ZodNullable extends ZodType {
    _parse(input) {
        const parsedType = this._getType(input);
        if (parsedType === _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.null) {
            return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.OK)(null);
        }
        return this._def.innerType._parse(input);
    }
    unwrap() {
        return this._def.innerType;
    }
}
ZodNullable.create = (type, params) => {
    return new ZodNullable({
        innerType: type,
        typeName: ZodFirstPartyTypeKind.ZodNullable,
        ...processCreateParams(params),
    });
};
class ZodDefault extends ZodType {
    _parse(input) {
        const { ctx } = this._processInputParams(input);
        let data = ctx.data;
        if (ctx.parsedType === _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.undefined) {
            data = this._def.defaultValue();
        }
        return this._def.innerType._parse({
            data,
            path: ctx.path,
            parent: ctx,
        });
    }
    removeDefault() {
        return this._def.innerType;
    }
}
ZodDefault.create = (type, params) => {
    return new ZodDefault({
        innerType: type,
        typeName: ZodFirstPartyTypeKind.ZodDefault,
        defaultValue: typeof params.default === "function" ? params.default : () => params.default,
        ...processCreateParams(params),
    });
};
class ZodCatch extends ZodType {
    _parse(input) {
        const { ctx } = this._processInputParams(input);
        // newCtx is used to not collect issues from inner types in ctx
        const newCtx = {
            ...ctx,
            common: {
                ...ctx.common,
                issues: [],
            },
        };
        const result = this._def.innerType._parse({
            data: newCtx.data,
            path: newCtx.path,
            parent: {
                ...newCtx,
            },
        });
        if ((0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.isAsync)(result)) {
            return result.then((result) => {
                return {
                    status: "valid",
                    value: result.status === "valid"
                        ? result.value
                        : this._def.catchValue({
                            get error() {
                                return new _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodError(newCtx.common.issues);
                            },
                            input: newCtx.data,
                        }),
                };
            });
        }
        else {
            return {
                status: "valid",
                value: result.status === "valid"
                    ? result.value
                    : this._def.catchValue({
                        get error() {
                            return new _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodError(newCtx.common.issues);
                        },
                        input: newCtx.data,
                    }),
            };
        }
    }
    removeCatch() {
        return this._def.innerType;
    }
}
ZodCatch.create = (type, params) => {
    return new ZodCatch({
        innerType: type,
        typeName: ZodFirstPartyTypeKind.ZodCatch,
        catchValue: typeof params.catch === "function" ? params.catch : () => params.catch,
        ...processCreateParams(params),
    });
};
class ZodNaN extends ZodType {
    _parse(input) {
        const parsedType = this._getType(input);
        if (parsedType !== _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.nan) {
            const ctx = this._getOrReturnCtx(input);
            (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.addIssueToContext)(ctx, {
                code: _ZodError_js__WEBPACK_IMPORTED_MODULE_0__.ZodIssueCode.invalid_type,
                expected: _helpers_util_js__WEBPACK_IMPORTED_MODULE_5__.ZodParsedType.nan,
                received: ctx.parsedType,
            });
            return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
        }
        return { status: "valid", value: input.data };
    }
}
ZodNaN.create = (params) => {
    return new ZodNaN({
        typeName: ZodFirstPartyTypeKind.ZodNaN,
        ...processCreateParams(params),
    });
};
const BRAND = Symbol("zod_brand");
class ZodBranded extends ZodType {
    _parse(input) {
        const { ctx } = this._processInputParams(input);
        const data = ctx.data;
        return this._def.type._parse({
            data,
            path: ctx.path,
            parent: ctx,
        });
    }
    unwrap() {
        return this._def.type;
    }
}
class ZodPipeline extends ZodType {
    _parse(input) {
        const { status, ctx } = this._processInputParams(input);
        if (ctx.common.async) {
            const handleAsync = async () => {
                const inResult = await this._def.in._parseAsync({
                    data: ctx.data,
                    path: ctx.path,
                    parent: ctx,
                });
                if (inResult.status === "aborted")
                    return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
                if (inResult.status === "dirty") {
                    status.dirty();
                    return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.DIRTY)(inResult.value);
                }
                else {
                    return this._def.out._parseAsync({
                        data: inResult.value,
                        path: ctx.path,
                        parent: ctx,
                    });
                }
            };
            return handleAsync();
        }
        else {
            const inResult = this._def.in._parseSync({
                data: ctx.data,
                path: ctx.path,
                parent: ctx,
            });
            if (inResult.status === "aborted")
                return _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;
            if (inResult.status === "dirty") {
                status.dirty();
                return {
                    status: "dirty",
                    value: inResult.value,
                };
            }
            else {
                return this._def.out._parseSync({
                    data: inResult.value,
                    path: ctx.path,
                    parent: ctx,
                });
            }
        }
    }
    static create(a, b) {
        return new ZodPipeline({
            in: a,
            out: b,
            typeName: ZodFirstPartyTypeKind.ZodPipeline,
        });
    }
}
class ZodReadonly extends ZodType {
    _parse(input) {
        const result = this._def.innerType._parse(input);
        const freeze = (data) => {
            if ((0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.isValid)(data)) {
                data.value = Object.freeze(data.value);
            }
            return data;
        };
        return (0,_helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.isAsync)(result) ? result.then((data) => freeze(data)) : freeze(result);
    }
    unwrap() {
        return this._def.innerType;
    }
}
ZodReadonly.create = (type, params) => {
    return new ZodReadonly({
        innerType: type,
        typeName: ZodFirstPartyTypeKind.ZodReadonly,
        ...processCreateParams(params),
    });
};
////////////////////////////////////////
////////////////////////////////////////
//////////                    //////////
//////////      z.custom      //////////
//////////                    //////////
////////////////////////////////////////
////////////////////////////////////////
function cleanParams(params, data) {
    const p = typeof params === "function" ? params(data) : typeof params === "string" ? { message: params } : params;
    const p2 = typeof p === "string" ? { message: p } : p;
    return p2;
}
function custom(check, _params = {}, 
/**
 * @deprecated
 *
 * Pass `fatal` into the params object instead:
 *
 * ```ts
 * z.string().custom((val) => val.length > 5, { fatal: false })
 * ```
 *
 */
fatal) {
    if (check)
        return ZodAny.create().superRefine((data, ctx) => {
            const r = check(data);
            if (r instanceof Promise) {
                return r.then((r) => {
                    if (!r) {
                        const params = cleanParams(_params, data);
                        const _fatal = params.fatal ?? fatal ?? true;
                        ctx.addIssue({ code: "custom", ...params, fatal: _fatal });
                    }
                });
            }
            if (!r) {
                const params = cleanParams(_params, data);
                const _fatal = params.fatal ?? fatal ?? true;
                ctx.addIssue({ code: "custom", ...params, fatal: _fatal });
            }
            return;
        });
    return ZodAny.create();
}

const late = {
    object: ZodObject.lazycreate,
};
var ZodFirstPartyTypeKind;
(function (ZodFirstPartyTypeKind) {
    ZodFirstPartyTypeKind["ZodString"] = "ZodString";
    ZodFirstPartyTypeKind["ZodNumber"] = "ZodNumber";
    ZodFirstPartyTypeKind["ZodNaN"] = "ZodNaN";
    ZodFirstPartyTypeKind["ZodBigInt"] = "ZodBigInt";
    ZodFirstPartyTypeKind["ZodBoolean"] = "ZodBoolean";
    ZodFirstPartyTypeKind["ZodDate"] = "ZodDate";
    ZodFirstPartyTypeKind["ZodSymbol"] = "ZodSymbol";
    ZodFirstPartyTypeKind["ZodUndefined"] = "ZodUndefined";
    ZodFirstPartyTypeKind["ZodNull"] = "ZodNull";
    ZodFirstPartyTypeKind["ZodAny"] = "ZodAny";
    ZodFirstPartyTypeKind["ZodUnknown"] = "ZodUnknown";
    ZodFirstPartyTypeKind["ZodNever"] = "ZodNever";
    ZodFirstPartyTypeKind["ZodVoid"] = "ZodVoid";
    ZodFirstPartyTypeKind["ZodArray"] = "ZodArray";
    ZodFirstPartyTypeKind["ZodObject"] = "ZodObject";
    ZodFirstPartyTypeKind["ZodUnion"] = "ZodUnion";
    ZodFirstPartyTypeKind["ZodDiscriminatedUnion"] = "ZodDiscriminatedUnion";
    ZodFirstPartyTypeKind["ZodIntersection"] = "ZodIntersection";
    ZodFirstPartyTypeKind["ZodTuple"] = "ZodTuple";
    ZodFirstPartyTypeKind["ZodRecord"] = "ZodRecord";
    ZodFirstPartyTypeKind["ZodMap"] = "ZodMap";
    ZodFirstPartyTypeKind["ZodSet"] = "ZodSet";
    ZodFirstPartyTypeKind["ZodFunction"] = "ZodFunction";
    ZodFirstPartyTypeKind["ZodLazy"] = "ZodLazy";
    ZodFirstPartyTypeKind["ZodLiteral"] = "ZodLiteral";
    ZodFirstPartyTypeKind["ZodEnum"] = "ZodEnum";
    ZodFirstPartyTypeKind["ZodEffects"] = "ZodEffects";
    ZodFirstPartyTypeKind["ZodNativeEnum"] = "ZodNativeEnum";
    ZodFirstPartyTypeKind["ZodOptional"] = "ZodOptional";
    ZodFirstPartyTypeKind["ZodNullable"] = "ZodNullable";
    ZodFirstPartyTypeKind["ZodDefault"] = "ZodDefault";
    ZodFirstPartyTypeKind["ZodCatch"] = "ZodCatch";
    ZodFirstPartyTypeKind["ZodPromise"] = "ZodPromise";
    ZodFirstPartyTypeKind["ZodBranded"] = "ZodBranded";
    ZodFirstPartyTypeKind["ZodPipeline"] = "ZodPipeline";
    ZodFirstPartyTypeKind["ZodReadonly"] = "ZodReadonly";
})(ZodFirstPartyTypeKind || (ZodFirstPartyTypeKind = {}));
// requires TS 4.4+
class Class {
    constructor(..._) { }
}
const instanceOfType = (
// const instanceOfType = <T extends new (...args: any[]) => any>(
cls, params = {
    message: `Input not instance of ${cls.name}`,
}) => custom((data) => data instanceof cls, params);
const stringType = ZodString.create;
const numberType = ZodNumber.create;
const nanType = ZodNaN.create;
const bigIntType = ZodBigInt.create;
const booleanType = ZodBoolean.create;
const dateType = ZodDate.create;
const symbolType = ZodSymbol.create;
const undefinedType = ZodUndefined.create;
const nullType = ZodNull.create;
const anyType = ZodAny.create;
const unknownType = ZodUnknown.create;
const neverType = ZodNever.create;
const voidType = ZodVoid.create;
const arrayType = ZodArray.create;
const objectType = ZodObject.create;
const strictObjectType = ZodObject.strictCreate;
const unionType = ZodUnion.create;
const discriminatedUnionType = ZodDiscriminatedUnion.create;
const intersectionType = ZodIntersection.create;
const tupleType = ZodTuple.create;
const recordType = ZodRecord.create;
const mapType = ZodMap.create;
const setType = ZodSet.create;
const functionType = ZodFunction.create;
const lazyType = ZodLazy.create;
const literalType = ZodLiteral.create;
const enumType = ZodEnum.create;
const nativeEnumType = ZodNativeEnum.create;
const promiseType = ZodPromise.create;
const effectsType = ZodEffects.create;
const optionalType = ZodOptional.create;
const nullableType = ZodNullable.create;
const preprocessType = ZodEffects.createWithPreprocess;
const pipelineType = ZodPipeline.create;
const ostring = () => stringType().optional();
const onumber = () => numberType().optional();
const oboolean = () => booleanType().optional();
const coerce = {
    string: ((arg) => ZodString.create({ ...arg, coerce: true })),
    number: ((arg) => ZodNumber.create({ ...arg, coerce: true })),
    boolean: ((arg) => ZodBoolean.create({
        ...arg,
        coerce: true,
    })),
    bigint: ((arg) => ZodBigInt.create({ ...arg, coerce: true })),
    date: ((arg) => ZodDate.create({ ...arg, coerce: true })),
};

const NEVER = _helpers_parseUtil_js__WEBPACK_IMPORTED_MODULE_4__.INVALID;


/***/ }),

/***/ "./node_modules/zod/v4/classic/checks.js":
/*!***********************************************!*\
  !*** ./node_modules/zod/v4/classic/checks.js ***!
  \***********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   endsWith: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._endsWith; },
/* harmony export */   gt: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._gt; },
/* harmony export */   gte: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._gte; },
/* harmony export */   includes: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._includes; },
/* harmony export */   length: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._length; },
/* harmony export */   lowercase: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._lowercase; },
/* harmony export */   lt: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._lt; },
/* harmony export */   lte: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._lte; },
/* harmony export */   maxLength: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._maxLength; },
/* harmony export */   maxSize: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._maxSize; },
/* harmony export */   mime: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._mime; },
/* harmony export */   minLength: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._minLength; },
/* harmony export */   minSize: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._minSize; },
/* harmony export */   multipleOf: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._multipleOf; },
/* harmony export */   negative: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._negative; },
/* harmony export */   nonnegative: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._nonnegative; },
/* harmony export */   nonpositive: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._nonpositive; },
/* harmony export */   normalize: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._normalize; },
/* harmony export */   overwrite: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._overwrite; },
/* harmony export */   positive: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._positive; },
/* harmony export */   property: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._property; },
/* harmony export */   regex: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._regex; },
/* harmony export */   size: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._size; },
/* harmony export */   startsWith: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._startsWith; },
/* harmony export */   toLowerCase: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._toLowerCase; },
/* harmony export */   toUpperCase: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._toUpperCase; },
/* harmony export */   trim: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._trim; },
/* harmony export */   uppercase: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._uppercase; }
/* harmony export */ });
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/api.js");



/***/ }),

/***/ "./node_modules/zod/v4/classic/coerce.js":
/*!***********************************************!*\
  !*** ./node_modules/zod/v4/classic/coerce.js ***!
  \***********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   bigint: function() { return /* binding */ bigint; },
/* harmony export */   boolean: function() { return /* binding */ boolean; },
/* harmony export */   date: function() { return /* binding */ date; },
/* harmony export */   number: function() { return /* binding */ number; },
/* harmony export */   string: function() { return /* binding */ string; }
/* harmony export */ });
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/api.js");
/* harmony import */ var _schemas_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./schemas.js */ "./node_modules/zod/v4/classic/schemas.js");


function string(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_0__._coercedString(_schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodString, params);
}
function number(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_0__._coercedNumber(_schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodNumber, params);
}
function boolean(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_0__._coercedBoolean(_schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodBoolean, params);
}
function bigint(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_0__._coercedBigint(_schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodBigInt, params);
}
function date(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_0__._coercedDate(_schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodDate, params);
}


/***/ }),

/***/ "./node_modules/zod/v4/classic/compat.js":
/*!***********************************************!*\
  !*** ./node_modules/zod/v4/classic/compat.js ***!
  \***********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   $brand: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$brand; },
/* harmony export */   ZodIssueCode: function() { return /* binding */ ZodIssueCode; },
/* harmony export */   config: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.config; },
/* harmony export */   getErrorMap: function() { return /* binding */ getErrorMap; },
/* harmony export */   setErrorMap: function() { return /* binding */ setErrorMap; }
/* harmony export */ });
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/core.js");
// Zod 3 compat layer

/** @deprecated Use the raw string literal codes instead, e.g. "invalid_type". */
const ZodIssueCode = {
    invalid_type: "invalid_type",
    too_big: "too_big",
    too_small: "too_small",
    invalid_format: "invalid_format",
    not_multiple_of: "not_multiple_of",
    unrecognized_keys: "unrecognized_keys",
    invalid_union: "invalid_union",
    invalid_key: "invalid_key",
    invalid_element: "invalid_element",
    invalid_value: "invalid_value",
    custom: "custom",
};

/** @deprecated Use `z.config(params)` instead. */
function setErrorMap(map) {
    _core_index_js__WEBPACK_IMPORTED_MODULE_0__.config({
        customError: map,
    });
}
/** @deprecated Use `z.config()` instead. */
function getErrorMap() {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_0__.config().customError;
}


/***/ }),

/***/ "./node_modules/zod/v4/classic/errors.js":
/*!***********************************************!*\
  !*** ./node_modules/zod/v4/classic/errors.js ***!
  \***********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ZodError: function() { return /* binding */ ZodError; },
/* harmony export */   ZodRealError: function() { return /* binding */ ZodRealError; }
/* harmony export */ });
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/core.js");
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/errors.js");


const initializer = (inst, issues) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodError.init(inst, issues);
    inst.name = "ZodError";
    Object.defineProperties(inst, {
        format: {
            value: (mapper) => _core_index_js__WEBPACK_IMPORTED_MODULE_1__.formatError(inst, mapper),
            // enumerable: false,
        },
        flatten: {
            value: (mapper) => _core_index_js__WEBPACK_IMPORTED_MODULE_1__.flattenError(inst, mapper),
            // enumerable: false,
        },
        addIssue: {
            value: (issue) => inst.issues.push(issue),
            // enumerable: false,
        },
        addIssues: {
            value: (issues) => inst.issues.push(...issues),
            // enumerable: false,
        },
        isEmpty: {
            get() {
                return inst.issues.length === 0;
            },
            // enumerable: false,
        },
    });
    // Object.defineProperty(inst, "isEmpty", {
    //   get() {
    //     return inst.issues.length === 0;
    //   },
    // });
};
const ZodError = _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodError", initializer);
const ZodRealError = _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodError", initializer, {
    Parent: Error,
});
// /** @deprecated Use `z.core.$ZodErrorMapCtx` instead. */
// export type ErrorMapCtx = core.$ZodErrorMapCtx;


/***/ }),

/***/ "./node_modules/zod/v4/classic/external.js":
/*!*************************************************!*\
  !*** ./node_modules/zod/v4/classic/external.js ***!
  \*************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   $brand: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_6__.$brand; },
/* harmony export */   $input: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_11__.$input; },
/* harmony export */   $output: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_11__.$output; },
/* harmony export */   NEVER: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_6__.NEVER; },
/* harmony export */   TimePrecision: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_13__.TimePrecision; },
/* harmony export */   ZodAny: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodAny; },
/* harmony export */   ZodArray: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodArray; },
/* harmony export */   ZodBase64: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodBase64; },
/* harmony export */   ZodBase64URL: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodBase64URL; },
/* harmony export */   ZodBigInt: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodBigInt; },
/* harmony export */   ZodBigIntFormat: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodBigIntFormat; },
/* harmony export */   ZodBoolean: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodBoolean; },
/* harmony export */   ZodCIDRv4: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodCIDRv4; },
/* harmony export */   ZodCIDRv6: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodCIDRv6; },
/* harmony export */   ZodCUID: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodCUID; },
/* harmony export */   ZodCUID2: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodCUID2; },
/* harmony export */   ZodCatch: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodCatch; },
/* harmony export */   ZodCustom: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodCustom; },
/* harmony export */   ZodCustomStringFormat: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodCustomStringFormat; },
/* harmony export */   ZodDate: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodDate; },
/* harmony export */   ZodDefault: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodDefault; },
/* harmony export */   ZodDiscriminatedUnion: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodDiscriminatedUnion; },
/* harmony export */   ZodE164: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodE164; },
/* harmony export */   ZodEmail: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodEmail; },
/* harmony export */   ZodEmoji: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodEmoji; },
/* harmony export */   ZodEnum: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodEnum; },
/* harmony export */   ZodError: function() { return /* reexport safe */ _errors_js__WEBPACK_IMPORTED_MODULE_3__.ZodError; },
/* harmony export */   ZodFile: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodFile; },
/* harmony export */   ZodGUID: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodGUID; },
/* harmony export */   ZodIPv4: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodIPv4; },
/* harmony export */   ZodIPv6: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodIPv6; },
/* harmony export */   ZodISODate: function() { return /* reexport safe */ _iso_js__WEBPACK_IMPORTED_MODULE_16__.ZodISODate; },
/* harmony export */   ZodISODateTime: function() { return /* reexport safe */ _iso_js__WEBPACK_IMPORTED_MODULE_16__.ZodISODateTime; },
/* harmony export */   ZodISODuration: function() { return /* reexport safe */ _iso_js__WEBPACK_IMPORTED_MODULE_16__.ZodISODuration; },
/* harmony export */   ZodISOTime: function() { return /* reexport safe */ _iso_js__WEBPACK_IMPORTED_MODULE_16__.ZodISOTime; },
/* harmony export */   ZodIntersection: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodIntersection; },
/* harmony export */   ZodIssueCode: function() { return /* reexport safe */ _compat_js__WEBPACK_IMPORTED_MODULE_5__.ZodIssueCode; },
/* harmony export */   ZodJWT: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodJWT; },
/* harmony export */   ZodKSUID: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodKSUID; },
/* harmony export */   ZodLazy: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodLazy; },
/* harmony export */   ZodLiteral: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodLiteral; },
/* harmony export */   ZodMap: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodMap; },
/* harmony export */   ZodNaN: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodNaN; },
/* harmony export */   ZodNanoID: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodNanoID; },
/* harmony export */   ZodNever: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodNever; },
/* harmony export */   ZodNonOptional: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodNonOptional; },
/* harmony export */   ZodNull: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodNull; },
/* harmony export */   ZodNullable: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodNullable; },
/* harmony export */   ZodNumber: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodNumber; },
/* harmony export */   ZodNumberFormat: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodNumberFormat; },
/* harmony export */   ZodObject: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodObject; },
/* harmony export */   ZodOptional: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodOptional; },
/* harmony export */   ZodPipe: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodPipe; },
/* harmony export */   ZodPrefault: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodPrefault; },
/* harmony export */   ZodPromise: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodPromise; },
/* harmony export */   ZodReadonly: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodReadonly; },
/* harmony export */   ZodRealError: function() { return /* reexport safe */ _errors_js__WEBPACK_IMPORTED_MODULE_3__.ZodRealError; },
/* harmony export */   ZodRecord: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodRecord; },
/* harmony export */   ZodSet: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodSet; },
/* harmony export */   ZodString: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodString; },
/* harmony export */   ZodStringFormat: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodStringFormat; },
/* harmony export */   ZodSuccess: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodSuccess; },
/* harmony export */   ZodSymbol: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodSymbol; },
/* harmony export */   ZodTemplateLiteral: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodTemplateLiteral; },
/* harmony export */   ZodTransform: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodTransform; },
/* harmony export */   ZodTuple: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodTuple; },
/* harmony export */   ZodType: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodType; },
/* harmony export */   ZodULID: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodULID; },
/* harmony export */   ZodURL: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodURL; },
/* harmony export */   ZodUUID: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodUUID; },
/* harmony export */   ZodUndefined: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodUndefined; },
/* harmony export */   ZodUnion: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodUnion; },
/* harmony export */   ZodUnknown: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodUnknown; },
/* harmony export */   ZodVoid: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodVoid; },
/* harmony export */   ZodXID: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ZodXID; },
/* harmony export */   _ZodString: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__._ZodString; },
/* harmony export */   _default: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__._default; },
/* harmony export */   any: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.any; },
/* harmony export */   array: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.array; },
/* harmony export */   base64: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.base64; },
/* harmony export */   base64url: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.base64url; },
/* harmony export */   bigint: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.bigint; },
/* harmony export */   boolean: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.boolean; },
/* harmony export */   "catch": function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__["catch"]; },
/* harmony export */   check: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.check; },
/* harmony export */   cidrv4: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.cidrv4; },
/* harmony export */   cidrv6: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.cidrv6; },
/* harmony export */   clone: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_9__.clone; },
/* harmony export */   coerce: function() { return /* reexport module object */ _coerce_js__WEBPACK_IMPORTED_MODULE_17__; },
/* harmony export */   config: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_6__.config; },
/* harmony export */   core: function() { return /* reexport module object */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__; },
/* harmony export */   cuid: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.cuid; },
/* harmony export */   cuid2: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.cuid2; },
/* harmony export */   custom: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.custom; },
/* harmony export */   date: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.date; },
/* harmony export */   discriminatedUnion: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.discriminatedUnion; },
/* harmony export */   e164: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.e164; },
/* harmony export */   email: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.email; },
/* harmony export */   emoji: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.emoji; },
/* harmony export */   endsWith: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.endsWith; },
/* harmony export */   "enum": function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__["enum"]; },
/* harmony export */   file: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.file; },
/* harmony export */   flattenError: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_8__.flattenError; },
/* harmony export */   float32: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.float32; },
/* harmony export */   float64: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.float64; },
/* harmony export */   formatError: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_8__.formatError; },
/* harmony export */   "function": function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_12__["function"]; },
/* harmony export */   getErrorMap: function() { return /* reexport safe */ _compat_js__WEBPACK_IMPORTED_MODULE_5__.getErrorMap; },
/* harmony export */   globalRegistry: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_11__.globalRegistry; },
/* harmony export */   gt: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.gt; },
/* harmony export */   gte: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.gte; },
/* harmony export */   guid: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.guid; },
/* harmony export */   includes: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.includes; },
/* harmony export */   "instanceof": function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__["instanceof"]; },
/* harmony export */   int: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.int; },
/* harmony export */   int32: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.int32; },
/* harmony export */   int64: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.int64; },
/* harmony export */   intersection: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.intersection; },
/* harmony export */   ipv4: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ipv4; },
/* harmony export */   ipv6: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ipv6; },
/* harmony export */   iso: function() { return /* reexport module object */ _iso_js__WEBPACK_IMPORTED_MODULE_16__; },
/* harmony export */   json: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.json; },
/* harmony export */   jwt: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.jwt; },
/* harmony export */   keyof: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.keyof; },
/* harmony export */   ksuid: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ksuid; },
/* harmony export */   lazy: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.lazy; },
/* harmony export */   length: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.length; },
/* harmony export */   literal: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.literal; },
/* harmony export */   locales: function() { return /* reexport module object */ _locales_index_js__WEBPACK_IMPORTED_MODULE_15__; },
/* harmony export */   looseObject: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.looseObject; },
/* harmony export */   lowercase: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.lowercase; },
/* harmony export */   lt: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.lt; },
/* harmony export */   lte: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.lte; },
/* harmony export */   map: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.map; },
/* harmony export */   maxLength: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.maxLength; },
/* harmony export */   maxSize: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.maxSize; },
/* harmony export */   mime: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.mime; },
/* harmony export */   minLength: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.minLength; },
/* harmony export */   minSize: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.minSize; },
/* harmony export */   multipleOf: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.multipleOf; },
/* harmony export */   nan: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.nan; },
/* harmony export */   nanoid: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.nanoid; },
/* harmony export */   nativeEnum: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.nativeEnum; },
/* harmony export */   negative: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.negative; },
/* harmony export */   never: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.never; },
/* harmony export */   nonnegative: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.nonnegative; },
/* harmony export */   nonoptional: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.nonoptional; },
/* harmony export */   nonpositive: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.nonpositive; },
/* harmony export */   normalize: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.normalize; },
/* harmony export */   "null": function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__["null"]; },
/* harmony export */   nullable: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.nullable; },
/* harmony export */   nullish: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.nullish; },
/* harmony export */   number: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.number; },
/* harmony export */   object: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.object; },
/* harmony export */   optional: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.optional; },
/* harmony export */   overwrite: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.overwrite; },
/* harmony export */   parse: function() { return /* reexport safe */ _parse_js__WEBPACK_IMPORTED_MODULE_4__.parse; },
/* harmony export */   parseAsync: function() { return /* reexport safe */ _parse_js__WEBPACK_IMPORTED_MODULE_4__.parseAsync; },
/* harmony export */   partialRecord: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.partialRecord; },
/* harmony export */   pipe: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.pipe; },
/* harmony export */   positive: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.positive; },
/* harmony export */   prefault: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.prefault; },
/* harmony export */   preprocess: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.preprocess; },
/* harmony export */   prettifyError: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_8__.prettifyError; },
/* harmony export */   promise: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.promise; },
/* harmony export */   property: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.property; },
/* harmony export */   readonly: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.readonly; },
/* harmony export */   record: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.record; },
/* harmony export */   refine: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.refine; },
/* harmony export */   regex: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.regex; },
/* harmony export */   regexes: function() { return /* reexport module object */ _core_index_js__WEBPACK_IMPORTED_MODULE_10__; },
/* harmony export */   registry: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_11__.registry; },
/* harmony export */   safeParse: function() { return /* reexport safe */ _parse_js__WEBPACK_IMPORTED_MODULE_4__.safeParse; },
/* harmony export */   safeParseAsync: function() { return /* reexport safe */ _parse_js__WEBPACK_IMPORTED_MODULE_4__.safeParseAsync; },
/* harmony export */   set: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.set; },
/* harmony export */   setErrorMap: function() { return /* reexport safe */ _compat_js__WEBPACK_IMPORTED_MODULE_5__.setErrorMap; },
/* harmony export */   size: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.size; },
/* harmony export */   startsWith: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.startsWith; },
/* harmony export */   strictObject: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.strictObject; },
/* harmony export */   string: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.string; },
/* harmony export */   stringFormat: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.stringFormat; },
/* harmony export */   stringbool: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.stringbool; },
/* harmony export */   success: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.success; },
/* harmony export */   superRefine: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.superRefine; },
/* harmony export */   symbol: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.symbol; },
/* harmony export */   templateLiteral: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.templateLiteral; },
/* harmony export */   toJSONSchema: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_14__.toJSONSchema; },
/* harmony export */   toLowerCase: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.toLowerCase; },
/* harmony export */   toUpperCase: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.toUpperCase; },
/* harmony export */   transform: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.transform; },
/* harmony export */   treeifyError: function() { return /* reexport safe */ _core_index_js__WEBPACK_IMPORTED_MODULE_8__.treeifyError; },
/* harmony export */   trim: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.trim; },
/* harmony export */   tuple: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.tuple; },
/* harmony export */   uint32: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.uint32; },
/* harmony export */   uint64: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.uint64; },
/* harmony export */   ulid: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.ulid; },
/* harmony export */   undefined: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.undefined; },
/* harmony export */   union: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.union; },
/* harmony export */   unknown: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.unknown; },
/* harmony export */   uppercase: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_2__.uppercase; },
/* harmony export */   url: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.url; },
/* harmony export */   uuid: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.uuid; },
/* harmony export */   uuidv4: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.uuidv4; },
/* harmony export */   uuidv6: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.uuidv6; },
/* harmony export */   uuidv7: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.uuidv7; },
/* harmony export */   "void": function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__["void"]; },
/* harmony export */   xid: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_1__.xid; }
/* harmony export */ });
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/index.js");
/* harmony import */ var _schemas_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./schemas.js */ "./node_modules/zod/v4/classic/schemas.js");
/* harmony import */ var _checks_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./checks.js */ "./node_modules/zod/v4/classic/checks.js");
/* harmony import */ var _errors_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./errors.js */ "./node_modules/zod/v4/classic/errors.js");
/* harmony import */ var _parse_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./parse.js */ "./node_modules/zod/v4/classic/parse.js");
/* harmony import */ var _compat_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./compat.js */ "./node_modules/zod/v4/classic/compat.js");
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/core.js");
/* harmony import */ var _locales_en_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../locales/en.js */ "./node_modules/zod/v4/locales/en.js");
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/errors.js");
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/util.js");
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/regexes.js");
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/registries.js");
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/function.js");
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/api.js");
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/to-json-schema.js");
/* harmony import */ var _locales_index_js__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ../locales/index.js */ "./node_modules/zod/v4/locales/index.js");
/* harmony import */ var _iso_js__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./iso.js */ "./node_modules/zod/v4/classic/iso.js");
/* harmony import */ var _coerce_js__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./coerce.js */ "./node_modules/zod/v4/classic/coerce.js");






// zod-specified


(0,_core_index_js__WEBPACK_IMPORTED_MODULE_6__.config)((0,_locales_en_js__WEBPACK_IMPORTED_MODULE_7__["default"])());


// iso
// must be exported from top-level
// https://github.com/colinhacks/zod/issues/4491





/***/ }),

/***/ "./node_modules/zod/v4/classic/iso.js":
/*!********************************************!*\
  !*** ./node_modules/zod/v4/classic/iso.js ***!
  \********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ZodISODate: function() { return /* binding */ ZodISODate; },
/* harmony export */   ZodISODateTime: function() { return /* binding */ ZodISODateTime; },
/* harmony export */   ZodISODuration: function() { return /* binding */ ZodISODuration; },
/* harmony export */   ZodISOTime: function() { return /* binding */ ZodISOTime; },
/* harmony export */   date: function() { return /* binding */ date; },
/* harmony export */   datetime: function() { return /* binding */ datetime; },
/* harmony export */   duration: function() { return /* binding */ duration; },
/* harmony export */   time: function() { return /* binding */ time; }
/* harmony export */ });
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/core.js");
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/schemas.js");
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/api.js");
/* harmony import */ var _schemas_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./schemas.js */ "./node_modules/zod/v4/classic/schemas.js");


const ZodISODateTime = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodISODateTime", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodISODateTime.init(inst, def);
    _schemas_js__WEBPACK_IMPORTED_MODULE_3__.ZodStringFormat.init(inst, def);
});
function datetime(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_2__._isoDateTime(ZodISODateTime, params);
}
const ZodISODate = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodISODate", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodISODate.init(inst, def);
    _schemas_js__WEBPACK_IMPORTED_MODULE_3__.ZodStringFormat.init(inst, def);
});
function date(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_2__._isoDate(ZodISODate, params);
}
const ZodISOTime = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodISOTime", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodISOTime.init(inst, def);
    _schemas_js__WEBPACK_IMPORTED_MODULE_3__.ZodStringFormat.init(inst, def);
});
function time(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_2__._isoTime(ZodISOTime, params);
}
const ZodISODuration = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodISODuration", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodISODuration.init(inst, def);
    _schemas_js__WEBPACK_IMPORTED_MODULE_3__.ZodStringFormat.init(inst, def);
});
function duration(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_2__._isoDuration(ZodISODuration, params);
}


/***/ }),

/***/ "./node_modules/zod/v4/classic/parse.js":
/*!**********************************************!*\
  !*** ./node_modules/zod/v4/classic/parse.js ***!
  \**********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   parse: function() { return /* binding */ parse; },
/* harmony export */   parseAsync: function() { return /* binding */ parseAsync; },
/* harmony export */   safeParse: function() { return /* binding */ safeParse; },
/* harmony export */   safeParseAsync: function() { return /* binding */ safeParseAsync; }
/* harmony export */ });
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/parse.js");
/* harmony import */ var _errors_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./errors.js */ "./node_modules/zod/v4/classic/errors.js");


const parse = /* @__PURE__ */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._parse(_errors_js__WEBPACK_IMPORTED_MODULE_1__.ZodRealError);
const parseAsync = /* @__PURE__ */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._parseAsync(_errors_js__WEBPACK_IMPORTED_MODULE_1__.ZodRealError);
const safeParse = /* @__PURE__ */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._safeParse(_errors_js__WEBPACK_IMPORTED_MODULE_1__.ZodRealError);
const safeParseAsync = /* @__PURE__ */ _core_index_js__WEBPACK_IMPORTED_MODULE_0__._safeParseAsync(_errors_js__WEBPACK_IMPORTED_MODULE_1__.ZodRealError);


/***/ }),

/***/ "./node_modules/zod/v4/classic/schemas.js":
/*!************************************************!*\
  !*** ./node_modules/zod/v4/classic/schemas.js ***!
  \************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ZodAny: function() { return /* binding */ ZodAny; },
/* harmony export */   ZodArray: function() { return /* binding */ ZodArray; },
/* harmony export */   ZodBase64: function() { return /* binding */ ZodBase64; },
/* harmony export */   ZodBase64URL: function() { return /* binding */ ZodBase64URL; },
/* harmony export */   ZodBigInt: function() { return /* binding */ ZodBigInt; },
/* harmony export */   ZodBigIntFormat: function() { return /* binding */ ZodBigIntFormat; },
/* harmony export */   ZodBoolean: function() { return /* binding */ ZodBoolean; },
/* harmony export */   ZodCIDRv4: function() { return /* binding */ ZodCIDRv4; },
/* harmony export */   ZodCIDRv6: function() { return /* binding */ ZodCIDRv6; },
/* harmony export */   ZodCUID: function() { return /* binding */ ZodCUID; },
/* harmony export */   ZodCUID2: function() { return /* binding */ ZodCUID2; },
/* harmony export */   ZodCatch: function() { return /* binding */ ZodCatch; },
/* harmony export */   ZodCustom: function() { return /* binding */ ZodCustom; },
/* harmony export */   ZodCustomStringFormat: function() { return /* binding */ ZodCustomStringFormat; },
/* harmony export */   ZodDate: function() { return /* binding */ ZodDate; },
/* harmony export */   ZodDefault: function() { return /* binding */ ZodDefault; },
/* harmony export */   ZodDiscriminatedUnion: function() { return /* binding */ ZodDiscriminatedUnion; },
/* harmony export */   ZodE164: function() { return /* binding */ ZodE164; },
/* harmony export */   ZodEmail: function() { return /* binding */ ZodEmail; },
/* harmony export */   ZodEmoji: function() { return /* binding */ ZodEmoji; },
/* harmony export */   ZodEnum: function() { return /* binding */ ZodEnum; },
/* harmony export */   ZodFile: function() { return /* binding */ ZodFile; },
/* harmony export */   ZodGUID: function() { return /* binding */ ZodGUID; },
/* harmony export */   ZodIPv4: function() { return /* binding */ ZodIPv4; },
/* harmony export */   ZodIPv6: function() { return /* binding */ ZodIPv6; },
/* harmony export */   ZodIntersection: function() { return /* binding */ ZodIntersection; },
/* harmony export */   ZodJWT: function() { return /* binding */ ZodJWT; },
/* harmony export */   ZodKSUID: function() { return /* binding */ ZodKSUID; },
/* harmony export */   ZodLazy: function() { return /* binding */ ZodLazy; },
/* harmony export */   ZodLiteral: function() { return /* binding */ ZodLiteral; },
/* harmony export */   ZodMap: function() { return /* binding */ ZodMap; },
/* harmony export */   ZodNaN: function() { return /* binding */ ZodNaN; },
/* harmony export */   ZodNanoID: function() { return /* binding */ ZodNanoID; },
/* harmony export */   ZodNever: function() { return /* binding */ ZodNever; },
/* harmony export */   ZodNonOptional: function() { return /* binding */ ZodNonOptional; },
/* harmony export */   ZodNull: function() { return /* binding */ ZodNull; },
/* harmony export */   ZodNullable: function() { return /* binding */ ZodNullable; },
/* harmony export */   ZodNumber: function() { return /* binding */ ZodNumber; },
/* harmony export */   ZodNumberFormat: function() { return /* binding */ ZodNumberFormat; },
/* harmony export */   ZodObject: function() { return /* binding */ ZodObject; },
/* harmony export */   ZodOptional: function() { return /* binding */ ZodOptional; },
/* harmony export */   ZodPipe: function() { return /* binding */ ZodPipe; },
/* harmony export */   ZodPrefault: function() { return /* binding */ ZodPrefault; },
/* harmony export */   ZodPromise: function() { return /* binding */ ZodPromise; },
/* harmony export */   ZodReadonly: function() { return /* binding */ ZodReadonly; },
/* harmony export */   ZodRecord: function() { return /* binding */ ZodRecord; },
/* harmony export */   ZodSet: function() { return /* binding */ ZodSet; },
/* harmony export */   ZodString: function() { return /* binding */ ZodString; },
/* harmony export */   ZodStringFormat: function() { return /* binding */ ZodStringFormat; },
/* harmony export */   ZodSuccess: function() { return /* binding */ ZodSuccess; },
/* harmony export */   ZodSymbol: function() { return /* binding */ ZodSymbol; },
/* harmony export */   ZodTemplateLiteral: function() { return /* binding */ ZodTemplateLiteral; },
/* harmony export */   ZodTransform: function() { return /* binding */ ZodTransform; },
/* harmony export */   ZodTuple: function() { return /* binding */ ZodTuple; },
/* harmony export */   ZodType: function() { return /* binding */ ZodType; },
/* harmony export */   ZodULID: function() { return /* binding */ ZodULID; },
/* harmony export */   ZodURL: function() { return /* binding */ ZodURL; },
/* harmony export */   ZodUUID: function() { return /* binding */ ZodUUID; },
/* harmony export */   ZodUndefined: function() { return /* binding */ ZodUndefined; },
/* harmony export */   ZodUnion: function() { return /* binding */ ZodUnion; },
/* harmony export */   ZodUnknown: function() { return /* binding */ ZodUnknown; },
/* harmony export */   ZodVoid: function() { return /* binding */ ZodVoid; },
/* harmony export */   ZodXID: function() { return /* binding */ ZodXID; },
/* harmony export */   _ZodString: function() { return /* binding */ _ZodString; },
/* harmony export */   _default: function() { return /* binding */ _default; },
/* harmony export */   any: function() { return /* binding */ any; },
/* harmony export */   array: function() { return /* binding */ array; },
/* harmony export */   base64: function() { return /* binding */ base64; },
/* harmony export */   base64url: function() { return /* binding */ base64url; },
/* harmony export */   bigint: function() { return /* binding */ bigint; },
/* harmony export */   boolean: function() { return /* binding */ boolean; },
/* harmony export */   "catch": function() { return /* binding */ _catch; },
/* harmony export */   check: function() { return /* binding */ check; },
/* harmony export */   cidrv4: function() { return /* binding */ cidrv4; },
/* harmony export */   cidrv6: function() { return /* binding */ cidrv6; },
/* harmony export */   cuid: function() { return /* binding */ cuid; },
/* harmony export */   cuid2: function() { return /* binding */ cuid2; },
/* harmony export */   custom: function() { return /* binding */ custom; },
/* harmony export */   date: function() { return /* binding */ date; },
/* harmony export */   discriminatedUnion: function() { return /* binding */ discriminatedUnion; },
/* harmony export */   e164: function() { return /* binding */ e164; },
/* harmony export */   email: function() { return /* binding */ email; },
/* harmony export */   emoji: function() { return /* binding */ emoji; },
/* harmony export */   "enum": function() { return /* binding */ _enum; },
/* harmony export */   file: function() { return /* binding */ file; },
/* harmony export */   float32: function() { return /* binding */ float32; },
/* harmony export */   float64: function() { return /* binding */ float64; },
/* harmony export */   guid: function() { return /* binding */ guid; },
/* harmony export */   "instanceof": function() { return /* binding */ _instanceof; },
/* harmony export */   int: function() { return /* binding */ int; },
/* harmony export */   int32: function() { return /* binding */ int32; },
/* harmony export */   int64: function() { return /* binding */ int64; },
/* harmony export */   intersection: function() { return /* binding */ intersection; },
/* harmony export */   ipv4: function() { return /* binding */ ipv4; },
/* harmony export */   ipv6: function() { return /* binding */ ipv6; },
/* harmony export */   json: function() { return /* binding */ json; },
/* harmony export */   jwt: function() { return /* binding */ jwt; },
/* harmony export */   keyof: function() { return /* binding */ keyof; },
/* harmony export */   ksuid: function() { return /* binding */ ksuid; },
/* harmony export */   lazy: function() { return /* binding */ lazy; },
/* harmony export */   literal: function() { return /* binding */ literal; },
/* harmony export */   looseObject: function() { return /* binding */ looseObject; },
/* harmony export */   map: function() { return /* binding */ map; },
/* harmony export */   nan: function() { return /* binding */ nan; },
/* harmony export */   nanoid: function() { return /* binding */ nanoid; },
/* harmony export */   nativeEnum: function() { return /* binding */ nativeEnum; },
/* harmony export */   never: function() { return /* binding */ never; },
/* harmony export */   nonoptional: function() { return /* binding */ nonoptional; },
/* harmony export */   "null": function() { return /* binding */ _null; },
/* harmony export */   nullable: function() { return /* binding */ nullable; },
/* harmony export */   nullish: function() { return /* binding */ nullish; },
/* harmony export */   number: function() { return /* binding */ number; },
/* harmony export */   object: function() { return /* binding */ object; },
/* harmony export */   optional: function() { return /* binding */ optional; },
/* harmony export */   partialRecord: function() { return /* binding */ partialRecord; },
/* harmony export */   pipe: function() { return /* binding */ pipe; },
/* harmony export */   prefault: function() { return /* binding */ prefault; },
/* harmony export */   preprocess: function() { return /* binding */ preprocess; },
/* harmony export */   promise: function() { return /* binding */ promise; },
/* harmony export */   readonly: function() { return /* binding */ readonly; },
/* harmony export */   record: function() { return /* binding */ record; },
/* harmony export */   refine: function() { return /* binding */ refine; },
/* harmony export */   set: function() { return /* binding */ set; },
/* harmony export */   strictObject: function() { return /* binding */ strictObject; },
/* harmony export */   string: function() { return /* binding */ string; },
/* harmony export */   stringFormat: function() { return /* binding */ stringFormat; },
/* harmony export */   stringbool: function() { return /* binding */ stringbool; },
/* harmony export */   success: function() { return /* binding */ success; },
/* harmony export */   superRefine: function() { return /* binding */ superRefine; },
/* harmony export */   symbol: function() { return /* binding */ symbol; },
/* harmony export */   templateLiteral: function() { return /* binding */ templateLiteral; },
/* harmony export */   transform: function() { return /* binding */ transform; },
/* harmony export */   tuple: function() { return /* binding */ tuple; },
/* harmony export */   uint32: function() { return /* binding */ uint32; },
/* harmony export */   uint64: function() { return /* binding */ uint64; },
/* harmony export */   ulid: function() { return /* binding */ ulid; },
/* harmony export */   undefined: function() { return /* binding */ _undefined; },
/* harmony export */   union: function() { return /* binding */ union; },
/* harmony export */   unknown: function() { return /* binding */ unknown; },
/* harmony export */   url: function() { return /* binding */ url; },
/* harmony export */   uuid: function() { return /* binding */ uuid; },
/* harmony export */   uuidv4: function() { return /* binding */ uuidv4; },
/* harmony export */   uuidv6: function() { return /* binding */ uuidv6; },
/* harmony export */   uuidv7: function() { return /* binding */ uuidv7; },
/* harmony export */   "void": function() { return /* binding */ _void; },
/* harmony export */   xid: function() { return /* binding */ xid; }
/* harmony export */ });
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/core.js");
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/schemas.js");
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/checks.js");
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/util.js");
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../core/index.js */ "./node_modules/zod/v4/core/registries.js");
/* harmony import */ var _core_index_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./checks.js */ "./node_modules/zod/v4/core/api.js");
/* harmony import */ var _iso_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./iso.js */ "./node_modules/zod/v4/classic/iso.js");
/* harmony import */ var _parse_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./parse.js */ "./node_modules/zod/v4/classic/parse.js");





const ZodType = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodType", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodType.init(inst, def);
    inst.def = def;
    Object.defineProperty(inst, "_def", { value: def });
    // base methods
    inst.check = (...checks) => {
        return inst.clone({
            ...def,
            checks: [
                ...(def.checks ?? []),
                ...checks.map((ch) => typeof ch === "function" ? { _zod: { check: ch, def: { check: "custom" }, onattach: [] } } : ch),
            ],
        }
        // { parent: true }
        );
    };
    inst.clone = (def, params) => _core_index_js__WEBPACK_IMPORTED_MODULE_3__.clone(inst, def, params);
    inst.brand = () => inst;
    inst.register = ((reg, meta) => {
        reg.add(inst, meta);
        return inst;
    });
    // parsing
    inst.parse = (data, params) => _parse_js__WEBPACK_IMPORTED_MODULE_7__.parse(inst, data, params, { callee: inst.parse });
    inst.safeParse = (data, params) => _parse_js__WEBPACK_IMPORTED_MODULE_7__.safeParse(inst, data, params);
    inst.parseAsync = async (data, params) => _parse_js__WEBPACK_IMPORTED_MODULE_7__.parseAsync(inst, data, params, { callee: inst.parseAsync });
    inst.safeParseAsync = async (data, params) => _parse_js__WEBPACK_IMPORTED_MODULE_7__.safeParseAsync(inst, data, params);
    inst.spa = inst.safeParseAsync;
    // refinements
    inst.refine = (check, params) => inst.check(refine(check, params));
    inst.superRefine = (refinement) => inst.check(superRefine(refinement));
    inst.overwrite = (fn) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._overwrite(fn));
    // wrappers
    inst.optional = () => optional(inst);
    inst.nullable = () => nullable(inst);
    inst.nullish = () => optional(nullable(inst));
    inst.nonoptional = (params) => nonoptional(inst, params);
    inst.array = () => array(inst);
    inst.or = (arg) => union([inst, arg]);
    inst.and = (arg) => intersection(inst, arg);
    inst.transform = (tx) => pipe(inst, transform(tx));
    inst.default = (def) => _default(inst, def);
    inst.prefault = (def) => prefault(inst, def);
    // inst.coalesce = (def, params) => coalesce(inst, def, params);
    inst.catch = (params) => _catch(inst, params);
    inst.pipe = (target) => pipe(inst, target);
    inst.readonly = () => readonly(inst);
    // meta
    inst.describe = (description) => {
        const cl = inst.clone();
        _core_index_js__WEBPACK_IMPORTED_MODULE_4__.globalRegistry.add(cl, { description });
        return cl;
    };
    Object.defineProperty(inst, "description", {
        get() {
            return _core_index_js__WEBPACK_IMPORTED_MODULE_4__.globalRegistry.get(inst)?.description;
        },
        configurable: true,
    });
    inst.meta = (...args) => {
        if (args.length === 0) {
            return _core_index_js__WEBPACK_IMPORTED_MODULE_4__.globalRegistry.get(inst);
        }
        const cl = inst.clone();
        _core_index_js__WEBPACK_IMPORTED_MODULE_4__.globalRegistry.add(cl, args[0]);
        return cl;
    };
    // helpers
    inst.isOptional = () => inst.safeParse(undefined).success;
    inst.isNullable = () => inst.safeParse(null).success;
    return inst;
});
/** @internal */
const _ZodString = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("_ZodString", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodString.init(inst, def);
    ZodType.init(inst, def);
    const bag = inst._zod.bag;
    inst.format = bag.format ?? null;
    inst.minLength = bag.minimum ?? null;
    inst.maxLength = bag.maximum ?? null;
    // validations
    inst.regex = (...args) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._regex(...args));
    inst.includes = (...args) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._includes(...args));
    inst.startsWith = (...args) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._startsWith(...args));
    inst.endsWith = (...args) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._endsWith(...args));
    inst.min = (...args) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._minLength(...args));
    inst.max = (...args) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._maxLength(...args));
    inst.length = (...args) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._length(...args));
    inst.nonempty = (...args) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._minLength(1, ...args));
    inst.lowercase = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._lowercase(params));
    inst.uppercase = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._uppercase(params));
    // transforms
    inst.trim = () => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._trim());
    inst.normalize = (...args) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._normalize(...args));
    inst.toLowerCase = () => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._toLowerCase());
    inst.toUpperCase = () => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._toUpperCase());
});
const ZodString = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodString", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodString.init(inst, def);
    _ZodString.init(inst, def);
    inst.email = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._email(ZodEmail, params));
    inst.url = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._url(ZodURL, params));
    inst.jwt = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._jwt(ZodJWT, params));
    inst.emoji = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._emoji(ZodEmoji, params));
    inst.guid = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._guid(ZodGUID, params));
    inst.uuid = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._uuid(ZodUUID, params));
    inst.uuidv4 = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._uuidv4(ZodUUID, params));
    inst.uuidv6 = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._uuidv6(ZodUUID, params));
    inst.uuidv7 = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._uuidv7(ZodUUID, params));
    inst.nanoid = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._nanoid(ZodNanoID, params));
    inst.guid = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._guid(ZodGUID, params));
    inst.cuid = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._cuid(ZodCUID, params));
    inst.cuid2 = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._cuid2(ZodCUID2, params));
    inst.ulid = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._ulid(ZodULID, params));
    inst.base64 = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._base64(ZodBase64, params));
    inst.base64url = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._base64url(ZodBase64URL, params));
    inst.xid = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._xid(ZodXID, params));
    inst.ksuid = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._ksuid(ZodKSUID, params));
    inst.ipv4 = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._ipv4(ZodIPv4, params));
    inst.ipv6 = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._ipv6(ZodIPv6, params));
    inst.cidrv4 = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._cidrv4(ZodCIDRv4, params));
    inst.cidrv6 = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._cidrv6(ZodCIDRv6, params));
    inst.e164 = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._e164(ZodE164, params));
    // iso
    inst.datetime = (params) => inst.check(_iso_js__WEBPACK_IMPORTED_MODULE_6__.datetime(params));
    inst.date = (params) => inst.check(_iso_js__WEBPACK_IMPORTED_MODULE_6__.date(params));
    inst.time = (params) => inst.check(_iso_js__WEBPACK_IMPORTED_MODULE_6__.time(params));
    inst.duration = (params) => inst.check(_iso_js__WEBPACK_IMPORTED_MODULE_6__.duration(params));
});
function string(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._string(ZodString, params);
}
const ZodStringFormat = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodStringFormat", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodStringFormat.init(inst, def);
    _ZodString.init(inst, def);
});
const ZodEmail = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodEmail", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodEmail.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function email(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._email(ZodEmail, params);
}
const ZodGUID = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodGUID", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodGUID.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function guid(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._guid(ZodGUID, params);
}
const ZodUUID = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodUUID", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodUUID.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function uuid(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._uuid(ZodUUID, params);
}
function uuidv4(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._uuidv4(ZodUUID, params);
}
// ZodUUIDv6
function uuidv6(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._uuidv6(ZodUUID, params);
}
// ZodUUIDv7
function uuidv7(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._uuidv7(ZodUUID, params);
}
const ZodURL = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodURL", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodURL.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function url(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._url(ZodURL, params);
}
const ZodEmoji = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodEmoji", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodEmoji.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function emoji(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._emoji(ZodEmoji, params);
}
const ZodNanoID = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodNanoID", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodNanoID.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function nanoid(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._nanoid(ZodNanoID, params);
}
const ZodCUID = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodCUID", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodCUID.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function cuid(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._cuid(ZodCUID, params);
}
const ZodCUID2 = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodCUID2", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodCUID2.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function cuid2(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._cuid2(ZodCUID2, params);
}
const ZodULID = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodULID", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodULID.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function ulid(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._ulid(ZodULID, params);
}
const ZodXID = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodXID", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodXID.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function xid(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._xid(ZodXID, params);
}
const ZodKSUID = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodKSUID", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodKSUID.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function ksuid(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._ksuid(ZodKSUID, params);
}
const ZodIPv4 = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodIPv4", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodIPv4.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function ipv4(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._ipv4(ZodIPv4, params);
}
const ZodIPv6 = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodIPv6", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodIPv6.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function ipv6(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._ipv6(ZodIPv6, params);
}
const ZodCIDRv4 = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodCIDRv4", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodCIDRv4.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function cidrv4(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._cidrv4(ZodCIDRv4, params);
}
const ZodCIDRv6 = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodCIDRv6", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodCIDRv6.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function cidrv6(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._cidrv6(ZodCIDRv6, params);
}
const ZodBase64 = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodBase64", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodBase64.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function base64(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._base64(ZodBase64, params);
}
const ZodBase64URL = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodBase64URL", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodBase64URL.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function base64url(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._base64url(ZodBase64URL, params);
}
const ZodE164 = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodE164", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodE164.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function e164(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._e164(ZodE164, params);
}
const ZodJWT = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodJWT", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodJWT.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function jwt(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._jwt(ZodJWT, params);
}
const ZodCustomStringFormat = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodCustomStringFormat", (inst, def) => {
    // ZodStringFormat.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodCustomStringFormat.init(inst, def);
    ZodStringFormat.init(inst, def);
});
function stringFormat(format, fnOrRegex, _params = {}) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._stringFormat(ZodCustomStringFormat, format, fnOrRegex, _params);
}
const ZodNumber = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodNumber", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodNumber.init(inst, def);
    ZodType.init(inst, def);
    inst.gt = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._gt(value, params));
    inst.gte = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._gte(value, params));
    inst.min = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._gte(value, params));
    inst.lt = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._lt(value, params));
    inst.lte = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._lte(value, params));
    inst.max = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._lte(value, params));
    inst.int = (params) => inst.check(int(params));
    inst.safe = (params) => inst.check(int(params));
    inst.positive = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._gt(0, params));
    inst.nonnegative = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._gte(0, params));
    inst.negative = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._lt(0, params));
    inst.nonpositive = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._lte(0, params));
    inst.multipleOf = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._multipleOf(value, params));
    inst.step = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._multipleOf(value, params));
    // inst.finite = (params) => inst.check(core.finite(params));
    inst.finite = () => inst;
    const bag = inst._zod.bag;
    inst.minValue =
        Math.max(bag.minimum ?? Number.NEGATIVE_INFINITY, bag.exclusiveMinimum ?? Number.NEGATIVE_INFINITY) ?? null;
    inst.maxValue =
        Math.min(bag.maximum ?? Number.POSITIVE_INFINITY, bag.exclusiveMaximum ?? Number.POSITIVE_INFINITY) ?? null;
    inst.isInt = (bag.format ?? "").includes("int") || Number.isSafeInteger(bag.multipleOf ?? 0.5);
    inst.isFinite = true;
    inst.format = bag.format ?? null;
});
function number(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._number(ZodNumber, params);
}
const ZodNumberFormat = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodNumberFormat", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodNumberFormat.init(inst, def);
    ZodNumber.init(inst, def);
});
function int(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._int(ZodNumberFormat, params);
}
function float32(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._float32(ZodNumberFormat, params);
}
function float64(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._float64(ZodNumberFormat, params);
}
function int32(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._int32(ZodNumberFormat, params);
}
function uint32(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._uint32(ZodNumberFormat, params);
}
const ZodBoolean = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodBoolean", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodBoolean.init(inst, def);
    ZodType.init(inst, def);
});
function boolean(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._boolean(ZodBoolean, params);
}
const ZodBigInt = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodBigInt", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodBigInt.init(inst, def);
    ZodType.init(inst, def);
    inst.gte = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._gte(value, params));
    inst.min = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._gte(value, params));
    inst.gt = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._gt(value, params));
    inst.gte = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._gte(value, params));
    inst.min = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._gte(value, params));
    inst.lt = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._lt(value, params));
    inst.lte = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._lte(value, params));
    inst.max = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._lte(value, params));
    inst.positive = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._gt(BigInt(0), params));
    inst.negative = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._lt(BigInt(0), params));
    inst.nonpositive = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._lte(BigInt(0), params));
    inst.nonnegative = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._gte(BigInt(0), params));
    inst.multipleOf = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._multipleOf(value, params));
    const bag = inst._zod.bag;
    inst.minValue = bag.minimum ?? null;
    inst.maxValue = bag.maximum ?? null;
    inst.format = bag.format ?? null;
});
function bigint(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._bigint(ZodBigInt, params);
}
const ZodBigIntFormat = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodBigIntFormat", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodBigIntFormat.init(inst, def);
    ZodBigInt.init(inst, def);
});
// int64
function int64(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._int64(ZodBigIntFormat, params);
}
// uint64
function uint64(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._uint64(ZodBigIntFormat, params);
}
const ZodSymbol = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodSymbol", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodSymbol.init(inst, def);
    ZodType.init(inst, def);
});
function symbol(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._symbol(ZodSymbol, params);
}
const ZodUndefined = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodUndefined", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodUndefined.init(inst, def);
    ZodType.init(inst, def);
});
function _undefined(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._undefined(ZodUndefined, params);
}

const ZodNull = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodNull", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodNull.init(inst, def);
    ZodType.init(inst, def);
});
function _null(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._null(ZodNull, params);
}

const ZodAny = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodAny", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodAny.init(inst, def);
    ZodType.init(inst, def);
});
function any() {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._any(ZodAny);
}
const ZodUnknown = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodUnknown", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodUnknown.init(inst, def);
    ZodType.init(inst, def);
});
function unknown() {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._unknown(ZodUnknown);
}
const ZodNever = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodNever", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodNever.init(inst, def);
    ZodType.init(inst, def);
});
function never(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._never(ZodNever, params);
}
const ZodVoid = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodVoid", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodVoid.init(inst, def);
    ZodType.init(inst, def);
});
function _void(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._void(ZodVoid, params);
}

const ZodDate = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodDate", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodDate.init(inst, def);
    ZodType.init(inst, def);
    inst.min = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._gte(value, params));
    inst.max = (value, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._lte(value, params));
    const c = inst._zod.bag;
    inst.minDate = c.minimum ? new Date(c.minimum) : null;
    inst.maxDate = c.maximum ? new Date(c.maximum) : null;
});
function date(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._date(ZodDate, params);
}
const ZodArray = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodArray", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodArray.init(inst, def);
    ZodType.init(inst, def);
    inst.element = def.element;
    inst.min = (minLength, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._minLength(minLength, params));
    inst.nonempty = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._minLength(1, params));
    inst.max = (maxLength, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._maxLength(maxLength, params));
    inst.length = (len, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._length(len, params));
    inst.unwrap = () => inst.element;
});
function array(element, params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._array(ZodArray, element, params);
}
// .keyof
function keyof(schema) {
    const shape = schema._zod.def.shape;
    return literal(Object.keys(shape));
}
const ZodObject = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodObject", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodObject.init(inst, def);
    ZodType.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_3__.defineLazy(inst, "shape", () => def.shape);
    inst.keyof = () => _enum(Object.keys(inst._zod.def.shape));
    inst.catchall = (catchall) => inst.clone({ ...inst._zod.def, catchall: catchall });
    inst.passthrough = () => inst.clone({ ...inst._zod.def, catchall: unknown() });
    // inst.nonstrict = () => inst.clone({ ...inst._zod.def, catchall: api.unknown() });
    inst.loose = () => inst.clone({ ...inst._zod.def, catchall: unknown() });
    inst.strict = () => inst.clone({ ...inst._zod.def, catchall: never() });
    inst.strip = () => inst.clone({ ...inst._zod.def, catchall: undefined });
    inst.extend = (incoming) => {
        return _core_index_js__WEBPACK_IMPORTED_MODULE_3__.extend(inst, incoming);
    };
    inst.merge = (other) => _core_index_js__WEBPACK_IMPORTED_MODULE_3__.merge(inst, other);
    inst.pick = (mask) => _core_index_js__WEBPACK_IMPORTED_MODULE_3__.pick(inst, mask);
    inst.omit = (mask) => _core_index_js__WEBPACK_IMPORTED_MODULE_3__.omit(inst, mask);
    inst.partial = (...args) => _core_index_js__WEBPACK_IMPORTED_MODULE_3__.partial(ZodOptional, inst, args[0]);
    inst.required = (...args) => _core_index_js__WEBPACK_IMPORTED_MODULE_3__.required(ZodNonOptional, inst, args[0]);
});
function object(shape, params) {
    const def = {
        type: "object",
        get shape() {
            _core_index_js__WEBPACK_IMPORTED_MODULE_3__.assignProp(this, "shape", { ...shape });
            return this.shape;
        },
        ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
    };
    return new ZodObject(def);
}
// strictObject
function strictObject(shape, params) {
    return new ZodObject({
        type: "object",
        get shape() {
            _core_index_js__WEBPACK_IMPORTED_MODULE_3__.assignProp(this, "shape", { ...shape });
            return this.shape;
        },
        catchall: never(),
        ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
    });
}
// looseObject
function looseObject(shape, params) {
    return new ZodObject({
        type: "object",
        get shape() {
            _core_index_js__WEBPACK_IMPORTED_MODULE_3__.assignProp(this, "shape", { ...shape });
            return this.shape;
        },
        catchall: unknown(),
        ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
    });
}
const ZodUnion = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodUnion", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodUnion.init(inst, def);
    ZodType.init(inst, def);
    inst.options = def.options;
});
function union(options, params) {
    return new ZodUnion({
        type: "union",
        options: options,
        ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
    });
}
const ZodDiscriminatedUnion = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodDiscriminatedUnion", (inst, def) => {
    ZodUnion.init(inst, def);
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodDiscriminatedUnion.init(inst, def);
});
function discriminatedUnion(discriminator, options, params) {
    // const [options, params] = args;
    return new ZodDiscriminatedUnion({
        type: "union",
        options,
        discriminator,
        ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
    });
}
const ZodIntersection = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodIntersection", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodIntersection.init(inst, def);
    ZodType.init(inst, def);
});
function intersection(left, right) {
    return new ZodIntersection({
        type: "intersection",
        left: left,
        right: right,
    });
}
const ZodTuple = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodTuple", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodTuple.init(inst, def);
    ZodType.init(inst, def);
    inst.rest = (rest) => inst.clone({
        ...inst._zod.def,
        rest: rest,
    });
});
function tuple(items, _paramsOrRest, _params) {
    const hasRest = _paramsOrRest instanceof _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodType;
    const params = hasRest ? _params : _paramsOrRest;
    const rest = hasRest ? _paramsOrRest : null;
    return new ZodTuple({
        type: "tuple",
        items: items,
        rest,
        ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
    });
}
const ZodRecord = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodRecord", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodRecord.init(inst, def);
    ZodType.init(inst, def);
    inst.keyType = def.keyType;
    inst.valueType = def.valueType;
});
function record(keyType, valueType, params) {
    return new ZodRecord({
        type: "record",
        keyType,
        valueType: valueType,
        ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
    });
}
// type alksjf = core.output<core.$ZodRecordKey>;
function partialRecord(keyType, valueType, params) {
    return new ZodRecord({
        type: "record",
        keyType: union([keyType, never()]),
        valueType: valueType,
        ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
    });
}
const ZodMap = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodMap", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodMap.init(inst, def);
    ZodType.init(inst, def);
    inst.keyType = def.keyType;
    inst.valueType = def.valueType;
});
function map(keyType, valueType, params) {
    return new ZodMap({
        type: "map",
        keyType: keyType,
        valueType: valueType,
        ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
    });
}
const ZodSet = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodSet", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodSet.init(inst, def);
    ZodType.init(inst, def);
    inst.min = (...args) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._minSize(...args));
    inst.nonempty = (params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._minSize(1, params));
    inst.max = (...args) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._maxSize(...args));
    inst.size = (...args) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._size(...args));
});
function set(valueType, params) {
    return new ZodSet({
        type: "set",
        valueType: valueType,
        ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
    });
}
const ZodEnum = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodEnum", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodEnum.init(inst, def);
    ZodType.init(inst, def);
    inst.enum = def.entries;
    inst.options = Object.values(def.entries);
    const keys = new Set(Object.keys(def.entries));
    inst.extract = (values, params) => {
        const newEntries = {};
        for (const value of values) {
            if (keys.has(value)) {
                newEntries[value] = def.entries[value];
            }
            else
                throw new Error(`Key ${value} not found in enum`);
        }
        return new ZodEnum({
            ...def,
            checks: [],
            ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
            entries: newEntries,
        });
    };
    inst.exclude = (values, params) => {
        const newEntries = { ...def.entries };
        for (const value of values) {
            if (keys.has(value)) {
                delete newEntries[value];
            }
            else
                throw new Error(`Key ${value} not found in enum`);
        }
        return new ZodEnum({
            ...def,
            checks: [],
            ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
            entries: newEntries,
        });
    };
});
function _enum(values, params) {
    const entries = Array.isArray(values) ? Object.fromEntries(values.map((v) => [v, v])) : values;
    return new ZodEnum({
        type: "enum",
        entries,
        ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
    });
}

/** @deprecated This API has been merged into `z.enum()`. Use `z.enum()` instead.
 *
 * ```ts
 * enum Colors { red, green, blue }
 * z.enum(Colors);
 * ```
 */
function nativeEnum(entries, params) {
    return new ZodEnum({
        type: "enum",
        entries,
        ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
    });
}
const ZodLiteral = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodLiteral", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodLiteral.init(inst, def);
    ZodType.init(inst, def);
    inst.values = new Set(def.values);
    Object.defineProperty(inst, "value", {
        get() {
            if (def.values.length > 1) {
                throw new Error("This schema contains multiple valid literal values. Use `.values` instead.");
            }
            return def.values[0];
        },
    });
});
function literal(value, params) {
    return new ZodLiteral({
        type: "literal",
        values: Array.isArray(value) ? value : [value],
        ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
    });
}
const ZodFile = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodFile", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodFile.init(inst, def);
    ZodType.init(inst, def);
    inst.min = (size, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._minSize(size, params));
    inst.max = (size, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._maxSize(size, params));
    inst.mime = (types, params) => inst.check(_core_index_js__WEBPACK_IMPORTED_MODULE_5__._mime(Array.isArray(types) ? types : [types], params));
});
function file(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._file(ZodFile, params);
}
const ZodTransform = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodTransform", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodTransform.init(inst, def);
    ZodType.init(inst, def);
    inst._zod.parse = (payload, _ctx) => {
        payload.addIssue = (issue) => {
            if (typeof issue === "string") {
                payload.issues.push(_core_index_js__WEBPACK_IMPORTED_MODULE_3__.issue(issue, payload.value, def));
            }
            else {
                // for Zod 3 backwards compatibility
                const _issue = issue;
                if (_issue.fatal)
                    _issue.continue = false;
                _issue.code ?? (_issue.code = "custom");
                _issue.input ?? (_issue.input = payload.value);
                _issue.inst ?? (_issue.inst = inst);
                _issue.continue ?? (_issue.continue = true);
                payload.issues.push(_core_index_js__WEBPACK_IMPORTED_MODULE_3__.issue(_issue));
            }
        };
        const output = def.transform(payload.value, payload);
        if (output instanceof Promise) {
            return output.then((output) => {
                payload.value = output;
                return payload;
            });
        }
        payload.value = output;
        return payload;
    };
});
function transform(fn) {
    return new ZodTransform({
        type: "transform",
        transform: fn,
    });
}
const ZodOptional = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodOptional", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodOptional.init(inst, def);
    ZodType.init(inst, def);
    inst.unwrap = () => inst._zod.def.innerType;
});
function optional(innerType) {
    return new ZodOptional({
        type: "optional",
        innerType: innerType,
    });
}
const ZodNullable = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodNullable", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodNullable.init(inst, def);
    ZodType.init(inst, def);
    inst.unwrap = () => inst._zod.def.innerType;
});
function nullable(innerType) {
    return new ZodNullable({
        type: "nullable",
        innerType: innerType,
    });
}
// nullish
function nullish(innerType) {
    return optional(nullable(innerType));
}
const ZodDefault = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodDefault", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodDefault.init(inst, def);
    ZodType.init(inst, def);
    inst.unwrap = () => inst._zod.def.innerType;
    inst.removeDefault = inst.unwrap;
});
function _default(innerType, defaultValue) {
    return new ZodDefault({
        type: "default",
        innerType: innerType,
        get defaultValue() {
            return typeof defaultValue === "function" ? defaultValue() : defaultValue;
        },
    });
}
const ZodPrefault = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodPrefault", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodPrefault.init(inst, def);
    ZodType.init(inst, def);
    inst.unwrap = () => inst._zod.def.innerType;
});
function prefault(innerType, defaultValue) {
    return new ZodPrefault({
        type: "prefault",
        innerType: innerType,
        get defaultValue() {
            return typeof defaultValue === "function" ? defaultValue() : defaultValue;
        },
    });
}
const ZodNonOptional = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodNonOptional", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodNonOptional.init(inst, def);
    ZodType.init(inst, def);
    inst.unwrap = () => inst._zod.def.innerType;
});
function nonoptional(innerType, params) {
    return new ZodNonOptional({
        type: "nonoptional",
        innerType: innerType,
        ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
    });
}
const ZodSuccess = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodSuccess", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodSuccess.init(inst, def);
    ZodType.init(inst, def);
    inst.unwrap = () => inst._zod.def.innerType;
});
function success(innerType) {
    return new ZodSuccess({
        type: "success",
        innerType: innerType,
    });
}
const ZodCatch = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodCatch", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodCatch.init(inst, def);
    ZodType.init(inst, def);
    inst.unwrap = () => inst._zod.def.innerType;
    inst.removeCatch = inst.unwrap;
});
function _catch(innerType, catchValue) {
    return new ZodCatch({
        type: "catch",
        innerType: innerType,
        catchValue: (typeof catchValue === "function" ? catchValue : () => catchValue),
    });
}

const ZodNaN = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodNaN", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodNaN.init(inst, def);
    ZodType.init(inst, def);
});
function nan(params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._nan(ZodNaN, params);
}
const ZodPipe = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodPipe", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodPipe.init(inst, def);
    ZodType.init(inst, def);
    inst.in = def.in;
    inst.out = def.out;
});
function pipe(in_, out) {
    return new ZodPipe({
        type: "pipe",
        in: in_,
        out: out,
        // ...util.normalizeParams(params),
    });
}
const ZodReadonly = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodReadonly", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodReadonly.init(inst, def);
    ZodType.init(inst, def);
});
function readonly(innerType) {
    return new ZodReadonly({
        type: "readonly",
        innerType: innerType,
    });
}
const ZodTemplateLiteral = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodTemplateLiteral", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodTemplateLiteral.init(inst, def);
    ZodType.init(inst, def);
});
function templateLiteral(parts, params) {
    return new ZodTemplateLiteral({
        type: "template_literal",
        parts,
        ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
    });
}
const ZodLazy = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodLazy", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodLazy.init(inst, def);
    ZodType.init(inst, def);
    inst.unwrap = () => inst._zod.def.getter();
});
function lazy(getter) {
    return new ZodLazy({
        type: "lazy",
        getter: getter,
    });
}
const ZodPromise = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodPromise", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodPromise.init(inst, def);
    ZodType.init(inst, def);
    inst.unwrap = () => inst._zod.def.innerType;
});
function promise(innerType) {
    return new ZodPromise({
        type: "promise",
        innerType: innerType,
    });
}
const ZodCustom = /*@__PURE__*/ _core_index_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("ZodCustom", (inst, def) => {
    _core_index_js__WEBPACK_IMPORTED_MODULE_1__.$ZodCustom.init(inst, def);
    ZodType.init(inst, def);
});
// custom checks
function check(fn) {
    const ch = new _core_index_js__WEBPACK_IMPORTED_MODULE_2__.$ZodCheck({
        check: "custom",
        // ...util.normalizeParams(params),
    });
    ch._zod.check = fn;
    return ch;
}
function custom(fn, _params) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._custom(ZodCustom, fn ?? (() => true), _params);
}
function refine(fn, _params = {}) {
    return _core_index_js__WEBPACK_IMPORTED_MODULE_5__._refine(ZodCustom, fn, _params);
}
// superRefine
function superRefine(fn) {
    const ch = check((payload) => {
        payload.addIssue = (issue) => {
            if (typeof issue === "string") {
                payload.issues.push(_core_index_js__WEBPACK_IMPORTED_MODULE_3__.issue(issue, payload.value, ch._zod.def));
            }
            else {
                // for Zod 3 backwards compatibility
                const _issue = issue;
                if (_issue.fatal)
                    _issue.continue = false;
                _issue.code ?? (_issue.code = "custom");
                _issue.input ?? (_issue.input = payload.value);
                _issue.inst ?? (_issue.inst = ch);
                _issue.continue ?? (_issue.continue = !ch._zod.def.abort);
                payload.issues.push(_core_index_js__WEBPACK_IMPORTED_MODULE_3__.issue(_issue));
            }
        };
        return fn(payload.value, payload);
    });
    return ch;
}
function _instanceof(cls, params = {
    error: `Input not instance of ${cls.name}`,
}) {
    const inst = new ZodCustom({
        type: "custom",
        check: "custom",
        fn: (data) => data instanceof cls,
        abort: true,
        ..._core_index_js__WEBPACK_IMPORTED_MODULE_3__.normalizeParams(params),
    });
    inst._zod.bag.Class = cls;
    return inst;
}

// stringbool
const stringbool = (...args) => _core_index_js__WEBPACK_IMPORTED_MODULE_5__._stringbool({
    Pipe: ZodPipe,
    Boolean: ZodBoolean,
    String: ZodString,
    Transform: ZodTransform,
}, ...args);
function json(params) {
    const jsonSchema = lazy(() => {
        return union([string(params), number(), boolean(), _null(), array(jsonSchema), record(string(), jsonSchema)]);
    });
    return jsonSchema;
}
// preprocess
// /** @deprecated Use `z.pipe()` and `z.transform()` instead. */
function preprocess(fn, schema) {
    return pipe(transform(fn), schema);
}


/***/ }),

/***/ "./node_modules/zod/v4/core/api.js":
/*!*****************************************!*\
  !*** ./node_modules/zod/v4/core/api.js ***!
  \*****************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TimePrecision: function() { return /* binding */ TimePrecision; },
/* harmony export */   _any: function() { return /* binding */ _any; },
/* harmony export */   _array: function() { return /* binding */ _array; },
/* harmony export */   _base64: function() { return /* binding */ _base64; },
/* harmony export */   _base64url: function() { return /* binding */ _base64url; },
/* harmony export */   _bigint: function() { return /* binding */ _bigint; },
/* harmony export */   _boolean: function() { return /* binding */ _boolean; },
/* harmony export */   _catch: function() { return /* binding */ _catch; },
/* harmony export */   _cidrv4: function() { return /* binding */ _cidrv4; },
/* harmony export */   _cidrv6: function() { return /* binding */ _cidrv6; },
/* harmony export */   _coercedBigint: function() { return /* binding */ _coercedBigint; },
/* harmony export */   _coercedBoolean: function() { return /* binding */ _coercedBoolean; },
/* harmony export */   _coercedDate: function() { return /* binding */ _coercedDate; },
/* harmony export */   _coercedNumber: function() { return /* binding */ _coercedNumber; },
/* harmony export */   _coercedString: function() { return /* binding */ _coercedString; },
/* harmony export */   _cuid: function() { return /* binding */ _cuid; },
/* harmony export */   _cuid2: function() { return /* binding */ _cuid2; },
/* harmony export */   _custom: function() { return /* binding */ _custom; },
/* harmony export */   _date: function() { return /* binding */ _date; },
/* harmony export */   _default: function() { return /* binding */ _default; },
/* harmony export */   _discriminatedUnion: function() { return /* binding */ _discriminatedUnion; },
/* harmony export */   _e164: function() { return /* binding */ _e164; },
/* harmony export */   _email: function() { return /* binding */ _email; },
/* harmony export */   _emoji: function() { return /* binding */ _emoji; },
/* harmony export */   _endsWith: function() { return /* binding */ _endsWith; },
/* harmony export */   _enum: function() { return /* binding */ _enum; },
/* harmony export */   _file: function() { return /* binding */ _file; },
/* harmony export */   _float32: function() { return /* binding */ _float32; },
/* harmony export */   _float64: function() { return /* binding */ _float64; },
/* harmony export */   _gt: function() { return /* binding */ _gt; },
/* harmony export */   _gte: function() { return /* binding */ _gte; },
/* harmony export */   _guid: function() { return /* binding */ _guid; },
/* harmony export */   _includes: function() { return /* binding */ _includes; },
/* harmony export */   _int: function() { return /* binding */ _int; },
/* harmony export */   _int32: function() { return /* binding */ _int32; },
/* harmony export */   _int64: function() { return /* binding */ _int64; },
/* harmony export */   _intersection: function() { return /* binding */ _intersection; },
/* harmony export */   _ipv4: function() { return /* binding */ _ipv4; },
/* harmony export */   _ipv6: function() { return /* binding */ _ipv6; },
/* harmony export */   _isoDate: function() { return /* binding */ _isoDate; },
/* harmony export */   _isoDateTime: function() { return /* binding */ _isoDateTime; },
/* harmony export */   _isoDuration: function() { return /* binding */ _isoDuration; },
/* harmony export */   _isoTime: function() { return /* binding */ _isoTime; },
/* harmony export */   _jwt: function() { return /* binding */ _jwt; },
/* harmony export */   _ksuid: function() { return /* binding */ _ksuid; },
/* harmony export */   _lazy: function() { return /* binding */ _lazy; },
/* harmony export */   _length: function() { return /* binding */ _length; },
/* harmony export */   _literal: function() { return /* binding */ _literal; },
/* harmony export */   _lowercase: function() { return /* binding */ _lowercase; },
/* harmony export */   _lt: function() { return /* binding */ _lt; },
/* harmony export */   _lte: function() { return /* binding */ _lte; },
/* harmony export */   _map: function() { return /* binding */ _map; },
/* harmony export */   _max: function() { return /* binding */ _lte; },
/* harmony export */   _maxLength: function() { return /* binding */ _maxLength; },
/* harmony export */   _maxSize: function() { return /* binding */ _maxSize; },
/* harmony export */   _mime: function() { return /* binding */ _mime; },
/* harmony export */   _min: function() { return /* binding */ _gte; },
/* harmony export */   _minLength: function() { return /* binding */ _minLength; },
/* harmony export */   _minSize: function() { return /* binding */ _minSize; },
/* harmony export */   _multipleOf: function() { return /* binding */ _multipleOf; },
/* harmony export */   _nan: function() { return /* binding */ _nan; },
/* harmony export */   _nanoid: function() { return /* binding */ _nanoid; },
/* harmony export */   _nativeEnum: function() { return /* binding */ _nativeEnum; },
/* harmony export */   _negative: function() { return /* binding */ _negative; },
/* harmony export */   _never: function() { return /* binding */ _never; },
/* harmony export */   _nonnegative: function() { return /* binding */ _nonnegative; },
/* harmony export */   _nonoptional: function() { return /* binding */ _nonoptional; },
/* harmony export */   _nonpositive: function() { return /* binding */ _nonpositive; },
/* harmony export */   _normalize: function() { return /* binding */ _normalize; },
/* harmony export */   _null: function() { return /* binding */ _null; },
/* harmony export */   _nullable: function() { return /* binding */ _nullable; },
/* harmony export */   _number: function() { return /* binding */ _number; },
/* harmony export */   _optional: function() { return /* binding */ _optional; },
/* harmony export */   _overwrite: function() { return /* binding */ _overwrite; },
/* harmony export */   _pipe: function() { return /* binding */ _pipe; },
/* harmony export */   _positive: function() { return /* binding */ _positive; },
/* harmony export */   _promise: function() { return /* binding */ _promise; },
/* harmony export */   _property: function() { return /* binding */ _property; },
/* harmony export */   _readonly: function() { return /* binding */ _readonly; },
/* harmony export */   _record: function() { return /* binding */ _record; },
/* harmony export */   _refine: function() { return /* binding */ _refine; },
/* harmony export */   _regex: function() { return /* binding */ _regex; },
/* harmony export */   _set: function() { return /* binding */ _set; },
/* harmony export */   _size: function() { return /* binding */ _size; },
/* harmony export */   _startsWith: function() { return /* binding */ _startsWith; },
/* harmony export */   _string: function() { return /* binding */ _string; },
/* harmony export */   _stringFormat: function() { return /* binding */ _stringFormat; },
/* harmony export */   _stringbool: function() { return /* binding */ _stringbool; },
/* harmony export */   _success: function() { return /* binding */ _success; },
/* harmony export */   _symbol: function() { return /* binding */ _symbol; },
/* harmony export */   _templateLiteral: function() { return /* binding */ _templateLiteral; },
/* harmony export */   _toLowerCase: function() { return /* binding */ _toLowerCase; },
/* harmony export */   _toUpperCase: function() { return /* binding */ _toUpperCase; },
/* harmony export */   _transform: function() { return /* binding */ _transform; },
/* harmony export */   _trim: function() { return /* binding */ _trim; },
/* harmony export */   _tuple: function() { return /* binding */ _tuple; },
/* harmony export */   _uint32: function() { return /* binding */ _uint32; },
/* harmony export */   _uint64: function() { return /* binding */ _uint64; },
/* harmony export */   _ulid: function() { return /* binding */ _ulid; },
/* harmony export */   _undefined: function() { return /* binding */ _undefined; },
/* harmony export */   _union: function() { return /* binding */ _union; },
/* harmony export */   _unknown: function() { return /* binding */ _unknown; },
/* harmony export */   _uppercase: function() { return /* binding */ _uppercase; },
/* harmony export */   _url: function() { return /* binding */ _url; },
/* harmony export */   _uuid: function() { return /* binding */ _uuid; },
/* harmony export */   _uuidv4: function() { return /* binding */ _uuidv4; },
/* harmony export */   _uuidv6: function() { return /* binding */ _uuidv6; },
/* harmony export */   _uuidv7: function() { return /* binding */ _uuidv7; },
/* harmony export */   _void: function() { return /* binding */ _void; },
/* harmony export */   _xid: function() { return /* binding */ _xid; }
/* harmony export */ });
/* harmony import */ var _checks_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./checks.js */ "./node_modules/zod/v4/core/checks.js");
/* harmony import */ var _schemas_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./schemas.js */ "./node_modules/zod/v4/core/schemas.js");
/* harmony import */ var _util_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./util.js */ "./node_modules/zod/v4/core/util.js");



function _string(Class, params) {
    return new Class({
        type: "string",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _coercedString(Class, params) {
    return new Class({
        type: "string",
        coerce: true,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _email(Class, params) {
    return new Class({
        type: "string",
        format: "email",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _guid(Class, params) {
    return new Class({
        type: "string",
        format: "guid",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _uuid(Class, params) {
    return new Class({
        type: "string",
        format: "uuid",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _uuidv4(Class, params) {
    return new Class({
        type: "string",
        format: "uuid",
        check: "string_format",
        abort: false,
        version: "v4",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _uuidv6(Class, params) {
    return new Class({
        type: "string",
        format: "uuid",
        check: "string_format",
        abort: false,
        version: "v6",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _uuidv7(Class, params) {
    return new Class({
        type: "string",
        format: "uuid",
        check: "string_format",
        abort: false,
        version: "v7",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _url(Class, params) {
    return new Class({
        type: "string",
        format: "url",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _emoji(Class, params) {
    return new Class({
        type: "string",
        format: "emoji",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _nanoid(Class, params) {
    return new Class({
        type: "string",
        format: "nanoid",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _cuid(Class, params) {
    return new Class({
        type: "string",
        format: "cuid",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _cuid2(Class, params) {
    return new Class({
        type: "string",
        format: "cuid2",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _ulid(Class, params) {
    return new Class({
        type: "string",
        format: "ulid",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _xid(Class, params) {
    return new Class({
        type: "string",
        format: "xid",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _ksuid(Class, params) {
    return new Class({
        type: "string",
        format: "ksuid",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _ipv4(Class, params) {
    return new Class({
        type: "string",
        format: "ipv4",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _ipv6(Class, params) {
    return new Class({
        type: "string",
        format: "ipv6",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _cidrv4(Class, params) {
    return new Class({
        type: "string",
        format: "cidrv4",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _cidrv6(Class, params) {
    return new Class({
        type: "string",
        format: "cidrv6",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _base64(Class, params) {
    return new Class({
        type: "string",
        format: "base64",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _base64url(Class, params) {
    return new Class({
        type: "string",
        format: "base64url",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _e164(Class, params) {
    return new Class({
        type: "string",
        format: "e164",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _jwt(Class, params) {
    return new Class({
        type: "string",
        format: "jwt",
        check: "string_format",
        abort: false,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
const TimePrecision = {
    Any: null,
    Minute: -1,
    Second: 0,
    Millisecond: 3,
    Microsecond: 6,
};
function _isoDateTime(Class, params) {
    return new Class({
        type: "string",
        format: "datetime",
        check: "string_format",
        offset: false,
        local: false,
        precision: null,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _isoDate(Class, params) {
    return new Class({
        type: "string",
        format: "date",
        check: "string_format",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _isoTime(Class, params) {
    return new Class({
        type: "string",
        format: "time",
        check: "string_format",
        precision: null,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _isoDuration(Class, params) {
    return new Class({
        type: "string",
        format: "duration",
        check: "string_format",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _number(Class, params) {
    return new Class({
        type: "number",
        checks: [],
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _coercedNumber(Class, params) {
    return new Class({
        type: "number",
        coerce: true,
        checks: [],
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _int(Class, params) {
    return new Class({
        type: "number",
        check: "number_format",
        abort: false,
        format: "safeint",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _float32(Class, params) {
    return new Class({
        type: "number",
        check: "number_format",
        abort: false,
        format: "float32",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _float64(Class, params) {
    return new Class({
        type: "number",
        check: "number_format",
        abort: false,
        format: "float64",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _int32(Class, params) {
    return new Class({
        type: "number",
        check: "number_format",
        abort: false,
        format: "int32",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _uint32(Class, params) {
    return new Class({
        type: "number",
        check: "number_format",
        abort: false,
        format: "uint32",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _boolean(Class, params) {
    return new Class({
        type: "boolean",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _coercedBoolean(Class, params) {
    return new Class({
        type: "boolean",
        coerce: true,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _bigint(Class, params) {
    return new Class({
        type: "bigint",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _coercedBigint(Class, params) {
    return new Class({
        type: "bigint",
        coerce: true,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _int64(Class, params) {
    return new Class({
        type: "bigint",
        check: "bigint_format",
        abort: false,
        format: "int64",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _uint64(Class, params) {
    return new Class({
        type: "bigint",
        check: "bigint_format",
        abort: false,
        format: "uint64",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _symbol(Class, params) {
    return new Class({
        type: "symbol",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _undefined(Class, params) {
    return new Class({
        type: "undefined",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _null(Class, params) {
    return new Class({
        type: "null",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _any(Class) {
    return new Class({
        type: "any",
    });
}
function _unknown(Class) {
    return new Class({
        type: "unknown",
    });
}
function _never(Class, params) {
    return new Class({
        type: "never",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _void(Class, params) {
    return new Class({
        type: "void",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _date(Class, params) {
    return new Class({
        type: "date",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _coercedDate(Class, params) {
    return new Class({
        type: "date",
        coerce: true,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _nan(Class, params) {
    return new Class({
        type: "nan",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _lt(value, params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckLessThan({
        check: "less_than",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
        value,
        inclusive: false,
    });
}
function _lte(value, params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckLessThan({
        check: "less_than",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
        value,
        inclusive: true,
    });
}

function _gt(value, params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckGreaterThan({
        check: "greater_than",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
        value,
        inclusive: false,
    });
}
function _gte(value, params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckGreaterThan({
        check: "greater_than",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
        value,
        inclusive: true,
    });
}

function _positive(params) {
    return _gt(0, params);
}
// negative
function _negative(params) {
    return _lt(0, params);
}
// nonpositive
function _nonpositive(params) {
    return _lte(0, params);
}
// nonnegative
function _nonnegative(params) {
    return _gte(0, params);
}
function _multipleOf(value, params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckMultipleOf({
        check: "multiple_of",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
        value,
    });
}
function _maxSize(maximum, params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckMaxSize({
        check: "max_size",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
        maximum,
    });
}
function _minSize(minimum, params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckMinSize({
        check: "min_size",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
        minimum,
    });
}
function _size(size, params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckSizeEquals({
        check: "size_equals",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
        size,
    });
}
function _maxLength(maximum, params) {
    const ch = new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckMaxLength({
        check: "max_length",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
        maximum,
    });
    return ch;
}
function _minLength(minimum, params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckMinLength({
        check: "min_length",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
        minimum,
    });
}
function _length(length, params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckLengthEquals({
        check: "length_equals",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
        length,
    });
}
function _regex(pattern, params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckRegex({
        check: "string_format",
        format: "regex",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
        pattern,
    });
}
function _lowercase(params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckLowerCase({
        check: "string_format",
        format: "lowercase",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _uppercase(params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckUpperCase({
        check: "string_format",
        format: "uppercase",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _includes(includes, params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckIncludes({
        check: "string_format",
        format: "includes",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
        includes,
    });
}
function _startsWith(prefix, params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckStartsWith({
        check: "string_format",
        format: "starts_with",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
        prefix,
    });
}
function _endsWith(suffix, params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckEndsWith({
        check: "string_format",
        format: "ends_with",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
        suffix,
    });
}
function _property(property, schema, params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckProperty({
        check: "property",
        property,
        schema,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _mime(types, params) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckMimeType({
        check: "mime_type",
        mime: types,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _overwrite(tx) {
    return new _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckOverwrite({
        check: "overwrite",
        tx,
    });
}
// normalize
function _normalize(form) {
    return _overwrite((input) => input.normalize(form));
}
// trim
function _trim() {
    return _overwrite((input) => input.trim());
}
// toLowerCase
function _toLowerCase() {
    return _overwrite((input) => input.toLowerCase());
}
// toUpperCase
function _toUpperCase() {
    return _overwrite((input) => input.toUpperCase());
}
function _array(Class, element, params) {
    return new Class({
        type: "array",
        element,
        // get element() {
        //   return element;
        // },
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _union(Class, options, params) {
    return new Class({
        type: "union",
        options,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _discriminatedUnion(Class, discriminator, options, params) {
    return new Class({
        type: "union",
        options,
        discriminator,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _intersection(Class, left, right) {
    return new Class({
        type: "intersection",
        left,
        right,
    });
}
// export function _tuple(
//   Class: util.SchemaClass<schemas.$ZodTuple>,
//   items: [],
//   params?: string | $ZodTupleParams
// ): schemas.$ZodTuple<[], null>;
function _tuple(Class, items, _paramsOrRest, _params) {
    const hasRest = _paramsOrRest instanceof _schemas_js__WEBPACK_IMPORTED_MODULE_1__.$ZodType;
    const params = hasRest ? _params : _paramsOrRest;
    const rest = hasRest ? _paramsOrRest : null;
    return new Class({
        type: "tuple",
        items,
        rest,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _record(Class, keyType, valueType, params) {
    return new Class({
        type: "record",
        keyType,
        valueType,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _map(Class, keyType, valueType, params) {
    return new Class({
        type: "map",
        keyType,
        valueType,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _set(Class, valueType, params) {
    return new Class({
        type: "set",
        valueType,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _enum(Class, values, params) {
    const entries = Array.isArray(values) ? Object.fromEntries(values.map((v) => [v, v])) : values;
    // if (Array.isArray(values)) {
    //   for (const value of values) {
    //     entries[value] = value;
    //   }
    // } else {
    //   Object.assign(entries, values);
    // }
    // const entries: util.EnumLike = {};
    // for (const val of values) {
    //   entries[val] = val;
    // }
    return new Class({
        type: "enum",
        entries,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
/** @deprecated This API has been merged into `z.enum()`. Use `z.enum()` instead.
 *
 * ```ts
 * enum Colors { red, green, blue }
 * z.enum(Colors);
 * ```
 */
function _nativeEnum(Class, entries, params) {
    return new Class({
        type: "enum",
        entries,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _literal(Class, value, params) {
    return new Class({
        type: "literal",
        values: Array.isArray(value) ? value : [value],
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _file(Class, params) {
    return new Class({
        type: "file",
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _transform(Class, fn) {
    return new Class({
        type: "transform",
        transform: fn,
    });
}
function _optional(Class, innerType) {
    return new Class({
        type: "optional",
        innerType,
    });
}
function _nullable(Class, innerType) {
    return new Class({
        type: "nullable",
        innerType,
    });
}
function _default(Class, innerType, defaultValue) {
    return new Class({
        type: "default",
        innerType,
        get defaultValue() {
            return typeof defaultValue === "function" ? defaultValue() : defaultValue;
        },
    });
}
function _nonoptional(Class, innerType, params) {
    return new Class({
        type: "nonoptional",
        innerType,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _success(Class, innerType) {
    return new Class({
        type: "success",
        innerType,
    });
}
function _catch(Class, innerType, catchValue) {
    return new Class({
        type: "catch",
        innerType,
        catchValue: (typeof catchValue === "function" ? catchValue : () => catchValue),
    });
}
function _pipe(Class, in_, out) {
    return new Class({
        type: "pipe",
        in: in_,
        out,
    });
}
function _readonly(Class, innerType) {
    return new Class({
        type: "readonly",
        innerType,
    });
}
function _templateLiteral(Class, parts, params) {
    return new Class({
        type: "template_literal",
        parts,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(params),
    });
}
function _lazy(Class, getter) {
    return new Class({
        type: "lazy",
        getter,
    });
}
function _promise(Class, innerType) {
    return new Class({
        type: "promise",
        innerType,
    });
}
function _custom(Class, fn, _params) {
    const norm = _util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(_params);
    norm.abort ?? (norm.abort = true); // default to abort:false
    const schema = new Class({
        type: "custom",
        check: "custom",
        fn: fn,
        ...norm,
    });
    return schema;
}
// export function _refine<T>(
//   Class: util.SchemaClass<schemas.$ZodCustom>,
//   fn: (arg: NoInfer<T>) => util.MaybeAsync<unknown>,
//   _params: string | $ZodCustomParams = {}
// ): checks.$ZodCheck<T> {
//   return _custom(Class, fn, _params);
// }
// same as _custom but defaults to abort:false
function _refine(Class, fn, _params) {
    const schema = new Class({
        type: "custom",
        check: "custom",
        fn: fn,
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(_params),
    });
    return schema;
}
function _stringbool(Classes, _params) {
    const params = _util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(_params);
    let truthyArray = params.truthy ?? ["true", "1", "yes", "on", "y", "enabled"];
    let falsyArray = params.falsy ?? ["false", "0", "no", "off", "n", "disabled"];
    if (params.case !== "sensitive") {
        truthyArray = truthyArray.map((v) => (typeof v === "string" ? v.toLowerCase() : v));
        falsyArray = falsyArray.map((v) => (typeof v === "string" ? v.toLowerCase() : v));
    }
    const truthySet = new Set(truthyArray);
    const falsySet = new Set(falsyArray);
    const _Pipe = Classes.Pipe ?? _schemas_js__WEBPACK_IMPORTED_MODULE_1__.$ZodPipe;
    const _Boolean = Classes.Boolean ?? _schemas_js__WEBPACK_IMPORTED_MODULE_1__.$ZodBoolean;
    const _String = Classes.String ?? _schemas_js__WEBPACK_IMPORTED_MODULE_1__.$ZodString;
    const _Transform = Classes.Transform ?? _schemas_js__WEBPACK_IMPORTED_MODULE_1__.$ZodTransform;
    const tx = new _Transform({
        type: "transform",
        transform: (input, payload) => {
            let data = input;
            if (params.case !== "sensitive")
                data = data.toLowerCase();
            if (truthySet.has(data)) {
                return true;
            }
            else if (falsySet.has(data)) {
                return false;
            }
            else {
                payload.issues.push({
                    code: "invalid_value",
                    expected: "stringbool",
                    values: [...truthySet, ...falsySet],
                    input: payload.value,
                    inst: tx,
                });
                return {};
            }
        },
        error: params.error,
    });
    // params.error;
    const innerPipe = new _Pipe({
        type: "pipe",
        in: new _String({ type: "string", error: params.error }),
        out: tx,
        error: params.error,
    });
    const outerPipe = new _Pipe({
        type: "pipe",
        in: innerPipe,
        out: new _Boolean({
            type: "boolean",
            error: params.error,
        }),
        error: params.error,
    });
    return outerPipe;
}
function _stringFormat(Class, format, fnOrRegex, _params = {}) {
    const params = _util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(_params);
    const def = {
        ..._util_js__WEBPACK_IMPORTED_MODULE_2__.normalizeParams(_params),
        check: "string_format",
        type: "string",
        format,
        fn: typeof fnOrRegex === "function" ? fnOrRegex : (val) => fnOrRegex.test(val),
        ...params,
    };
    if (fnOrRegex instanceof RegExp) {
        def.pattern = fnOrRegex;
    }
    const inst = new Class(def);
    return inst;
}


/***/ }),

/***/ "./node_modules/zod/v4/core/checks.js":
/*!********************************************!*\
  !*** ./node_modules/zod/v4/core/checks.js ***!
  \********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   $ZodCheck: function() { return /* binding */ $ZodCheck; },
/* harmony export */   $ZodCheckBigIntFormat: function() { return /* binding */ $ZodCheckBigIntFormat; },
/* harmony export */   $ZodCheckEndsWith: function() { return /* binding */ $ZodCheckEndsWith; },
/* harmony export */   $ZodCheckGreaterThan: function() { return /* binding */ $ZodCheckGreaterThan; },
/* harmony export */   $ZodCheckIncludes: function() { return /* binding */ $ZodCheckIncludes; },
/* harmony export */   $ZodCheckLengthEquals: function() { return /* binding */ $ZodCheckLengthEquals; },
/* harmony export */   $ZodCheckLessThan: function() { return /* binding */ $ZodCheckLessThan; },
/* harmony export */   $ZodCheckLowerCase: function() { return /* binding */ $ZodCheckLowerCase; },
/* harmony export */   $ZodCheckMaxLength: function() { return /* binding */ $ZodCheckMaxLength; },
/* harmony export */   $ZodCheckMaxSize: function() { return /* binding */ $ZodCheckMaxSize; },
/* harmony export */   $ZodCheckMimeType: function() { return /* binding */ $ZodCheckMimeType; },
/* harmony export */   $ZodCheckMinLength: function() { return /* binding */ $ZodCheckMinLength; },
/* harmony export */   $ZodCheckMinSize: function() { return /* binding */ $ZodCheckMinSize; },
/* harmony export */   $ZodCheckMultipleOf: function() { return /* binding */ $ZodCheckMultipleOf; },
/* harmony export */   $ZodCheckNumberFormat: function() { return /* binding */ $ZodCheckNumberFormat; },
/* harmony export */   $ZodCheckOverwrite: function() { return /* binding */ $ZodCheckOverwrite; },
/* harmony export */   $ZodCheckProperty: function() { return /* binding */ $ZodCheckProperty; },
/* harmony export */   $ZodCheckRegex: function() { return /* binding */ $ZodCheckRegex; },
/* harmony export */   $ZodCheckSizeEquals: function() { return /* binding */ $ZodCheckSizeEquals; },
/* harmony export */   $ZodCheckStartsWith: function() { return /* binding */ $ZodCheckStartsWith; },
/* harmony export */   $ZodCheckStringFormat: function() { return /* binding */ $ZodCheckStringFormat; },
/* harmony export */   $ZodCheckUpperCase: function() { return /* binding */ $ZodCheckUpperCase; }
/* harmony export */ });
/* harmony import */ var _core_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./core.js */ "./node_modules/zod/v4/core/core.js");
/* harmony import */ var _regexes_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./regexes.js */ "./node_modules/zod/v4/core/regexes.js");
/* harmony import */ var _util_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./util.js */ "./node_modules/zod/v4/core/util.js");
// import { $ZodType } from "./schemas.js";



const $ZodCheck = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheck", (inst, def) => {
    var _a;
    inst._zod ?? (inst._zod = {});
    inst._zod.def = def;
    (_a = inst._zod).onattach ?? (_a.onattach = []);
});
const numericOriginMap = {
    number: "number",
    bigint: "bigint",
    object: "date",
};
const $ZodCheckLessThan = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckLessThan", (inst, def) => {
    $ZodCheck.init(inst, def);
    const origin = numericOriginMap[typeof def.value];
    inst._zod.onattach.push((inst) => {
        const bag = inst._zod.bag;
        const curr = (def.inclusive ? bag.maximum : bag.exclusiveMaximum) ?? Number.POSITIVE_INFINITY;
        if (def.value < curr) {
            if (def.inclusive)
                bag.maximum = def.value;
            else
                bag.exclusiveMaximum = def.value;
        }
    });
    inst._zod.check = (payload) => {
        if (def.inclusive ? payload.value <= def.value : payload.value < def.value) {
            return;
        }
        payload.issues.push({
            origin,
            code: "too_big",
            maximum: def.value,
            input: payload.value,
            inclusive: def.inclusive,
            inst,
            continue: !def.abort,
        });
    };
});
const $ZodCheckGreaterThan = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckGreaterThan", (inst, def) => {
    $ZodCheck.init(inst, def);
    const origin = numericOriginMap[typeof def.value];
    inst._zod.onattach.push((inst) => {
        const bag = inst._zod.bag;
        const curr = (def.inclusive ? bag.minimum : bag.exclusiveMinimum) ?? Number.NEGATIVE_INFINITY;
        if (def.value > curr) {
            if (def.inclusive)
                bag.minimum = def.value;
            else
                bag.exclusiveMinimum = def.value;
        }
    });
    inst._zod.check = (payload) => {
        if (def.inclusive ? payload.value >= def.value : payload.value > def.value) {
            return;
        }
        payload.issues.push({
            origin,
            code: "too_small",
            minimum: def.value,
            input: payload.value,
            inclusive: def.inclusive,
            inst,
            continue: !def.abort,
        });
    };
});
const $ZodCheckMultipleOf = 
/*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckMultipleOf", (inst, def) => {
    $ZodCheck.init(inst, def);
    inst._zod.onattach.push((inst) => {
        var _a;
        (_a = inst._zod.bag).multipleOf ?? (_a.multipleOf = def.value);
    });
    inst._zod.check = (payload) => {
        if (typeof payload.value !== typeof def.value)
            throw new Error("Cannot mix number and bigint in multiple_of check.");
        const isMultiple = typeof payload.value === "bigint"
            ? payload.value % def.value === BigInt(0)
            : _util_js__WEBPACK_IMPORTED_MODULE_2__.floatSafeRemainder(payload.value, def.value) === 0;
        if (isMultiple)
            return;
        payload.issues.push({
            origin: typeof payload.value,
            code: "not_multiple_of",
            divisor: def.value,
            input: payload.value,
            inst,
            continue: !def.abort,
        });
    };
});
const $ZodCheckNumberFormat = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckNumberFormat", (inst, def) => {
    $ZodCheck.init(inst, def); // no format checks
    def.format = def.format || "float64";
    const isInt = def.format?.includes("int");
    const origin = isInt ? "int" : "number";
    const [minimum, maximum] = _util_js__WEBPACK_IMPORTED_MODULE_2__.NUMBER_FORMAT_RANGES[def.format];
    inst._zod.onattach.push((inst) => {
        const bag = inst._zod.bag;
        bag.format = def.format;
        bag.minimum = minimum;
        bag.maximum = maximum;
        if (isInt)
            bag.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_1__.integer;
    });
    inst._zod.check = (payload) => {
        const input = payload.value;
        if (isInt) {
            if (!Number.isInteger(input)) {
                // invalid_format issue
                // payload.issues.push({
                //   expected: def.format,
                //   format: def.format,
                //   code: "invalid_format",
                //   input,
                //   inst,
                // });
                // invalid_type issue
                payload.issues.push({
                    expected: origin,
                    format: def.format,
                    code: "invalid_type",
                    input,
                    inst,
                });
                return;
                // not_multiple_of issue
                // payload.issues.push({
                //   code: "not_multiple_of",
                //   origin: "number",
                //   input,
                //   inst,
                //   divisor: 1,
                // });
            }
            if (!Number.isSafeInteger(input)) {
                if (input > 0) {
                    // too_big
                    payload.issues.push({
                        input,
                        code: "too_big",
                        maximum: Number.MAX_SAFE_INTEGER,
                        note: "Integers must be within the safe integer range.",
                        inst,
                        origin,
                        continue: !def.abort,
                    });
                }
                else {
                    // too_small
                    payload.issues.push({
                        input,
                        code: "too_small",
                        minimum: Number.MIN_SAFE_INTEGER,
                        note: "Integers must be within the safe integer range.",
                        inst,
                        origin,
                        continue: !def.abort,
                    });
                }
                return;
            }
        }
        if (input < minimum) {
            payload.issues.push({
                origin: "number",
                input,
                code: "too_small",
                minimum,
                inclusive: true,
                inst,
                continue: !def.abort,
            });
        }
        if (input > maximum) {
            payload.issues.push({
                origin: "number",
                input,
                code: "too_big",
                maximum,
                inst,
            });
        }
    };
});
const $ZodCheckBigIntFormat = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckBigIntFormat", (inst, def) => {
    $ZodCheck.init(inst, def); // no format checks
    const [minimum, maximum] = _util_js__WEBPACK_IMPORTED_MODULE_2__.BIGINT_FORMAT_RANGES[def.format];
    inst._zod.onattach.push((inst) => {
        const bag = inst._zod.bag;
        bag.format = def.format;
        bag.minimum = minimum;
        bag.maximum = maximum;
    });
    inst._zod.check = (payload) => {
        const input = payload.value;
        if (input < minimum) {
            payload.issues.push({
                origin: "bigint",
                input,
                code: "too_small",
                minimum: minimum,
                inclusive: true,
                inst,
                continue: !def.abort,
            });
        }
        if (input > maximum) {
            payload.issues.push({
                origin: "bigint",
                input,
                code: "too_big",
                maximum,
                inst,
            });
        }
    };
});
const $ZodCheckMaxSize = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckMaxSize", (inst, def) => {
    var _a;
    $ZodCheck.init(inst, def);
    (_a = inst._zod.def).when ?? (_a.when = (payload) => {
        const val = payload.value;
        return !_util_js__WEBPACK_IMPORTED_MODULE_2__.nullish(val) && val.size !== undefined;
    });
    inst._zod.onattach.push((inst) => {
        const curr = (inst._zod.bag.maximum ?? Number.POSITIVE_INFINITY);
        if (def.maximum < curr)
            inst._zod.bag.maximum = def.maximum;
    });
    inst._zod.check = (payload) => {
        const input = payload.value;
        const size = input.size;
        if (size <= def.maximum)
            return;
        payload.issues.push({
            origin: _util_js__WEBPACK_IMPORTED_MODULE_2__.getSizableOrigin(input),
            code: "too_big",
            maximum: def.maximum,
            input,
            inst,
            continue: !def.abort,
        });
    };
});
const $ZodCheckMinSize = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckMinSize", (inst, def) => {
    var _a;
    $ZodCheck.init(inst, def);
    (_a = inst._zod.def).when ?? (_a.when = (payload) => {
        const val = payload.value;
        return !_util_js__WEBPACK_IMPORTED_MODULE_2__.nullish(val) && val.size !== undefined;
    });
    inst._zod.onattach.push((inst) => {
        const curr = (inst._zod.bag.minimum ?? Number.NEGATIVE_INFINITY);
        if (def.minimum > curr)
            inst._zod.bag.minimum = def.minimum;
    });
    inst._zod.check = (payload) => {
        const input = payload.value;
        const size = input.size;
        if (size >= def.minimum)
            return;
        payload.issues.push({
            origin: _util_js__WEBPACK_IMPORTED_MODULE_2__.getSizableOrigin(input),
            code: "too_small",
            minimum: def.minimum,
            input,
            inst,
            continue: !def.abort,
        });
    };
});
const $ZodCheckSizeEquals = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckSizeEquals", (inst, def) => {
    var _a;
    $ZodCheck.init(inst, def);
    (_a = inst._zod.def).when ?? (_a.when = (payload) => {
        const val = payload.value;
        return !_util_js__WEBPACK_IMPORTED_MODULE_2__.nullish(val) && val.size !== undefined;
    });
    inst._zod.onattach.push((inst) => {
        const bag = inst._zod.bag;
        bag.minimum = def.size;
        bag.maximum = def.size;
        bag.size = def.size;
    });
    inst._zod.check = (payload) => {
        const input = payload.value;
        const size = input.size;
        if (size === def.size)
            return;
        const tooBig = size > def.size;
        payload.issues.push({
            origin: _util_js__WEBPACK_IMPORTED_MODULE_2__.getSizableOrigin(input),
            ...(tooBig ? { code: "too_big", maximum: def.size } : { code: "too_small", minimum: def.size }),
            inclusive: true,
            exact: true,
            input: payload.value,
            inst,
            continue: !def.abort,
        });
    };
});
const $ZodCheckMaxLength = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckMaxLength", (inst, def) => {
    var _a;
    $ZodCheck.init(inst, def);
    (_a = inst._zod.def).when ?? (_a.when = (payload) => {
        const val = payload.value;
        return !_util_js__WEBPACK_IMPORTED_MODULE_2__.nullish(val) && val.length !== undefined;
    });
    inst._zod.onattach.push((inst) => {
        const curr = (inst._zod.bag.maximum ?? Number.POSITIVE_INFINITY);
        if (def.maximum < curr)
            inst._zod.bag.maximum = def.maximum;
    });
    inst._zod.check = (payload) => {
        const input = payload.value;
        const length = input.length;
        if (length <= def.maximum)
            return;
        const origin = _util_js__WEBPACK_IMPORTED_MODULE_2__.getLengthableOrigin(input);
        payload.issues.push({
            origin,
            code: "too_big",
            maximum: def.maximum,
            inclusive: true,
            input,
            inst,
            continue: !def.abort,
        });
    };
});
const $ZodCheckMinLength = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckMinLength", (inst, def) => {
    var _a;
    $ZodCheck.init(inst, def);
    (_a = inst._zod.def).when ?? (_a.when = (payload) => {
        const val = payload.value;
        return !_util_js__WEBPACK_IMPORTED_MODULE_2__.nullish(val) && val.length !== undefined;
    });
    inst._zod.onattach.push((inst) => {
        const curr = (inst._zod.bag.minimum ?? Number.NEGATIVE_INFINITY);
        if (def.minimum > curr)
            inst._zod.bag.minimum = def.minimum;
    });
    inst._zod.check = (payload) => {
        const input = payload.value;
        const length = input.length;
        if (length >= def.minimum)
            return;
        const origin = _util_js__WEBPACK_IMPORTED_MODULE_2__.getLengthableOrigin(input);
        payload.issues.push({
            origin,
            code: "too_small",
            minimum: def.minimum,
            inclusive: true,
            input,
            inst,
            continue: !def.abort,
        });
    };
});
const $ZodCheckLengthEquals = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckLengthEquals", (inst, def) => {
    var _a;
    $ZodCheck.init(inst, def);
    (_a = inst._zod.def).when ?? (_a.when = (payload) => {
        const val = payload.value;
        return !_util_js__WEBPACK_IMPORTED_MODULE_2__.nullish(val) && val.length !== undefined;
    });
    inst._zod.onattach.push((inst) => {
        const bag = inst._zod.bag;
        bag.minimum = def.length;
        bag.maximum = def.length;
        bag.length = def.length;
    });
    inst._zod.check = (payload) => {
        const input = payload.value;
        const length = input.length;
        if (length === def.length)
            return;
        const origin = _util_js__WEBPACK_IMPORTED_MODULE_2__.getLengthableOrigin(input);
        const tooBig = length > def.length;
        payload.issues.push({
            origin,
            ...(tooBig ? { code: "too_big", maximum: def.length } : { code: "too_small", minimum: def.length }),
            inclusive: true,
            exact: true,
            input: payload.value,
            inst,
            continue: !def.abort,
        });
    };
});
const $ZodCheckStringFormat = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckStringFormat", (inst, def) => {
    var _a, _b;
    $ZodCheck.init(inst, def);
    inst._zod.onattach.push((inst) => {
        const bag = inst._zod.bag;
        bag.format = def.format;
        if (def.pattern) {
            bag.patterns ?? (bag.patterns = new Set());
            bag.patterns.add(def.pattern);
        }
    });
    if (def.pattern)
        (_a = inst._zod).check ?? (_a.check = (payload) => {
            def.pattern.lastIndex = 0;
            if (def.pattern.test(payload.value))
                return;
            payload.issues.push({
                origin: "string",
                code: "invalid_format",
                format: def.format,
                input: payload.value,
                ...(def.pattern ? { pattern: def.pattern.toString() } : {}),
                inst,
                continue: !def.abort,
            });
        });
    else
        (_b = inst._zod).check ?? (_b.check = () => { });
});
const $ZodCheckRegex = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckRegex", (inst, def) => {
    $ZodCheckStringFormat.init(inst, def);
    inst._zod.check = (payload) => {
        def.pattern.lastIndex = 0;
        if (def.pattern.test(payload.value))
            return;
        payload.issues.push({
            origin: "string",
            code: "invalid_format",
            format: "regex",
            input: payload.value,
            pattern: def.pattern.toString(),
            inst,
            continue: !def.abort,
        });
    };
});
const $ZodCheckLowerCase = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckLowerCase", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_1__.lowercase);
    $ZodCheckStringFormat.init(inst, def);
});
const $ZodCheckUpperCase = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckUpperCase", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_1__.uppercase);
    $ZodCheckStringFormat.init(inst, def);
});
const $ZodCheckIncludes = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckIncludes", (inst, def) => {
    $ZodCheck.init(inst, def);
    const escapedRegex = _util_js__WEBPACK_IMPORTED_MODULE_2__.escapeRegex(def.includes);
    const pattern = new RegExp(typeof def.position === "number" ? `^.{${def.position}}${escapedRegex}` : escapedRegex);
    def.pattern = pattern;
    inst._zod.onattach.push((inst) => {
        const bag = inst._zod.bag;
        bag.patterns ?? (bag.patterns = new Set());
        bag.patterns.add(pattern);
    });
    inst._zod.check = (payload) => {
        if (payload.value.includes(def.includes, def.position))
            return;
        payload.issues.push({
            origin: "string",
            code: "invalid_format",
            format: "includes",
            includes: def.includes,
            input: payload.value,
            inst,
            continue: !def.abort,
        });
    };
});
const $ZodCheckStartsWith = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckStartsWith", (inst, def) => {
    $ZodCheck.init(inst, def);
    const pattern = new RegExp(`^${_util_js__WEBPACK_IMPORTED_MODULE_2__.escapeRegex(def.prefix)}.*`);
    def.pattern ?? (def.pattern = pattern);
    inst._zod.onattach.push((inst) => {
        const bag = inst._zod.bag;
        bag.patterns ?? (bag.patterns = new Set());
        bag.patterns.add(pattern);
    });
    inst._zod.check = (payload) => {
        if (payload.value.startsWith(def.prefix))
            return;
        payload.issues.push({
            origin: "string",
            code: "invalid_format",
            format: "starts_with",
            prefix: def.prefix,
            input: payload.value,
            inst,
            continue: !def.abort,
        });
    };
});
const $ZodCheckEndsWith = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckEndsWith", (inst, def) => {
    $ZodCheck.init(inst, def);
    const pattern = new RegExp(`.*${_util_js__WEBPACK_IMPORTED_MODULE_2__.escapeRegex(def.suffix)}$`);
    def.pattern ?? (def.pattern = pattern);
    inst._zod.onattach.push((inst) => {
        const bag = inst._zod.bag;
        bag.patterns ?? (bag.patterns = new Set());
        bag.patterns.add(pattern);
    });
    inst._zod.check = (payload) => {
        if (payload.value.endsWith(def.suffix))
            return;
        payload.issues.push({
            origin: "string",
            code: "invalid_format",
            format: "ends_with",
            suffix: def.suffix,
            input: payload.value,
            inst,
            continue: !def.abort,
        });
    };
});
///////////////////////////////////
/////    $ZodCheckProperty    /////
///////////////////////////////////
function handleCheckPropertyResult(result, payload, property) {
    if (result.issues.length) {
        payload.issues.push(..._util_js__WEBPACK_IMPORTED_MODULE_2__.prefixIssues(property, result.issues));
    }
}
const $ZodCheckProperty = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckProperty", (inst, def) => {
    $ZodCheck.init(inst, def);
    inst._zod.check = (payload) => {
        const result = def.schema._zod.run({
            value: payload.value[def.property],
            issues: [],
        }, {});
        if (result instanceof Promise) {
            return result.then((result) => handleCheckPropertyResult(result, payload, def.property));
        }
        handleCheckPropertyResult(result, payload, def.property);
        return;
    };
});
const $ZodCheckMimeType = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckMimeType", (inst, def) => {
    $ZodCheck.init(inst, def);
    const mimeSet = new Set(def.mime);
    inst._zod.onattach.push((inst) => {
        inst._zod.bag.mime = def.mime;
    });
    inst._zod.check = (payload) => {
        if (mimeSet.has(payload.value.type))
            return;
        payload.issues.push({
            code: "invalid_value",
            values: def.mime,
            input: payload.value.type,
            inst,
        });
    };
});
const $ZodCheckOverwrite = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor("$ZodCheckOverwrite", (inst, def) => {
    $ZodCheck.init(inst, def);
    inst._zod.check = (payload) => {
        payload.value = def.tx(payload.value);
    };
});


/***/ }),

/***/ "./node_modules/zod/v4/core/core.js":
/*!******************************************!*\
  !*** ./node_modules/zod/v4/core/core.js ***!
  \******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   $ZodAsyncError: function() { return /* binding */ $ZodAsyncError; },
/* harmony export */   $brand: function() { return /* binding */ $brand; },
/* harmony export */   $constructor: function() { return /* binding */ $constructor; },
/* harmony export */   NEVER: function() { return /* binding */ NEVER; },
/* harmony export */   config: function() { return /* binding */ config; },
/* harmony export */   globalConfig: function() { return /* binding */ globalConfig; }
/* harmony export */ });
/** A special constant with type `never` */
const NEVER = Object.freeze({
    status: "aborted",
});
function $constructor(name, initializer, params) {
    function init(inst, def) {
        var _a;
        Object.defineProperty(inst, "_zod", {
            value: inst._zod ?? {},
            enumerable: false,
        });
        (_a = inst._zod).traits ?? (_a.traits = new Set());
        inst._zod.traits.add(name);
        initializer(inst, def);
        // support prototype modifications
        for (const k in _.prototype) {
            if (!(k in inst))
                Object.defineProperty(inst, k, { value: _.prototype[k].bind(inst) });
        }
        inst._zod.constr = _;
        inst._zod.def = def;
    }
    // doesn't work if Parent has a constructor with arguments
    const Parent = params?.Parent ?? Object;
    class Definition extends Parent {
    }
    Object.defineProperty(Definition, "name", { value: name });
    function _(def) {
        var _a;
        const inst = params?.Parent ? new Definition() : this;
        init(inst, def);
        (_a = inst._zod).deferred ?? (_a.deferred = []);
        for (const fn of inst._zod.deferred) {
            fn();
        }
        return inst;
    }
    Object.defineProperty(_, "init", { value: init });
    Object.defineProperty(_, Symbol.hasInstance, {
        value: (inst) => {
            if (params?.Parent && inst instanceof params.Parent)
                return true;
            return inst?._zod?.traits?.has(name);
        },
    });
    Object.defineProperty(_, "name", { value: name });
    return _;
}
//////////////////////////////   UTILITIES   ///////////////////////////////////////
const $brand = Symbol("zod_brand");
class $ZodAsyncError extends Error {
    constructor() {
        super(`Encountered Promise during synchronous parse. Use .parseAsync() instead.`);
    }
}
const globalConfig = {};
function config(newConfig) {
    if (newConfig)
        Object.assign(globalConfig, newConfig);
    return globalConfig;
}


/***/ }),

/***/ "./node_modules/zod/v4/core/doc.js":
/*!*****************************************!*\
  !*** ./node_modules/zod/v4/core/doc.js ***!
  \*****************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Doc: function() { return /* binding */ Doc; }
/* harmony export */ });
class Doc {
    constructor(args = []) {
        this.content = [];
        this.indent = 0;
        if (this)
            this.args = args;
    }
    indented(fn) {
        this.indent += 1;
        fn(this);
        this.indent -= 1;
    }
    write(arg) {
        if (typeof arg === "function") {
            arg(this, { execution: "sync" });
            arg(this, { execution: "async" });
            return;
        }
        const content = arg;
        const lines = content.split("\n").filter((x) => x);
        const minIndent = Math.min(...lines.map((x) => x.length - x.trimStart().length));
        const dedented = lines.map((x) => x.slice(minIndent)).map((x) => " ".repeat(this.indent * 2) + x);
        for (const line of dedented) {
            this.content.push(line);
        }
    }
    compile() {
        const F = Function;
        const args = this?.args;
        const content = this?.content ?? [``];
        const lines = [...content.map((x) => `  ${x}`)];
        // console.log(lines.join("\n"));
        return new F(...args, lines.join("\n"));
    }
}


/***/ }),

/***/ "./node_modules/zod/v4/core/errors.js":
/*!********************************************!*\
  !*** ./node_modules/zod/v4/core/errors.js ***!
  \********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   $ZodError: function() { return /* binding */ $ZodError; },
/* harmony export */   $ZodRealError: function() { return /* binding */ $ZodRealError; },
/* harmony export */   flattenError: function() { return /* binding */ flattenError; },
/* harmony export */   formatError: function() { return /* binding */ formatError; },
/* harmony export */   prettifyError: function() { return /* binding */ prettifyError; },
/* harmony export */   toDotPath: function() { return /* binding */ toDotPath; },
/* harmony export */   treeifyError: function() { return /* binding */ treeifyError; }
/* harmony export */ });
/* harmony import */ var _core_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./core.js */ "./node_modules/zod/v4/core/core.js");
/* harmony import */ var _util_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./util.js */ "./node_modules/zod/v4/core/util.js");


const initializer = (inst, def) => {
    inst.name = "$ZodError";
    Object.defineProperty(inst, "_zod", {
        value: inst._zod,
        enumerable: false,
    });
    Object.defineProperty(inst, "issues", {
        value: def,
        enumerable: false,
    });
    Object.defineProperty(inst, "message", {
        get() {
            return JSON.stringify(def, _util_js__WEBPACK_IMPORTED_MODULE_1__.jsonStringifyReplacer, 2);
        },
        enumerable: true,
        // configurable: false,
    });
    Object.defineProperty(inst, "toString", {
        value: () => inst.message,
        enumerable: false,
    });
};
const $ZodError = (0,_core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor)("$ZodError", initializer);
const $ZodRealError = (0,_core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor)("$ZodError", initializer, { Parent: Error });
function flattenError(error, mapper = (issue) => issue.message) {
    const fieldErrors = {};
    const formErrors = [];
    for (const sub of error.issues) {
        if (sub.path.length > 0) {
            fieldErrors[sub.path[0]] = fieldErrors[sub.path[0]] || [];
            fieldErrors[sub.path[0]].push(mapper(sub));
        }
        else {
            formErrors.push(mapper(sub));
        }
    }
    return { formErrors, fieldErrors };
}
function formatError(error, _mapper) {
    const mapper = _mapper ||
        function (issue) {
            return issue.message;
        };
    const fieldErrors = { _errors: [] };
    const processError = (error) => {
        for (const issue of error.issues) {
            if (issue.code === "invalid_union" && issue.errors.length) {
                issue.errors.map((issues) => processError({ issues }));
            }
            else if (issue.code === "invalid_key") {
                processError({ issues: issue.issues });
            }
            else if (issue.code === "invalid_element") {
                processError({ issues: issue.issues });
            }
            else if (issue.path.length === 0) {
                fieldErrors._errors.push(mapper(issue));
            }
            else {
                let curr = fieldErrors;
                let i = 0;
                while (i < issue.path.length) {
                    const el = issue.path[i];
                    const terminal = i === issue.path.length - 1;
                    if (!terminal) {
                        curr[el] = curr[el] || { _errors: [] };
                    }
                    else {
                        curr[el] = curr[el] || { _errors: [] };
                        curr[el]._errors.push(mapper(issue));
                    }
                    curr = curr[el];
                    i++;
                }
            }
        }
    };
    processError(error);
    return fieldErrors;
}
function treeifyError(error, _mapper) {
    const mapper = _mapper ||
        function (issue) {
            return issue.message;
        };
    const result = { errors: [] };
    const processError = (error, path = []) => {
        var _a, _b;
        for (const issue of error.issues) {
            if (issue.code === "invalid_union" && issue.errors.length) {
                // regular union error
                issue.errors.map((issues) => processError({ issues }, issue.path));
            }
            else if (issue.code === "invalid_key") {
                processError({ issues: issue.issues }, issue.path);
            }
            else if (issue.code === "invalid_element") {
                processError({ issues: issue.issues }, issue.path);
            }
            else {
                const fullpath = [...path, ...issue.path];
                if (fullpath.length === 0) {
                    result.errors.push(mapper(issue));
                    continue;
                }
                let curr = result;
                let i = 0;
                while (i < fullpath.length) {
                    const el = fullpath[i];
                    const terminal = i === fullpath.length - 1;
                    if (typeof el === "string") {
                        curr.properties ?? (curr.properties = {});
                        (_a = curr.properties)[el] ?? (_a[el] = { errors: [] });
                        curr = curr.properties[el];
                    }
                    else {
                        curr.items ?? (curr.items = []);
                        (_b = curr.items)[el] ?? (_b[el] = { errors: [] });
                        curr = curr.items[el];
                    }
                    if (terminal) {
                        curr.errors.push(mapper(issue));
                    }
                    i++;
                }
            }
        }
    };
    processError(error);
    return result;
}
/** Format a ZodError as a human-readable string in the following form.
 *
 * From
 *
 * ```ts
 * ZodError {
 *   issues: [
 *     {
 *       expected: 'string',
 *       code: 'invalid_type',
 *       path: [ 'username' ],
 *       message: 'Invalid input: expected string'
 *     },
 *     {
 *       expected: 'number',
 *       code: 'invalid_type',
 *       path: [ 'favoriteNumbers', 1 ],
 *       message: 'Invalid input: expected number'
 *     }
 *   ];
 * }
 * ```
 *
 * to
 *
 * ```
 * username
 *   ✖ Expected number, received string at "username
 * favoriteNumbers[0]
 *   ✖ Invalid input: expected number
 * ```
 */
function toDotPath(path) {
    const segs = [];
    for (const seg of path) {
        if (typeof seg === "number")
            segs.push(`[${seg}]`);
        else if (typeof seg === "symbol")
            segs.push(`[${JSON.stringify(String(seg))}]`);
        else if (/[^\w$]/.test(seg))
            segs.push(`[${JSON.stringify(seg)}]`);
        else {
            if (segs.length)
                segs.push(".");
            segs.push(seg);
        }
    }
    return segs.join("");
}
function prettifyError(error) {
    const lines = [];
    // sort by path length
    const issues = [...error.issues].sort((a, b) => a.path.length - b.path.length);
    // Process each issue
    for (const issue of issues) {
        lines.push(`✖ ${issue.message}`);
        if (issue.path?.length)
            lines.push(`  → at ${toDotPath(issue.path)}`);
    }
    // Convert Map to formatted string
    return lines.join("\n");
}


/***/ }),

/***/ "./node_modules/zod/v4/core/function.js":
/*!**********************************************!*\
  !*** ./node_modules/zod/v4/core/function.js ***!
  \**********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   $ZodFunction: function() { return /* binding */ $ZodFunction; },
/* harmony export */   "function": function() { return /* binding */ _function; }
/* harmony export */ });
/* harmony import */ var _api_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./api.js */ "./node_modules/zod/v4/core/api.js");
/* harmony import */ var _parse_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./parse.js */ "./node_modules/zod/v4/core/parse.js");
/* harmony import */ var _schemas_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./schemas.js */ "./node_modules/zod/v4/core/schemas.js");




class $ZodFunction {
    constructor(def) {
        this._def = def;
        this.def = def;
    }
    implement(func) {
        if (typeof func !== "function") {
            throw new Error("implement() must be called with a function");
        }
        const impl = ((...args) => {
            const parsedArgs = this._def.input ? (0,_parse_js__WEBPACK_IMPORTED_MODULE_1__.parse)(this._def.input, args, undefined, { callee: impl }) : args;
            if (!Array.isArray(parsedArgs)) {
                throw new Error("Invalid arguments schema: not an array or tuple schema.");
            }
            const output = func(...parsedArgs);
            return this._def.output ? (0,_parse_js__WEBPACK_IMPORTED_MODULE_1__.parse)(this._def.output, output, undefined, { callee: impl }) : output;
        });
        return impl;
    }
    implementAsync(func) {
        if (typeof func !== "function") {
            throw new Error("implement() must be called with a function");
        }
        const impl = (async (...args) => {
            const parsedArgs = this._def.input ? await (0,_parse_js__WEBPACK_IMPORTED_MODULE_1__.parseAsync)(this._def.input, args, undefined, { callee: impl }) : args;
            if (!Array.isArray(parsedArgs)) {
                throw new Error("Invalid arguments schema: not an array or tuple schema.");
            }
            const output = await func(...parsedArgs);
            return this._def.output ? (0,_parse_js__WEBPACK_IMPORTED_MODULE_1__.parseAsync)(this._def.output, output, undefined, { callee: impl }) : output;
        });
        return impl;
    }
    input(...args) {
        const F = this.constructor;
        if (Array.isArray(args[0])) {
            return new F({
                type: "function",
                input: new _schemas_js__WEBPACK_IMPORTED_MODULE_2__.$ZodTuple({
                    type: "tuple",
                    items: args[0],
                    rest: args[1],
                }),
                output: this._def.output,
            });
        }
        return new F({
            type: "function",
            input: args[0],
            output: this._def.output,
        });
    }
    output(output) {
        const F = this.constructor;
        return new F({
            type: "function",
            input: this._def.input,
            output,
        });
    }
}
function _function(params) {
    return new $ZodFunction({
        type: "function",
        input: Array.isArray(params?.input)
            ? (0,_api_js__WEBPACK_IMPORTED_MODULE_0__._tuple)(_schemas_js__WEBPACK_IMPORTED_MODULE_2__.$ZodTuple, params?.input)
            : (params?.input ?? (0,_api_js__WEBPACK_IMPORTED_MODULE_0__._array)(_schemas_js__WEBPACK_IMPORTED_MODULE_2__.$ZodArray, (0,_api_js__WEBPACK_IMPORTED_MODULE_0__._unknown)(_schemas_js__WEBPACK_IMPORTED_MODULE_2__.$ZodUnknown))),
        output: params?.output ?? (0,_api_js__WEBPACK_IMPORTED_MODULE_0__._unknown)(_schemas_js__WEBPACK_IMPORTED_MODULE_2__.$ZodUnknown),
    });
}



/***/ }),

/***/ "./node_modules/zod/v4/core/index.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/core/index.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   $ZodAny: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodAny; },
/* harmony export */   $ZodArray: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodArray; },
/* harmony export */   $ZodAsyncError: function() { return /* reexport safe */ _core_js__WEBPACK_IMPORTED_MODULE_0__.$ZodAsyncError; },
/* harmony export */   $ZodBase64: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodBase64; },
/* harmony export */   $ZodBase64URL: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodBase64URL; },
/* harmony export */   $ZodBigInt: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodBigInt; },
/* harmony export */   $ZodBigIntFormat: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodBigIntFormat; },
/* harmony export */   $ZodBoolean: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodBoolean; },
/* harmony export */   $ZodCIDRv4: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodCIDRv4; },
/* harmony export */   $ZodCIDRv6: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodCIDRv6; },
/* harmony export */   $ZodCUID: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodCUID; },
/* harmony export */   $ZodCUID2: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodCUID2; },
/* harmony export */   $ZodCatch: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodCatch; },
/* harmony export */   $ZodCheck: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheck; },
/* harmony export */   $ZodCheckBigIntFormat: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckBigIntFormat; },
/* harmony export */   $ZodCheckEndsWith: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckEndsWith; },
/* harmony export */   $ZodCheckGreaterThan: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckGreaterThan; },
/* harmony export */   $ZodCheckIncludes: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckIncludes; },
/* harmony export */   $ZodCheckLengthEquals: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckLengthEquals; },
/* harmony export */   $ZodCheckLessThan: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckLessThan; },
/* harmony export */   $ZodCheckLowerCase: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckLowerCase; },
/* harmony export */   $ZodCheckMaxLength: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckMaxLength; },
/* harmony export */   $ZodCheckMaxSize: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckMaxSize; },
/* harmony export */   $ZodCheckMimeType: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckMimeType; },
/* harmony export */   $ZodCheckMinLength: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckMinLength; },
/* harmony export */   $ZodCheckMinSize: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckMinSize; },
/* harmony export */   $ZodCheckMultipleOf: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckMultipleOf; },
/* harmony export */   $ZodCheckNumberFormat: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckNumberFormat; },
/* harmony export */   $ZodCheckOverwrite: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckOverwrite; },
/* harmony export */   $ZodCheckProperty: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckProperty; },
/* harmony export */   $ZodCheckRegex: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckRegex; },
/* harmony export */   $ZodCheckSizeEquals: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckSizeEquals; },
/* harmony export */   $ZodCheckStartsWith: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckStartsWith; },
/* harmony export */   $ZodCheckStringFormat: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckStringFormat; },
/* harmony export */   $ZodCheckUpperCase: function() { return /* reexport safe */ _checks_js__WEBPACK_IMPORTED_MODULE_4__.$ZodCheckUpperCase; },
/* harmony export */   $ZodCustom: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodCustom; },
/* harmony export */   $ZodCustomStringFormat: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodCustomStringFormat; },
/* harmony export */   $ZodDate: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodDate; },
/* harmony export */   $ZodDefault: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodDefault; },
/* harmony export */   $ZodDiscriminatedUnion: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodDiscriminatedUnion; },
/* harmony export */   $ZodE164: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodE164; },
/* harmony export */   $ZodEmail: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodEmail; },
/* harmony export */   $ZodEmoji: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodEmoji; },
/* harmony export */   $ZodEnum: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodEnum; },
/* harmony export */   $ZodError: function() { return /* reexport safe */ _errors_js__WEBPACK_IMPORTED_MODULE_2__.$ZodError; },
/* harmony export */   $ZodFile: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodFile; },
/* harmony export */   $ZodFunction: function() { return /* reexport safe */ _function_js__WEBPACK_IMPORTED_MODULE_11__.$ZodFunction; },
/* harmony export */   $ZodGUID: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodGUID; },
/* harmony export */   $ZodIPv4: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodIPv4; },
/* harmony export */   $ZodIPv6: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodIPv6; },
/* harmony export */   $ZodISODate: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodISODate; },
/* harmony export */   $ZodISODateTime: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodISODateTime; },
/* harmony export */   $ZodISODuration: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodISODuration; },
/* harmony export */   $ZodISOTime: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodISOTime; },
/* harmony export */   $ZodIntersection: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodIntersection; },
/* harmony export */   $ZodJWT: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodJWT; },
/* harmony export */   $ZodKSUID: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodKSUID; },
/* harmony export */   $ZodLazy: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodLazy; },
/* harmony export */   $ZodLiteral: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodLiteral; },
/* harmony export */   $ZodMap: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodMap; },
/* harmony export */   $ZodNaN: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodNaN; },
/* harmony export */   $ZodNanoID: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodNanoID; },
/* harmony export */   $ZodNever: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodNever; },
/* harmony export */   $ZodNonOptional: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodNonOptional; },
/* harmony export */   $ZodNull: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodNull; },
/* harmony export */   $ZodNullable: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodNullable; },
/* harmony export */   $ZodNumber: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodNumber; },
/* harmony export */   $ZodNumberFormat: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodNumberFormat; },
/* harmony export */   $ZodObject: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodObject; },
/* harmony export */   $ZodOptional: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodOptional; },
/* harmony export */   $ZodPipe: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodPipe; },
/* harmony export */   $ZodPrefault: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodPrefault; },
/* harmony export */   $ZodPromise: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodPromise; },
/* harmony export */   $ZodReadonly: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodReadonly; },
/* harmony export */   $ZodRealError: function() { return /* reexport safe */ _errors_js__WEBPACK_IMPORTED_MODULE_2__.$ZodRealError; },
/* harmony export */   $ZodRecord: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodRecord; },
/* harmony export */   $ZodRegistry: function() { return /* reexport safe */ _registries_js__WEBPACK_IMPORTED_MODULE_9__.$ZodRegistry; },
/* harmony export */   $ZodSet: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodSet; },
/* harmony export */   $ZodString: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodString; },
/* harmony export */   $ZodStringFormat: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodStringFormat; },
/* harmony export */   $ZodSuccess: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodSuccess; },
/* harmony export */   $ZodSymbol: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodSymbol; },
/* harmony export */   $ZodTemplateLiteral: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodTemplateLiteral; },
/* harmony export */   $ZodTransform: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodTransform; },
/* harmony export */   $ZodTuple: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodTuple; },
/* harmony export */   $ZodType: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodType; },
/* harmony export */   $ZodULID: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodULID; },
/* harmony export */   $ZodURL: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodURL; },
/* harmony export */   $ZodUUID: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodUUID; },
/* harmony export */   $ZodUndefined: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodUndefined; },
/* harmony export */   $ZodUnion: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodUnion; },
/* harmony export */   $ZodUnknown: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodUnknown; },
/* harmony export */   $ZodVoid: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodVoid; },
/* harmony export */   $ZodXID: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.$ZodXID; },
/* harmony export */   $brand: function() { return /* reexport safe */ _core_js__WEBPACK_IMPORTED_MODULE_0__.$brand; },
/* harmony export */   $constructor: function() { return /* reexport safe */ _core_js__WEBPACK_IMPORTED_MODULE_0__.$constructor; },
/* harmony export */   $input: function() { return /* reexport safe */ _registries_js__WEBPACK_IMPORTED_MODULE_9__.$input; },
/* harmony export */   $output: function() { return /* reexport safe */ _registries_js__WEBPACK_IMPORTED_MODULE_9__.$output; },
/* harmony export */   Doc: function() { return /* reexport safe */ _doc_js__WEBPACK_IMPORTED_MODULE_10__.Doc; },
/* harmony export */   JSONSchema: function() { return /* reexport module object */ _json_schema_js__WEBPACK_IMPORTED_MODULE_14__; },
/* harmony export */   JSONSchemaGenerator: function() { return /* reexport safe */ _to_json_schema_js__WEBPACK_IMPORTED_MODULE_13__.JSONSchemaGenerator; },
/* harmony export */   NEVER: function() { return /* reexport safe */ _core_js__WEBPACK_IMPORTED_MODULE_0__.NEVER; },
/* harmony export */   TimePrecision: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__.TimePrecision; },
/* harmony export */   _any: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._any; },
/* harmony export */   _array: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._array; },
/* harmony export */   _base64: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._base64; },
/* harmony export */   _base64url: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._base64url; },
/* harmony export */   _bigint: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._bigint; },
/* harmony export */   _boolean: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._boolean; },
/* harmony export */   _catch: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._catch; },
/* harmony export */   _cidrv4: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._cidrv4; },
/* harmony export */   _cidrv6: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._cidrv6; },
/* harmony export */   _coercedBigint: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._coercedBigint; },
/* harmony export */   _coercedBoolean: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._coercedBoolean; },
/* harmony export */   _coercedDate: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._coercedDate; },
/* harmony export */   _coercedNumber: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._coercedNumber; },
/* harmony export */   _coercedString: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._coercedString; },
/* harmony export */   _cuid: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._cuid; },
/* harmony export */   _cuid2: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._cuid2; },
/* harmony export */   _custom: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._custom; },
/* harmony export */   _date: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._date; },
/* harmony export */   _default: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._default; },
/* harmony export */   _discriminatedUnion: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._discriminatedUnion; },
/* harmony export */   _e164: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._e164; },
/* harmony export */   _email: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._email; },
/* harmony export */   _emoji: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._emoji; },
/* harmony export */   _endsWith: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._endsWith; },
/* harmony export */   _enum: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._enum; },
/* harmony export */   _file: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._file; },
/* harmony export */   _float32: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._float32; },
/* harmony export */   _float64: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._float64; },
/* harmony export */   _gt: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._gt; },
/* harmony export */   _gte: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._gte; },
/* harmony export */   _guid: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._guid; },
/* harmony export */   _includes: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._includes; },
/* harmony export */   _int: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._int; },
/* harmony export */   _int32: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._int32; },
/* harmony export */   _int64: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._int64; },
/* harmony export */   _intersection: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._intersection; },
/* harmony export */   _ipv4: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._ipv4; },
/* harmony export */   _ipv6: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._ipv6; },
/* harmony export */   _isoDate: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._isoDate; },
/* harmony export */   _isoDateTime: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._isoDateTime; },
/* harmony export */   _isoDuration: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._isoDuration; },
/* harmony export */   _isoTime: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._isoTime; },
/* harmony export */   _jwt: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._jwt; },
/* harmony export */   _ksuid: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._ksuid; },
/* harmony export */   _lazy: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._lazy; },
/* harmony export */   _length: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._length; },
/* harmony export */   _literal: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._literal; },
/* harmony export */   _lowercase: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._lowercase; },
/* harmony export */   _lt: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._lt; },
/* harmony export */   _lte: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._lte; },
/* harmony export */   _map: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._map; },
/* harmony export */   _max: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._max; },
/* harmony export */   _maxLength: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._maxLength; },
/* harmony export */   _maxSize: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._maxSize; },
/* harmony export */   _mime: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._mime; },
/* harmony export */   _min: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._min; },
/* harmony export */   _minLength: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._minLength; },
/* harmony export */   _minSize: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._minSize; },
/* harmony export */   _multipleOf: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._multipleOf; },
/* harmony export */   _nan: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._nan; },
/* harmony export */   _nanoid: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._nanoid; },
/* harmony export */   _nativeEnum: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._nativeEnum; },
/* harmony export */   _negative: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._negative; },
/* harmony export */   _never: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._never; },
/* harmony export */   _nonnegative: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._nonnegative; },
/* harmony export */   _nonoptional: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._nonoptional; },
/* harmony export */   _nonpositive: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._nonpositive; },
/* harmony export */   _normalize: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._normalize; },
/* harmony export */   _null: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._null; },
/* harmony export */   _nullable: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._nullable; },
/* harmony export */   _number: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._number; },
/* harmony export */   _optional: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._optional; },
/* harmony export */   _overwrite: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._overwrite; },
/* harmony export */   _parse: function() { return /* reexport safe */ _parse_js__WEBPACK_IMPORTED_MODULE_1__._parse; },
/* harmony export */   _parseAsync: function() { return /* reexport safe */ _parse_js__WEBPACK_IMPORTED_MODULE_1__._parseAsync; },
/* harmony export */   _pipe: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._pipe; },
/* harmony export */   _positive: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._positive; },
/* harmony export */   _promise: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._promise; },
/* harmony export */   _property: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._property; },
/* harmony export */   _readonly: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._readonly; },
/* harmony export */   _record: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._record; },
/* harmony export */   _refine: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._refine; },
/* harmony export */   _regex: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._regex; },
/* harmony export */   _safeParse: function() { return /* reexport safe */ _parse_js__WEBPACK_IMPORTED_MODULE_1__._safeParse; },
/* harmony export */   _safeParseAsync: function() { return /* reexport safe */ _parse_js__WEBPACK_IMPORTED_MODULE_1__._safeParseAsync; },
/* harmony export */   _set: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._set; },
/* harmony export */   _size: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._size; },
/* harmony export */   _startsWith: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._startsWith; },
/* harmony export */   _string: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._string; },
/* harmony export */   _stringFormat: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._stringFormat; },
/* harmony export */   _stringbool: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._stringbool; },
/* harmony export */   _success: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._success; },
/* harmony export */   _symbol: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._symbol; },
/* harmony export */   _templateLiteral: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._templateLiteral; },
/* harmony export */   _toLowerCase: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._toLowerCase; },
/* harmony export */   _toUpperCase: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._toUpperCase; },
/* harmony export */   _transform: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._transform; },
/* harmony export */   _trim: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._trim; },
/* harmony export */   _tuple: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._tuple; },
/* harmony export */   _uint32: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._uint32; },
/* harmony export */   _uint64: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._uint64; },
/* harmony export */   _ulid: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._ulid; },
/* harmony export */   _undefined: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._undefined; },
/* harmony export */   _union: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._union; },
/* harmony export */   _unknown: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._unknown; },
/* harmony export */   _uppercase: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._uppercase; },
/* harmony export */   _url: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._url; },
/* harmony export */   _uuid: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._uuid; },
/* harmony export */   _uuidv4: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._uuidv4; },
/* harmony export */   _uuidv6: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._uuidv6; },
/* harmony export */   _uuidv7: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._uuidv7; },
/* harmony export */   _void: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._void; },
/* harmony export */   _xid: function() { return /* reexport safe */ _api_js__WEBPACK_IMPORTED_MODULE_12__._xid; },
/* harmony export */   clone: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.clone; },
/* harmony export */   config: function() { return /* reexport safe */ _core_js__WEBPACK_IMPORTED_MODULE_0__.config; },
/* harmony export */   flattenError: function() { return /* reexport safe */ _errors_js__WEBPACK_IMPORTED_MODULE_2__.flattenError; },
/* harmony export */   formatError: function() { return /* reexport safe */ _errors_js__WEBPACK_IMPORTED_MODULE_2__.formatError; },
/* harmony export */   "function": function() { return /* reexport safe */ _function_js__WEBPACK_IMPORTED_MODULE_11__["function"]; },
/* harmony export */   globalConfig: function() { return /* reexport safe */ _core_js__WEBPACK_IMPORTED_MODULE_0__.globalConfig; },
/* harmony export */   globalRegistry: function() { return /* reexport safe */ _registries_js__WEBPACK_IMPORTED_MODULE_9__.globalRegistry; },
/* harmony export */   isValidBase64: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.isValidBase64; },
/* harmony export */   isValidBase64URL: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.isValidBase64URL; },
/* harmony export */   isValidJWT: function() { return /* reexport safe */ _schemas_js__WEBPACK_IMPORTED_MODULE_3__.isValidJWT; },
/* harmony export */   locales: function() { return /* reexport module object */ _locales_index_js__WEBPACK_IMPORTED_MODULE_8__; },
/* harmony export */   parse: function() { return /* reexport safe */ _parse_js__WEBPACK_IMPORTED_MODULE_1__.parse; },
/* harmony export */   parseAsync: function() { return /* reexport safe */ _parse_js__WEBPACK_IMPORTED_MODULE_1__.parseAsync; },
/* harmony export */   prettifyError: function() { return /* reexport safe */ _errors_js__WEBPACK_IMPORTED_MODULE_2__.prettifyError; },
/* harmony export */   regexes: function() { return /* reexport module object */ _regexes_js__WEBPACK_IMPORTED_MODULE_7__; },
/* harmony export */   registry: function() { return /* reexport safe */ _registries_js__WEBPACK_IMPORTED_MODULE_9__.registry; },
/* harmony export */   safeParse: function() { return /* reexport safe */ _parse_js__WEBPACK_IMPORTED_MODULE_1__.safeParse; },
/* harmony export */   safeParseAsync: function() { return /* reexport safe */ _parse_js__WEBPACK_IMPORTED_MODULE_1__.safeParseAsync; },
/* harmony export */   toDotPath: function() { return /* reexport safe */ _errors_js__WEBPACK_IMPORTED_MODULE_2__.toDotPath; },
/* harmony export */   toJSONSchema: function() { return /* reexport safe */ _to_json_schema_js__WEBPACK_IMPORTED_MODULE_13__.toJSONSchema; },
/* harmony export */   treeifyError: function() { return /* reexport safe */ _errors_js__WEBPACK_IMPORTED_MODULE_2__.treeifyError; },
/* harmony export */   util: function() { return /* reexport module object */ _util_js__WEBPACK_IMPORTED_MODULE_6__; },
/* harmony export */   version: function() { return /* reexport safe */ _versions_js__WEBPACK_IMPORTED_MODULE_5__.version; }
/* harmony export */ });
/* harmony import */ var _core_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./core.js */ "./node_modules/zod/v4/core/core.js");
/* harmony import */ var _parse_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./parse.js */ "./node_modules/zod/v4/core/parse.js");
/* harmony import */ var _errors_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./errors.js */ "./node_modules/zod/v4/core/errors.js");
/* harmony import */ var _schemas_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./schemas.js */ "./node_modules/zod/v4/core/schemas.js");
/* harmony import */ var _checks_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./checks.js */ "./node_modules/zod/v4/core/checks.js");
/* harmony import */ var _versions_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./versions.js */ "./node_modules/zod/v4/core/versions.js");
/* harmony import */ var _util_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./util.js */ "./node_modules/zod/v4/core/util.js");
/* harmony import */ var _regexes_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./regexes.js */ "./node_modules/zod/v4/core/regexes.js");
/* harmony import */ var _locales_index_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../locales/index.js */ "./node_modules/zod/v4/locales/index.js");
/* harmony import */ var _registries_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./registries.js */ "./node_modules/zod/v4/core/registries.js");
/* harmony import */ var _doc_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./doc.js */ "./node_modules/zod/v4/core/doc.js");
/* harmony import */ var _function_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./function.js */ "./node_modules/zod/v4/core/function.js");
/* harmony import */ var _api_js__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./api.js */ "./node_modules/zod/v4/core/api.js");
/* harmony import */ var _to_json_schema_js__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./to-json-schema.js */ "./node_modules/zod/v4/core/to-json-schema.js");
/* harmony import */ var _json_schema_js__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./json-schema.js */ "./node_modules/zod/v4/core/json-schema.js");

















/***/ }),

/***/ "./node_modules/zod/v4/core/json-schema.js":
/*!*************************************************!*\
  !*** ./node_modules/zod/v4/core/json-schema.js ***!
  \*************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);



/***/ }),

/***/ "./node_modules/zod/v4/core/parse.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/core/parse.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   _parse: function() { return /* binding */ _parse; },
/* harmony export */   _parseAsync: function() { return /* binding */ _parseAsync; },
/* harmony export */   _safeParse: function() { return /* binding */ _safeParse; },
/* harmony export */   _safeParseAsync: function() { return /* binding */ _safeParseAsync; },
/* harmony export */   parse: function() { return /* binding */ parse; },
/* harmony export */   parseAsync: function() { return /* binding */ parseAsync; },
/* harmony export */   safeParse: function() { return /* binding */ safeParse; },
/* harmony export */   safeParseAsync: function() { return /* binding */ safeParseAsync; }
/* harmony export */ });
/* harmony import */ var _core_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./core.js */ "./node_modules/zod/v4/core/core.js");
/* harmony import */ var _errors_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./errors.js */ "./node_modules/zod/v4/core/errors.js");
/* harmony import */ var _util_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./util.js */ "./node_modules/zod/v4/core/util.js");



const _parse = (_Err) => (schema, value, _ctx, _params) => {
    const ctx = _ctx ? Object.assign(_ctx, { async: false }) : { async: false };
    const result = schema._zod.run({ value, issues: [] }, ctx);
    if (result instanceof Promise) {
        throw new _core_js__WEBPACK_IMPORTED_MODULE_0__.$ZodAsyncError();
    }
    if (result.issues.length) {
        const e = new (_params?.Err ?? _Err)(result.issues.map((iss) => _util_js__WEBPACK_IMPORTED_MODULE_2__.finalizeIssue(iss, ctx, _core_js__WEBPACK_IMPORTED_MODULE_0__.config())));
        _util_js__WEBPACK_IMPORTED_MODULE_2__.captureStackTrace(e, _params?.callee);
        throw e;
    }
    return result.value;
};
const parse = /* @__PURE__*/ _parse(_errors_js__WEBPACK_IMPORTED_MODULE_1__.$ZodRealError);
const _parseAsync = (_Err) => async (schema, value, _ctx, params) => {
    const ctx = _ctx ? Object.assign(_ctx, { async: true }) : { async: true };
    let result = schema._zod.run({ value, issues: [] }, ctx);
    if (result instanceof Promise)
        result = await result;
    if (result.issues.length) {
        const e = new (params?.Err ?? _Err)(result.issues.map((iss) => _util_js__WEBPACK_IMPORTED_MODULE_2__.finalizeIssue(iss, ctx, _core_js__WEBPACK_IMPORTED_MODULE_0__.config())));
        _util_js__WEBPACK_IMPORTED_MODULE_2__.captureStackTrace(e, params?.callee);
        throw e;
    }
    return result.value;
};
const parseAsync = /* @__PURE__*/ _parseAsync(_errors_js__WEBPACK_IMPORTED_MODULE_1__.$ZodRealError);
const _safeParse = (_Err) => (schema, value, _ctx) => {
    const ctx = _ctx ? { ..._ctx, async: false } : { async: false };
    const result = schema._zod.run({ value, issues: [] }, ctx);
    if (result instanceof Promise) {
        throw new _core_js__WEBPACK_IMPORTED_MODULE_0__.$ZodAsyncError();
    }
    return result.issues.length
        ? {
            success: false,
            error: new (_Err ?? _errors_js__WEBPACK_IMPORTED_MODULE_1__.$ZodError)(result.issues.map((iss) => _util_js__WEBPACK_IMPORTED_MODULE_2__.finalizeIssue(iss, ctx, _core_js__WEBPACK_IMPORTED_MODULE_0__.config()))),
        }
        : { success: true, data: result.value };
};
const safeParse = /* @__PURE__*/ _safeParse(_errors_js__WEBPACK_IMPORTED_MODULE_1__.$ZodRealError);
const _safeParseAsync = (_Err) => async (schema, value, _ctx) => {
    const ctx = _ctx ? Object.assign(_ctx, { async: true }) : { async: true };
    let result = schema._zod.run({ value, issues: [] }, ctx);
    if (result instanceof Promise)
        result = await result;
    return result.issues.length
        ? {
            success: false,
            error: new _Err(result.issues.map((iss) => _util_js__WEBPACK_IMPORTED_MODULE_2__.finalizeIssue(iss, ctx, _core_js__WEBPACK_IMPORTED_MODULE_0__.config()))),
        }
        : { success: true, data: result.value };
};
const safeParseAsync = /* @__PURE__*/ _safeParseAsync(_errors_js__WEBPACK_IMPORTED_MODULE_1__.$ZodRealError);


/***/ }),

/***/ "./node_modules/zod/v4/core/regexes.js":
/*!*********************************************!*\
  !*** ./node_modules/zod/v4/core/regexes.js ***!
  \*********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   _emoji: function() { return /* binding */ _emoji; },
/* harmony export */   base64: function() { return /* binding */ base64; },
/* harmony export */   base64url: function() { return /* binding */ base64url; },
/* harmony export */   bigint: function() { return /* binding */ bigint; },
/* harmony export */   boolean: function() { return /* binding */ boolean; },
/* harmony export */   browserEmail: function() { return /* binding */ browserEmail; },
/* harmony export */   cidrv4: function() { return /* binding */ cidrv4; },
/* harmony export */   cidrv6: function() { return /* binding */ cidrv6; },
/* harmony export */   cuid: function() { return /* binding */ cuid; },
/* harmony export */   cuid2: function() { return /* binding */ cuid2; },
/* harmony export */   date: function() { return /* binding */ date; },
/* harmony export */   datetime: function() { return /* binding */ datetime; },
/* harmony export */   domain: function() { return /* binding */ domain; },
/* harmony export */   duration: function() { return /* binding */ duration; },
/* harmony export */   e164: function() { return /* binding */ e164; },
/* harmony export */   email: function() { return /* binding */ email; },
/* harmony export */   emoji: function() { return /* binding */ emoji; },
/* harmony export */   extendedDuration: function() { return /* binding */ extendedDuration; },
/* harmony export */   guid: function() { return /* binding */ guid; },
/* harmony export */   hostname: function() { return /* binding */ hostname; },
/* harmony export */   html5Email: function() { return /* binding */ html5Email; },
/* harmony export */   integer: function() { return /* binding */ integer; },
/* harmony export */   ipv4: function() { return /* binding */ ipv4; },
/* harmony export */   ipv6: function() { return /* binding */ ipv6; },
/* harmony export */   ksuid: function() { return /* binding */ ksuid; },
/* harmony export */   lowercase: function() { return /* binding */ lowercase; },
/* harmony export */   nanoid: function() { return /* binding */ nanoid; },
/* harmony export */   "null": function() { return /* binding */ _null; },
/* harmony export */   number: function() { return /* binding */ number; },
/* harmony export */   rfc5322Email: function() { return /* binding */ rfc5322Email; },
/* harmony export */   string: function() { return /* binding */ string; },
/* harmony export */   time: function() { return /* binding */ time; },
/* harmony export */   ulid: function() { return /* binding */ ulid; },
/* harmony export */   undefined: function() { return /* binding */ _undefined; },
/* harmony export */   unicodeEmail: function() { return /* binding */ unicodeEmail; },
/* harmony export */   uppercase: function() { return /* binding */ uppercase; },
/* harmony export */   uuid: function() { return /* binding */ uuid; },
/* harmony export */   uuid4: function() { return /* binding */ uuid4; },
/* harmony export */   uuid6: function() { return /* binding */ uuid6; },
/* harmony export */   uuid7: function() { return /* binding */ uuid7; },
/* harmony export */   xid: function() { return /* binding */ xid; }
/* harmony export */ });
const cuid = /^[cC][^\s-]{8,}$/;
const cuid2 = /^[0-9a-z]+$/;
const ulid = /^[0-9A-HJKMNP-TV-Za-hjkmnp-tv-z]{26}$/;
const xid = /^[0-9a-vA-V]{20}$/;
const ksuid = /^[A-Za-z0-9]{27}$/;
const nanoid = /^[a-zA-Z0-9_-]{21}$/;
/** ISO 8601-1 duration regex. Does not support the 8601-2 extensions like negative durations or fractional/negative components. */
const duration = /^P(?:(\d+W)|(?!.*W)(?=\d|T\d)(\d+Y)?(\d+M)?(\d+D)?(T(?=\d)(\d+H)?(\d+M)?(\d+([.,]\d+)?S)?)?)$/;
/** Implements ISO 8601-2 extensions like explicit +- prefixes, mixing weeks with other units, and fractional/negative components. */
const extendedDuration = /^[-+]?P(?!$)(?:(?:[-+]?\d+Y)|(?:[-+]?\d+[.,]\d+Y$))?(?:(?:[-+]?\d+M)|(?:[-+]?\d+[.,]\d+M$))?(?:(?:[-+]?\d+W)|(?:[-+]?\d+[.,]\d+W$))?(?:(?:[-+]?\d+D)|(?:[-+]?\d+[.,]\d+D$))?(?:T(?=[\d+-])(?:(?:[-+]?\d+H)|(?:[-+]?\d+[.,]\d+H$))?(?:(?:[-+]?\d+M)|(?:[-+]?\d+[.,]\d+M$))?(?:[-+]?\d+(?:[.,]\d+)?S)?)??$/;
/** A regex for any UUID-like identifier: 8-4-4-4-12 hex pattern */
const guid = /^([0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12})$/;
/** Returns a regex for validating an RFC 4122 UUID.
 *
 * @param version Optionally specify a version 1-8. If no version is specified, all versions are supported. */
const uuid = (version) => {
    if (!version)
        return /^([0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-8][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}|00000000-0000-0000-0000-000000000000)$/;
    return new RegExp(`^([0-9a-fA-F]{8}-[0-9a-fA-F]{4}-${version}[0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12})$`);
};
const uuid4 = /*@__PURE__*/ uuid(4);
const uuid6 = /*@__PURE__*/ uuid(6);
const uuid7 = /*@__PURE__*/ uuid(7);
/** Practical email validation */
const email = /^(?!\.)(?!.*\.\.)([A-Za-z0-9_'+\-\.]*)[A-Za-z0-9_+-]@([A-Za-z0-9][A-Za-z0-9\-]*\.)+[A-Za-z]{2,}$/;
/** Equivalent to the HTML5 input[type=email] validation implemented by browsers. Source: https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/email */
const html5Email = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
/** The classic emailregex.com regex for RFC 5322-compliant emails */
const rfc5322Email = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
/** A loose regex that allows Unicode characters, enforces length limits, and that's about it. */
const unicodeEmail = /^[^\s@"]{1,64}@[^\s@]{1,255}$/u;
const browserEmail = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
// from https://thekevinscott.com/emojis-in-javascript/#writing-a-regular-expression
const _emoji = `^(\\p{Extended_Pictographic}|\\p{Emoji_Component})+$`;
function emoji() {
    return new RegExp(_emoji, "u");
}
const ipv4 = /^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])$/;
const ipv6 = /^(([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|::|([0-9a-fA-F]{1,4})?::([0-9a-fA-F]{1,4}:?){0,6})$/;
const cidrv4 = /^((25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\.){3}(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])\/([0-9]|[1-2][0-9]|3[0-2])$/;
const cidrv6 = /^(([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}|::|([0-9a-fA-F]{1,4})?::([0-9a-fA-F]{1,4}:?){0,6})\/(12[0-8]|1[01][0-9]|[1-9]?[0-9])$/;
// https://stackoverflow.com/questions/7860392/determine-if-string-is-in-base64-using-javascript
const base64 = /^$|^(?:[0-9a-zA-Z+/]{4})*(?:(?:[0-9a-zA-Z+/]{2}==)|(?:[0-9a-zA-Z+/]{3}=))?$/;
const base64url = /^[A-Za-z0-9_-]*$/;
// based on https://stackoverflow.com/questions/106179/regular-expression-to-match-dns-hostname-or-ip-address
// export const hostname: RegExp =
//   /^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)+([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/;
const hostname = /^([a-zA-Z0-9-]+\.)*[a-zA-Z0-9-]+$/;
const domain = /^([a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,}$/;
// https://blog.stevenlevithan.com/archives/validate-phone-number#r4-3 (regex sans spaces)
const e164 = /^\+(?:[0-9]){6,14}[0-9]$/;
// const dateSource = `((\\d\\d[2468][048]|\\d\\d[13579][26]|\\d\\d0[48]|[02468][048]00|[13579][26]00)-02-29|\\d{4}-((0[13578]|1[02])-(0[1-9]|[12]\\d|3[01])|(0[469]|11)-(0[1-9]|[12]\\d|30)|(02)-(0[1-9]|1\\d|2[0-8])))`;
const dateSource = `(?:(?:\\d\\d[2468][048]|\\d\\d[13579][26]|\\d\\d0[48]|[02468][048]00|[13579][26]00)-02-29|\\d{4}-(?:(?:0[13578]|1[02])-(?:0[1-9]|[12]\\d|3[01])|(?:0[469]|11)-(?:0[1-9]|[12]\\d|30)|(?:02)-(?:0[1-9]|1\\d|2[0-8])))`;
const date = /*@__PURE__*/ new RegExp(`^${dateSource}$`);
function timeSource(args) {
    const hhmm = `(?:[01]\\d|2[0-3]):[0-5]\\d`;
    const regex = typeof args.precision === "number"
        ? args.precision === -1
            ? `${hhmm}`
            : args.precision === 0
                ? `${hhmm}:[0-5]\\d`
                : `${hhmm}:[0-5]\\d\\.\\d{${args.precision}}`
        : `${hhmm}(?::[0-5]\\d(?:\\.\\d+)?)?`;
    return regex;
}
function time(args) {
    return new RegExp(`^${timeSource(args)}$`);
}
// Adapted from https://stackoverflow.com/a/3143231
function datetime(args) {
    const time = timeSource({ precision: args.precision });
    const opts = ["Z"];
    if (args.local)
        opts.push("");
    if (args.offset)
        opts.push(`([+-]\\d{2}:\\d{2})`);
    const timeRegex = `${time}(?:${opts.join("|")})`;
    return new RegExp(`^${dateSource}T(?:${timeRegex})$`);
}
const string = (params) => {
    const regex = params ? `[\\s\\S]{${params?.minimum ?? 0},${params?.maximum ?? ""}}` : `[\\s\\S]*`;
    return new RegExp(`^${regex}$`);
};
const bigint = /^\d+n?$/;
const integer = /^\d+$/;
const number = /^-?\d+(?:\.\d+)?/i;
const boolean = /true|false/i;
const _null = /null/i;

const _undefined = /undefined/i;

// regex for string with no uppercase letters
const lowercase = /^[^A-Z]*$/;
// regex for string with no lowercase letters
const uppercase = /^[^a-z]*$/;


/***/ }),

/***/ "./node_modules/zod/v4/core/registries.js":
/*!************************************************!*\
  !*** ./node_modules/zod/v4/core/registries.js ***!
  \************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   $ZodRegistry: function() { return /* binding */ $ZodRegistry; },
/* harmony export */   $input: function() { return /* binding */ $input; },
/* harmony export */   $output: function() { return /* binding */ $output; },
/* harmony export */   globalRegistry: function() { return /* binding */ globalRegistry; },
/* harmony export */   registry: function() { return /* binding */ registry; }
/* harmony export */ });
const $output = Symbol("ZodOutput");
const $input = Symbol("ZodInput");
class $ZodRegistry {
    constructor() {
        this._map = new Map();
        this._idmap = new Map();
    }
    add(schema, ..._meta) {
        const meta = _meta[0];
        this._map.set(schema, meta);
        if (meta && typeof meta === "object" && "id" in meta) {
            if (this._idmap.has(meta.id)) {
                throw new Error(`ID ${meta.id} already exists in the registry`);
            }
            this._idmap.set(meta.id, schema);
        }
        return this;
    }
    clear() {
        this._map = new Map();
        this._idmap = new Map();
        return this;
    }
    remove(schema) {
        const meta = this._map.get(schema);
        if (meta && typeof meta === "object" && "id" in meta) {
            this._idmap.delete(meta.id);
        }
        this._map.delete(schema);
        return this;
    }
    get(schema) {
        // return this._map.get(schema) as any;
        // inherit metadata
        const p = schema._zod.parent;
        if (p) {
            const pm = { ...(this.get(p) ?? {}) };
            delete pm.id; // do not inherit id
            return { ...pm, ...this._map.get(schema) };
        }
        return this._map.get(schema);
    }
    has(schema) {
        return this._map.has(schema);
    }
}
// registries
function registry() {
    return new $ZodRegistry();
}
const globalRegistry = /*@__PURE__*/ registry();


/***/ }),

/***/ "./node_modules/zod/v4/core/schemas.js":
/*!*********************************************!*\
  !*** ./node_modules/zod/v4/core/schemas.js ***!
  \*********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   $ZodAny: function() { return /* binding */ $ZodAny; },
/* harmony export */   $ZodArray: function() { return /* binding */ $ZodArray; },
/* harmony export */   $ZodBase64: function() { return /* binding */ $ZodBase64; },
/* harmony export */   $ZodBase64URL: function() { return /* binding */ $ZodBase64URL; },
/* harmony export */   $ZodBigInt: function() { return /* binding */ $ZodBigInt; },
/* harmony export */   $ZodBigIntFormat: function() { return /* binding */ $ZodBigIntFormat; },
/* harmony export */   $ZodBoolean: function() { return /* binding */ $ZodBoolean; },
/* harmony export */   $ZodCIDRv4: function() { return /* binding */ $ZodCIDRv4; },
/* harmony export */   $ZodCIDRv6: function() { return /* binding */ $ZodCIDRv6; },
/* harmony export */   $ZodCUID: function() { return /* binding */ $ZodCUID; },
/* harmony export */   $ZodCUID2: function() { return /* binding */ $ZodCUID2; },
/* harmony export */   $ZodCatch: function() { return /* binding */ $ZodCatch; },
/* harmony export */   $ZodCustom: function() { return /* binding */ $ZodCustom; },
/* harmony export */   $ZodCustomStringFormat: function() { return /* binding */ $ZodCustomStringFormat; },
/* harmony export */   $ZodDate: function() { return /* binding */ $ZodDate; },
/* harmony export */   $ZodDefault: function() { return /* binding */ $ZodDefault; },
/* harmony export */   $ZodDiscriminatedUnion: function() { return /* binding */ $ZodDiscriminatedUnion; },
/* harmony export */   $ZodE164: function() { return /* binding */ $ZodE164; },
/* harmony export */   $ZodEmail: function() { return /* binding */ $ZodEmail; },
/* harmony export */   $ZodEmoji: function() { return /* binding */ $ZodEmoji; },
/* harmony export */   $ZodEnum: function() { return /* binding */ $ZodEnum; },
/* harmony export */   $ZodFile: function() { return /* binding */ $ZodFile; },
/* harmony export */   $ZodGUID: function() { return /* binding */ $ZodGUID; },
/* harmony export */   $ZodIPv4: function() { return /* binding */ $ZodIPv4; },
/* harmony export */   $ZodIPv6: function() { return /* binding */ $ZodIPv6; },
/* harmony export */   $ZodISODate: function() { return /* binding */ $ZodISODate; },
/* harmony export */   $ZodISODateTime: function() { return /* binding */ $ZodISODateTime; },
/* harmony export */   $ZodISODuration: function() { return /* binding */ $ZodISODuration; },
/* harmony export */   $ZodISOTime: function() { return /* binding */ $ZodISOTime; },
/* harmony export */   $ZodIntersection: function() { return /* binding */ $ZodIntersection; },
/* harmony export */   $ZodJWT: function() { return /* binding */ $ZodJWT; },
/* harmony export */   $ZodKSUID: function() { return /* binding */ $ZodKSUID; },
/* harmony export */   $ZodLazy: function() { return /* binding */ $ZodLazy; },
/* harmony export */   $ZodLiteral: function() { return /* binding */ $ZodLiteral; },
/* harmony export */   $ZodMap: function() { return /* binding */ $ZodMap; },
/* harmony export */   $ZodNaN: function() { return /* binding */ $ZodNaN; },
/* harmony export */   $ZodNanoID: function() { return /* binding */ $ZodNanoID; },
/* harmony export */   $ZodNever: function() { return /* binding */ $ZodNever; },
/* harmony export */   $ZodNonOptional: function() { return /* binding */ $ZodNonOptional; },
/* harmony export */   $ZodNull: function() { return /* binding */ $ZodNull; },
/* harmony export */   $ZodNullable: function() { return /* binding */ $ZodNullable; },
/* harmony export */   $ZodNumber: function() { return /* binding */ $ZodNumber; },
/* harmony export */   $ZodNumberFormat: function() { return /* binding */ $ZodNumberFormat; },
/* harmony export */   $ZodObject: function() { return /* binding */ $ZodObject; },
/* harmony export */   $ZodOptional: function() { return /* binding */ $ZodOptional; },
/* harmony export */   $ZodPipe: function() { return /* binding */ $ZodPipe; },
/* harmony export */   $ZodPrefault: function() { return /* binding */ $ZodPrefault; },
/* harmony export */   $ZodPromise: function() { return /* binding */ $ZodPromise; },
/* harmony export */   $ZodReadonly: function() { return /* binding */ $ZodReadonly; },
/* harmony export */   $ZodRecord: function() { return /* binding */ $ZodRecord; },
/* harmony export */   $ZodSet: function() { return /* binding */ $ZodSet; },
/* harmony export */   $ZodString: function() { return /* binding */ $ZodString; },
/* harmony export */   $ZodStringFormat: function() { return /* binding */ $ZodStringFormat; },
/* harmony export */   $ZodSuccess: function() { return /* binding */ $ZodSuccess; },
/* harmony export */   $ZodSymbol: function() { return /* binding */ $ZodSymbol; },
/* harmony export */   $ZodTemplateLiteral: function() { return /* binding */ $ZodTemplateLiteral; },
/* harmony export */   $ZodTransform: function() { return /* binding */ $ZodTransform; },
/* harmony export */   $ZodTuple: function() { return /* binding */ $ZodTuple; },
/* harmony export */   $ZodType: function() { return /* binding */ $ZodType; },
/* harmony export */   $ZodULID: function() { return /* binding */ $ZodULID; },
/* harmony export */   $ZodURL: function() { return /* binding */ $ZodURL; },
/* harmony export */   $ZodUUID: function() { return /* binding */ $ZodUUID; },
/* harmony export */   $ZodUndefined: function() { return /* binding */ $ZodUndefined; },
/* harmony export */   $ZodUnion: function() { return /* binding */ $ZodUnion; },
/* harmony export */   $ZodUnknown: function() { return /* binding */ $ZodUnknown; },
/* harmony export */   $ZodVoid: function() { return /* binding */ $ZodVoid; },
/* harmony export */   $ZodXID: function() { return /* binding */ $ZodXID; },
/* harmony export */   clone: function() { return /* reexport safe */ _util_js__WEBPACK_IMPORTED_MODULE_5__.clone; },
/* harmony export */   isValidBase64: function() { return /* binding */ isValidBase64; },
/* harmony export */   isValidBase64URL: function() { return /* binding */ isValidBase64URL; },
/* harmony export */   isValidJWT: function() { return /* binding */ isValidJWT; }
/* harmony export */ });
/* harmony import */ var _checks_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./checks.js */ "./node_modules/zod/v4/core/checks.js");
/* harmony import */ var _core_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./core.js */ "./node_modules/zod/v4/core/core.js");
/* harmony import */ var _doc_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./doc.js */ "./node_modules/zod/v4/core/doc.js");
/* harmony import */ var _parse_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./parse.js */ "./node_modules/zod/v4/core/parse.js");
/* harmony import */ var _regexes_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./regexes.js */ "./node_modules/zod/v4/core/regexes.js");
/* harmony import */ var _util_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./util.js */ "./node_modules/zod/v4/core/util.js");
/* harmony import */ var _versions_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./versions.js */ "./node_modules/zod/v4/core/versions.js");







const $ZodType = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodType", (inst, def) => {
    var _a;
    inst ?? (inst = {});
    inst._zod.def = def; // set _def property
    inst._zod.bag = inst._zod.bag || {}; // initialize _bag object
    inst._zod.version = _versions_js__WEBPACK_IMPORTED_MODULE_6__.version;
    const checks = [...(inst._zod.def.checks ?? [])];
    // if inst is itself a checks.$ZodCheck, run it as a check
    if (inst._zod.traits.has("$ZodCheck")) {
        checks.unshift(inst);
    }
    //
    for (const ch of checks) {
        for (const fn of ch._zod.onattach) {
            fn(inst);
        }
    }
    if (checks.length === 0) {
        // deferred initializer
        // inst._zod.parse is not yet defined
        (_a = inst._zod).deferred ?? (_a.deferred = []);
        inst._zod.deferred?.push(() => {
            inst._zod.run = inst._zod.parse;
        });
    }
    else {
        const runChecks = (payload, checks, ctx) => {
            let isAborted = _util_js__WEBPACK_IMPORTED_MODULE_5__.aborted(payload);
            let asyncResult;
            for (const ch of checks) {
                if (ch._zod.def.when) {
                    const shouldRun = ch._zod.def.when(payload);
                    if (!shouldRun)
                        continue;
                }
                else if (isAborted) {
                    continue;
                }
                const currLen = payload.issues.length;
                const _ = ch._zod.check(payload);
                if (_ instanceof Promise && ctx?.async === false) {
                    throw new _core_js__WEBPACK_IMPORTED_MODULE_1__.$ZodAsyncError();
                }
                if (asyncResult || _ instanceof Promise) {
                    asyncResult = (asyncResult ?? Promise.resolve()).then(async () => {
                        await _;
                        const nextLen = payload.issues.length;
                        if (nextLen === currLen)
                            return;
                        if (!isAborted)
                            isAborted = _util_js__WEBPACK_IMPORTED_MODULE_5__.aborted(payload, currLen);
                    });
                }
                else {
                    const nextLen = payload.issues.length;
                    if (nextLen === currLen)
                        continue;
                    if (!isAborted)
                        isAborted = _util_js__WEBPACK_IMPORTED_MODULE_5__.aborted(payload, currLen);
                }
            }
            if (asyncResult) {
                return asyncResult.then(() => {
                    return payload;
                });
            }
            return payload;
        };
        inst._zod.run = (payload, ctx) => {
            const result = inst._zod.parse(payload, ctx);
            if (result instanceof Promise) {
                if (ctx.async === false)
                    throw new _core_js__WEBPACK_IMPORTED_MODULE_1__.$ZodAsyncError();
                return result.then((result) => runChecks(result, checks, ctx));
            }
            return runChecks(result, checks, ctx);
        };
    }
    inst["~standard"] = {
        validate: (value) => {
            try {
                const r = (0,_parse_js__WEBPACK_IMPORTED_MODULE_3__.safeParse)(inst, value);
                return r.success ? { value: r.data } : { issues: r.error?.issues };
            }
            catch (_) {
                return (0,_parse_js__WEBPACK_IMPORTED_MODULE_3__.safeParseAsync)(inst, value).then((r) => (r.success ? { value: r.data } : { issues: r.error?.issues }));
            }
        },
        vendor: "zod",
        version: 1,
    };
});

const $ZodString = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodString", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.pattern = [...(inst?._zod.bag?.patterns ?? [])].pop() ?? _regexes_js__WEBPACK_IMPORTED_MODULE_4__.string(inst._zod.bag);
    inst._zod.parse = (payload, _) => {
        if (def.coerce)
            try {
                payload.value = String(payload.value);
            }
            catch (_) { }
        if (typeof payload.value === "string")
            return payload;
        payload.issues.push({
            expected: "string",
            code: "invalid_type",
            input: payload.value,
            inst,
        });
        return payload;
    };
});
const $ZodStringFormat = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodStringFormat", (inst, def) => {
    // check initialization must come first
    _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckStringFormat.init(inst, def);
    $ZodString.init(inst, def);
});
const $ZodGUID = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodGUID", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.guid);
    $ZodStringFormat.init(inst, def);
});
const $ZodUUID = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodUUID", (inst, def) => {
    if (def.version) {
        const versionMap = {
            v1: 1,
            v2: 2,
            v3: 3,
            v4: 4,
            v5: 5,
            v6: 6,
            v7: 7,
            v8: 8,
        };
        const v = versionMap[def.version];
        if (v === undefined)
            throw new Error(`Invalid UUID version: "${def.version}"`);
        def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.uuid(v));
    }
    else
        def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.uuid());
    $ZodStringFormat.init(inst, def);
});
const $ZodEmail = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodEmail", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.email);
    $ZodStringFormat.init(inst, def);
});
const $ZodURL = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodURL", (inst, def) => {
    $ZodStringFormat.init(inst, def);
    inst._zod.check = (payload) => {
        try {
            const orig = payload.value;
            const url = new URL(orig);
            const href = url.href;
            if (def.hostname) {
                def.hostname.lastIndex = 0;
                if (!def.hostname.test(url.hostname)) {
                    payload.issues.push({
                        code: "invalid_format",
                        format: "url",
                        note: "Invalid hostname",
                        pattern: _regexes_js__WEBPACK_IMPORTED_MODULE_4__.hostname.source,
                        input: payload.value,
                        inst,
                        continue: !def.abort,
                    });
                }
            }
            if (def.protocol) {
                def.protocol.lastIndex = 0;
                if (!def.protocol.test(url.protocol.endsWith(":") ? url.protocol.slice(0, -1) : url.protocol)) {
                    payload.issues.push({
                        code: "invalid_format",
                        format: "url",
                        note: "Invalid protocol",
                        pattern: def.protocol.source,
                        input: payload.value,
                        inst,
                        continue: !def.abort,
                    });
                }
            }
            // payload.value = url.href;
            if (!orig.endsWith("/") && href.endsWith("/")) {
                payload.value = href.slice(0, -1);
            }
            else {
                payload.value = href;
            }
            return;
        }
        catch (_) {
            payload.issues.push({
                code: "invalid_format",
                format: "url",
                input: payload.value,
                inst,
                continue: !def.abort,
            });
        }
    };
});
const $ZodEmoji = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodEmoji", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.emoji());
    $ZodStringFormat.init(inst, def);
});
const $ZodNanoID = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodNanoID", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.nanoid);
    $ZodStringFormat.init(inst, def);
});
const $ZodCUID = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodCUID", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.cuid);
    $ZodStringFormat.init(inst, def);
});
const $ZodCUID2 = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodCUID2", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.cuid2);
    $ZodStringFormat.init(inst, def);
});
const $ZodULID = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodULID", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.ulid);
    $ZodStringFormat.init(inst, def);
});
const $ZodXID = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodXID", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.xid);
    $ZodStringFormat.init(inst, def);
});
const $ZodKSUID = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodKSUID", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.ksuid);
    $ZodStringFormat.init(inst, def);
});
const $ZodISODateTime = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodISODateTime", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.datetime(def));
    $ZodStringFormat.init(inst, def);
});
const $ZodISODate = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodISODate", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.date);
    $ZodStringFormat.init(inst, def);
});
const $ZodISOTime = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodISOTime", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.time(def));
    $ZodStringFormat.init(inst, def);
});
const $ZodISODuration = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodISODuration", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.duration);
    $ZodStringFormat.init(inst, def);
});
const $ZodIPv4 = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodIPv4", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.ipv4);
    $ZodStringFormat.init(inst, def);
    inst._zod.onattach.push((inst) => {
        const bag = inst._zod.bag;
        bag.format = `ipv4`;
    });
});
const $ZodIPv6 = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodIPv6", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.ipv6);
    $ZodStringFormat.init(inst, def);
    inst._zod.onattach.push((inst) => {
        const bag = inst._zod.bag;
        bag.format = `ipv6`;
    });
    inst._zod.check = (payload) => {
        try {
            new URL(`http://[${payload.value}]`);
            // return;
        }
        catch {
            payload.issues.push({
                code: "invalid_format",
                format: "ipv6",
                input: payload.value,
                inst,
                continue: !def.abort,
            });
        }
    };
});
const $ZodCIDRv4 = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodCIDRv4", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.cidrv4);
    $ZodStringFormat.init(inst, def);
});
const $ZodCIDRv6 = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodCIDRv6", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.cidrv6); // not used for validation
    $ZodStringFormat.init(inst, def);
    inst._zod.check = (payload) => {
        const [address, prefix] = payload.value.split("/");
        try {
            if (!prefix)
                throw new Error();
            const prefixNum = Number(prefix);
            if (`${prefixNum}` !== prefix)
                throw new Error();
            if (prefixNum < 0 || prefixNum > 128)
                throw new Error();
            new URL(`http://[${address}]`);
        }
        catch {
            payload.issues.push({
                code: "invalid_format",
                format: "cidrv6",
                input: payload.value,
                inst,
                continue: !def.abort,
            });
        }
    };
});
//////////////////////////////   ZodBase64   //////////////////////////////
function isValidBase64(data) {
    if (data === "")
        return true;
    if (data.length % 4 !== 0)
        return false;
    try {
        atob(data);
        return true;
    }
    catch {
        return false;
    }
}
const $ZodBase64 = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodBase64", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.base64);
    $ZodStringFormat.init(inst, def);
    inst._zod.onattach.push((inst) => {
        inst._zod.bag.contentEncoding = "base64";
    });
    inst._zod.check = (payload) => {
        if (isValidBase64(payload.value))
            return;
        payload.issues.push({
            code: "invalid_format",
            format: "base64",
            input: payload.value,
            inst,
            continue: !def.abort,
        });
    };
});
//////////////////////////////   ZodBase64   //////////////////////////////
function isValidBase64URL(data) {
    if (!_regexes_js__WEBPACK_IMPORTED_MODULE_4__.base64url.test(data))
        return false;
    const base64 = data.replace(/[-_]/g, (c) => (c === "-" ? "+" : "/"));
    const padded = base64.padEnd(Math.ceil(base64.length / 4) * 4, "=");
    return isValidBase64(padded);
}
const $ZodBase64URL = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodBase64URL", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.base64url);
    $ZodStringFormat.init(inst, def);
    inst._zod.onattach.push((inst) => {
        inst._zod.bag.contentEncoding = "base64url";
    });
    inst._zod.check = (payload) => {
        if (isValidBase64URL(payload.value))
            return;
        payload.issues.push({
            code: "invalid_format",
            format: "base64url",
            input: payload.value,
            inst,
            continue: !def.abort,
        });
    };
});
const $ZodE164 = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodE164", (inst, def) => {
    def.pattern ?? (def.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.e164);
    $ZodStringFormat.init(inst, def);
});
//////////////////////////////   ZodJWT   //////////////////////////////
function isValidJWT(token, algorithm = null) {
    try {
        const tokensParts = token.split(".");
        if (tokensParts.length !== 3)
            return false;
        const [header] = tokensParts;
        if (!header)
            return false;
        const parsedHeader = JSON.parse(atob(header));
        if ("typ" in parsedHeader && parsedHeader?.typ !== "JWT")
            return false;
        if (!parsedHeader.alg)
            return false;
        if (algorithm && (!("alg" in parsedHeader) || parsedHeader.alg !== algorithm))
            return false;
        return true;
    }
    catch {
        return false;
    }
}
const $ZodJWT = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodJWT", (inst, def) => {
    $ZodStringFormat.init(inst, def);
    inst._zod.check = (payload) => {
        if (isValidJWT(payload.value, def.alg))
            return;
        payload.issues.push({
            code: "invalid_format",
            format: "jwt",
            input: payload.value,
            inst,
            continue: !def.abort,
        });
    };
});
const $ZodCustomStringFormat = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodCustomStringFormat", (inst, def) => {
    $ZodStringFormat.init(inst, def);
    inst._zod.check = (payload) => {
        if (def.fn(payload.value))
            return;
        payload.issues.push({
            code: "invalid_format",
            format: def.format,
            input: payload.value,
            inst,
            continue: !def.abort,
        });
    };
});
const $ZodNumber = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodNumber", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.pattern = inst._zod.bag.pattern ?? _regexes_js__WEBPACK_IMPORTED_MODULE_4__.number;
    inst._zod.parse = (payload, _ctx) => {
        if (def.coerce)
            try {
                payload.value = Number(payload.value);
            }
            catch (_) { }
        const input = payload.value;
        if (typeof input === "number" && !Number.isNaN(input) && Number.isFinite(input)) {
            return payload;
        }
        const received = typeof input === "number"
            ? Number.isNaN(input)
                ? "NaN"
                : !Number.isFinite(input)
                    ? "Infinity"
                    : undefined
            : undefined;
        payload.issues.push({
            expected: "number",
            code: "invalid_type",
            input,
            inst,
            ...(received ? { received } : {}),
        });
        return payload;
    };
});
const $ZodNumberFormat = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodNumber", (inst, def) => {
    _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckNumberFormat.init(inst, def);
    $ZodNumber.init(inst, def); // no format checksp
});
const $ZodBoolean = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodBoolean", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.boolean;
    inst._zod.parse = (payload, _ctx) => {
        if (def.coerce)
            try {
                payload.value = Boolean(payload.value);
            }
            catch (_) { }
        const input = payload.value;
        if (typeof input === "boolean")
            return payload;
        payload.issues.push({
            expected: "boolean",
            code: "invalid_type",
            input,
            inst,
        });
        return payload;
    };
});
const $ZodBigInt = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodBigInt", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.bigint;
    inst._zod.parse = (payload, _ctx) => {
        if (def.coerce)
            try {
                payload.value = BigInt(payload.value);
            }
            catch (_) { }
        if (typeof payload.value === "bigint")
            return payload;
        payload.issues.push({
            expected: "bigint",
            code: "invalid_type",
            input: payload.value,
            inst,
        });
        return payload;
    };
});
const $ZodBigIntFormat = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodBigInt", (inst, def) => {
    _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheckBigIntFormat.init(inst, def);
    $ZodBigInt.init(inst, def); // no format checks
});
const $ZodSymbol = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodSymbol", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.parse = (payload, _ctx) => {
        const input = payload.value;
        if (typeof input === "symbol")
            return payload;
        payload.issues.push({
            expected: "symbol",
            code: "invalid_type",
            input,
            inst,
        });
        return payload;
    };
});
const $ZodUndefined = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodUndefined", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__.undefined;
    inst._zod.values = new Set([undefined]);
    inst._zod.optin = "optional";
    inst._zod.optout = "optional";
    inst._zod.parse = (payload, _ctx) => {
        const input = payload.value;
        if (typeof input === "undefined")
            return payload;
        payload.issues.push({
            expected: "undefined",
            code: "invalid_type",
            input,
            inst,
        });
        return payload;
    };
});
const $ZodNull = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodNull", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.pattern = _regexes_js__WEBPACK_IMPORTED_MODULE_4__["null"];
    inst._zod.values = new Set([null]);
    inst._zod.parse = (payload, _ctx) => {
        const input = payload.value;
        if (input === null)
            return payload;
        payload.issues.push({
            expected: "null",
            code: "invalid_type",
            input,
            inst,
        });
        return payload;
    };
});
const $ZodAny = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodAny", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.parse = (payload) => payload;
});
const $ZodUnknown = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodUnknown", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.parse = (payload) => payload;
});
const $ZodNever = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodNever", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.parse = (payload, _ctx) => {
        payload.issues.push({
            expected: "never",
            code: "invalid_type",
            input: payload.value,
            inst,
        });
        return payload;
    };
});
const $ZodVoid = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodVoid", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.parse = (payload, _ctx) => {
        const input = payload.value;
        if (typeof input === "undefined")
            return payload;
        payload.issues.push({
            expected: "void",
            code: "invalid_type",
            input,
            inst,
        });
        return payload;
    };
});
const $ZodDate = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodDate", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.parse = (payload, _ctx) => {
        if (def.coerce) {
            try {
                payload.value = new Date(payload.value);
            }
            catch (_err) { }
        }
        const input = payload.value;
        const isDate = input instanceof Date;
        const isValidDate = isDate && !Number.isNaN(input.getTime());
        if (isValidDate)
            return payload;
        payload.issues.push({
            expected: "date",
            code: "invalid_type",
            input,
            ...(isDate ? { received: "Invalid Date" } : {}),
            inst,
        });
        return payload;
    };
});
function handleArrayResult(result, final, index) {
    if (result.issues.length) {
        final.issues.push(..._util_js__WEBPACK_IMPORTED_MODULE_5__.prefixIssues(index, result.issues));
    }
    final.value[index] = result.value;
}
const $ZodArray = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodArray", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.parse = (payload, ctx) => {
        const input = payload.value;
        if (!Array.isArray(input)) {
            payload.issues.push({
                expected: "array",
                code: "invalid_type",
                input,
                inst,
            });
            return payload;
        }
        payload.value = Array(input.length);
        const proms = [];
        for (let i = 0; i < input.length; i++) {
            const item = input[i];
            const result = def.element._zod.run({
                value: item,
                issues: [],
            }, ctx);
            if (result instanceof Promise) {
                proms.push(result.then((result) => handleArrayResult(result, payload, i)));
            }
            else {
                handleArrayResult(result, payload, i);
            }
        }
        if (proms.length) {
            return Promise.all(proms).then(() => payload);
        }
        return payload; //handleArrayResultsAsync(parseResults, final);
    };
});
function handleObjectResult(result, final, key) {
    // if(isOptional)
    if (result.issues.length) {
        final.issues.push(..._util_js__WEBPACK_IMPORTED_MODULE_5__.prefixIssues(key, result.issues));
    }
    final.value[key] = result.value;
}
function handleOptionalObjectResult(result, final, key, input) {
    if (result.issues.length) {
        // validation failed against value schema
        if (input[key] === undefined) {
            // if input was undefined, ignore the error
            if (key in input) {
                final.value[key] = undefined;
            }
            else {
                final.value[key] = result.value;
            }
        }
        else {
            final.issues.push(..._util_js__WEBPACK_IMPORTED_MODULE_5__.prefixIssues(key, result.issues));
        }
    }
    else if (result.value === undefined) {
        // validation returned `undefined`
        if (key in input)
            final.value[key] = undefined;
    }
    else {
        // non-undefined value
        final.value[key] = result.value;
    }
}
const $ZodObject = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodObject", (inst, def) => {
    // requires cast because technically $ZodObject doesn't extend
    $ZodType.init(inst, def);
    const _normalized = _util_js__WEBPACK_IMPORTED_MODULE_5__.cached(() => {
        const keys = Object.keys(def.shape);
        for (const k of keys) {
            if (!(def.shape[k] instanceof $ZodType)) {
                throw new Error(`Invalid element at key "${k}": expected a Zod schema`);
            }
        }
        const okeys = _util_js__WEBPACK_IMPORTED_MODULE_5__.optionalKeys(def.shape);
        return {
            shape: def.shape,
            keys,
            keySet: new Set(keys),
            numKeys: keys.length,
            optionalKeys: new Set(okeys),
        };
    });
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "propValues", () => {
        const shape = def.shape;
        const propValues = {};
        for (const key in shape) {
            const field = shape[key]._zod;
            if (field.values) {
                propValues[key] ?? (propValues[key] = new Set());
                for (const v of field.values)
                    propValues[key].add(v);
            }
        }
        return propValues;
    });
    const generateFastpass = (shape) => {
        const doc = new _doc_js__WEBPACK_IMPORTED_MODULE_2__.Doc(["shape", "payload", "ctx"]);
        const normalized = _normalized.value;
        const parseStr = (key) => {
            const k = _util_js__WEBPACK_IMPORTED_MODULE_5__.esc(key);
            return `shape[${k}]._zod.run({ value: input[${k}], issues: [] }, ctx)`;
        };
        doc.write(`const input = payload.value;`);
        const ids = Object.create(null);
        let counter = 0;
        for (const key of normalized.keys) {
            ids[key] = `key_${counter++}`;
        }
        // A: preserve key order {
        doc.write(`const newResult = {}`);
        for (const key of normalized.keys) {
            if (normalized.optionalKeys.has(key)) {
                const id = ids[key];
                doc.write(`const ${id} = ${parseStr(key)};`);
                const k = _util_js__WEBPACK_IMPORTED_MODULE_5__.esc(key);
                doc.write(`
        if (${id}.issues.length) {
          if (input[${k}] === undefined) {
            if (${k} in input) {
              newResult[${k}] = undefined;
            }
          } else {
            payload.issues = payload.issues.concat(
              ${id}.issues.map((iss) => ({
                ...iss,
                path: iss.path ? [${k}, ...iss.path] : [${k}],
              }))
            );
          }
        } else if (${id}.value === undefined) {
          if (${k} in input) newResult[${k}] = undefined;
        } else {
          newResult[${k}] = ${id}.value;
        }
        `);
            }
            else {
                const id = ids[key];
                //  const id = ids[key];
                doc.write(`const ${id} = ${parseStr(key)};`);
                doc.write(`
          if (${id}.issues.length) payload.issues = payload.issues.concat(${id}.issues.map(iss => ({
            ...iss,
            path: iss.path ? [${_util_js__WEBPACK_IMPORTED_MODULE_5__.esc(key)}, ...iss.path] : [${_util_js__WEBPACK_IMPORTED_MODULE_5__.esc(key)}]
          })));`);
                doc.write(`newResult[${_util_js__WEBPACK_IMPORTED_MODULE_5__.esc(key)}] = ${id}.value`);
            }
        }
        doc.write(`payload.value = newResult;`);
        doc.write(`return payload;`);
        const fn = doc.compile();
        return (payload, ctx) => fn(shape, payload, ctx);
    };
    let fastpass;
    const isObject = _util_js__WEBPACK_IMPORTED_MODULE_5__.isObject;
    const jit = !_core_js__WEBPACK_IMPORTED_MODULE_1__.globalConfig.jitless;
    const allowsEval = _util_js__WEBPACK_IMPORTED_MODULE_5__.allowsEval;
    const fastEnabled = jit && allowsEval.value; // && !def.catchall;
    const catchall = def.catchall;
    let value;
    inst._zod.parse = (payload, ctx) => {
        value ?? (value = _normalized.value);
        const input = payload.value;
        if (!isObject(input)) {
            payload.issues.push({
                expected: "object",
                code: "invalid_type",
                input,
                inst,
            });
            return payload;
        }
        const proms = [];
        if (jit && fastEnabled && ctx?.async === false && ctx.jitless !== true) {
            // always synchronous
            if (!fastpass)
                fastpass = generateFastpass(def.shape);
            payload = fastpass(payload, ctx);
        }
        else {
            payload.value = {};
            const shape = value.shape;
            for (const key of value.keys) {
                const el = shape[key];
                // do not add omitted optional keys
                // if (!(key in input)) {
                //   if (optionalKeys.has(key)) continue;
                //   payload.issues.push({
                //     code: "invalid_type",
                //     path: [key],
                //     expected: "nonoptional",
                //     note: `Missing required key: "${key}"`,
                //     input,
                //     inst,
                //   });
                // }
                const r = el._zod.run({ value: input[key], issues: [] }, ctx);
                const isOptional = el._zod.optin === "optional" && el._zod.optout === "optional";
                if (r instanceof Promise) {
                    proms.push(r.then((r) => isOptional ? handleOptionalObjectResult(r, payload, key, input) : handleObjectResult(r, payload, key)));
                }
                else if (isOptional) {
                    handleOptionalObjectResult(r, payload, key, input);
                }
                else {
                    handleObjectResult(r, payload, key);
                }
            }
        }
        if (!catchall) {
            // return payload;
            return proms.length ? Promise.all(proms).then(() => payload) : payload;
        }
        const unrecognized = [];
        // iterate over input keys
        const keySet = value.keySet;
        const _catchall = catchall._zod;
        const t = _catchall.def.type;
        for (const key of Object.keys(input)) {
            if (keySet.has(key))
                continue;
            if (t === "never") {
                unrecognized.push(key);
                continue;
            }
            const r = _catchall.run({ value: input[key], issues: [] }, ctx);
            if (r instanceof Promise) {
                proms.push(r.then((r) => handleObjectResult(r, payload, key)));
            }
            else {
                handleObjectResult(r, payload, key);
            }
        }
        if (unrecognized.length) {
            payload.issues.push({
                code: "unrecognized_keys",
                keys: unrecognized,
                input,
                inst,
            });
        }
        if (!proms.length)
            return payload;
        return Promise.all(proms).then(() => {
            return payload;
        });
    };
});
function handleUnionResults(results, final, inst, ctx) {
    for (const result of results) {
        if (result.issues.length === 0) {
            final.value = result.value;
            return final;
        }
    }
    final.issues.push({
        code: "invalid_union",
        input: final.value,
        inst,
        errors: results.map((result) => result.issues.map((iss) => _util_js__WEBPACK_IMPORTED_MODULE_5__.finalizeIssue(iss, ctx, _core_js__WEBPACK_IMPORTED_MODULE_1__.config()))),
    });
    return final;
}
const $ZodUnion = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodUnion", (inst, def) => {
    $ZodType.init(inst, def);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "optin", () => def.options.some((o) => o._zod.optin === "optional") ? "optional" : undefined);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "optout", () => def.options.some((o) => o._zod.optout === "optional") ? "optional" : undefined);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "values", () => {
        if (def.options.every((o) => o._zod.values)) {
            return new Set(def.options.flatMap((option) => Array.from(option._zod.values)));
        }
        return undefined;
    });
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "pattern", () => {
        if (def.options.every((o) => o._zod.pattern)) {
            const patterns = def.options.map((o) => o._zod.pattern);
            return new RegExp(`^(${patterns.map((p) => _util_js__WEBPACK_IMPORTED_MODULE_5__.cleanRegex(p.source)).join("|")})$`);
        }
        return undefined;
    });
    inst._zod.parse = (payload, ctx) => {
        let async = false;
        const results = [];
        for (const option of def.options) {
            const result = option._zod.run({
                value: payload.value,
                issues: [],
            }, ctx);
            if (result instanceof Promise) {
                results.push(result);
                async = true;
            }
            else {
                if (result.issues.length === 0)
                    return result;
                results.push(result);
            }
        }
        if (!async)
            return handleUnionResults(results, payload, inst, ctx);
        return Promise.all(results).then((results) => {
            return handleUnionResults(results, payload, inst, ctx);
        });
    };
});
const $ZodDiscriminatedUnion = 
/*@__PURE__*/
_core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodDiscriminatedUnion", (inst, def) => {
    $ZodUnion.init(inst, def);
    const _super = inst._zod.parse;
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "propValues", () => {
        const propValues = {};
        for (const option of def.options) {
            const pv = option._zod.propValues;
            if (!pv || Object.keys(pv).length === 0)
                throw new Error(`Invalid discriminated union option at index "${def.options.indexOf(option)}"`);
            for (const [k, v] of Object.entries(pv)) {
                if (!propValues[k])
                    propValues[k] = new Set();
                for (const val of v) {
                    propValues[k].add(val);
                }
            }
        }
        return propValues;
    });
    const disc = _util_js__WEBPACK_IMPORTED_MODULE_5__.cached(() => {
        const opts = def.options;
        const map = new Map();
        for (const o of opts) {
            const values = o._zod.propValues[def.discriminator];
            if (!values || values.size === 0)
                throw new Error(`Invalid discriminated union option at index "${def.options.indexOf(o)}"`);
            for (const v of values) {
                if (map.has(v)) {
                    throw new Error(`Duplicate discriminator value "${String(v)}"`);
                }
                map.set(v, o);
            }
        }
        return map;
    });
    inst._zod.parse = (payload, ctx) => {
        const input = payload.value;
        if (!_util_js__WEBPACK_IMPORTED_MODULE_5__.isObject(input)) {
            payload.issues.push({
                code: "invalid_type",
                expected: "object",
                input,
                inst,
            });
            return payload;
        }
        const opt = disc.value.get(input?.[def.discriminator]);
        if (opt) {
            return opt._zod.run(payload, ctx);
        }
        if (def.unionFallback) {
            return _super(payload, ctx);
        }
        // no matching discriminator
        payload.issues.push({
            code: "invalid_union",
            errors: [],
            note: "No matching discriminator",
            input,
            path: [def.discriminator],
            inst,
        });
        return payload;
    };
});
const $ZodIntersection = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodIntersection", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.parse = (payload, ctx) => {
        const input = payload.value;
        const left = def.left._zod.run({ value: input, issues: [] }, ctx);
        const right = def.right._zod.run({ value: input, issues: [] }, ctx);
        const async = left instanceof Promise || right instanceof Promise;
        if (async) {
            return Promise.all([left, right]).then(([left, right]) => {
                return handleIntersectionResults(payload, left, right);
            });
        }
        return handleIntersectionResults(payload, left, right);
    };
});
function mergeValues(a, b) {
    // const aType = parse.t(a);
    // const bType = parse.t(b);
    if (a === b) {
        return { valid: true, data: a };
    }
    if (a instanceof Date && b instanceof Date && +a === +b) {
        return { valid: true, data: a };
    }
    if (_util_js__WEBPACK_IMPORTED_MODULE_5__.isPlainObject(a) && _util_js__WEBPACK_IMPORTED_MODULE_5__.isPlainObject(b)) {
        const bKeys = Object.keys(b);
        const sharedKeys = Object.keys(a).filter((key) => bKeys.indexOf(key) !== -1);
        const newObj = { ...a, ...b };
        for (const key of sharedKeys) {
            const sharedValue = mergeValues(a[key], b[key]);
            if (!sharedValue.valid) {
                return {
                    valid: false,
                    mergeErrorPath: [key, ...sharedValue.mergeErrorPath],
                };
            }
            newObj[key] = sharedValue.data;
        }
        return { valid: true, data: newObj };
    }
    if (Array.isArray(a) && Array.isArray(b)) {
        if (a.length !== b.length) {
            return { valid: false, mergeErrorPath: [] };
        }
        const newArray = [];
        for (let index = 0; index < a.length; index++) {
            const itemA = a[index];
            const itemB = b[index];
            const sharedValue = mergeValues(itemA, itemB);
            if (!sharedValue.valid) {
                return {
                    valid: false,
                    mergeErrorPath: [index, ...sharedValue.mergeErrorPath],
                };
            }
            newArray.push(sharedValue.data);
        }
        return { valid: true, data: newArray };
    }
    return { valid: false, mergeErrorPath: [] };
}
function handleIntersectionResults(result, left, right) {
    if (left.issues.length) {
        result.issues.push(...left.issues);
    }
    if (right.issues.length) {
        result.issues.push(...right.issues);
    }
    if (_util_js__WEBPACK_IMPORTED_MODULE_5__.aborted(result))
        return result;
    const merged = mergeValues(left.value, right.value);
    if (!merged.valid) {
        throw new Error(`Unmergable intersection. Error path: ` + `${JSON.stringify(merged.mergeErrorPath)}`);
    }
    result.value = merged.data;
    return result;
}
const $ZodTuple = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodTuple", (inst, def) => {
    $ZodType.init(inst, def);
    const items = def.items;
    const optStart = items.length - [...items].reverse().findIndex((item) => item._zod.optin !== "optional");
    inst._zod.parse = (payload, ctx) => {
        const input = payload.value;
        if (!Array.isArray(input)) {
            payload.issues.push({
                input,
                inst,
                expected: "tuple",
                code: "invalid_type",
            });
            return payload;
        }
        payload.value = [];
        const proms = [];
        if (!def.rest) {
            const tooBig = input.length > items.length;
            const tooSmall = input.length < optStart - 1;
            if (tooBig || tooSmall) {
                payload.issues.push({
                    input,
                    inst,
                    origin: "array",
                    ...(tooBig ? { code: "too_big", maximum: items.length } : { code: "too_small", minimum: items.length }),
                });
                return payload;
            }
        }
        let i = -1;
        for (const item of items) {
            i++;
            if (i >= input.length)
                if (i >= optStart)
                    continue;
            const result = item._zod.run({
                value: input[i],
                issues: [],
            }, ctx);
            if (result instanceof Promise) {
                proms.push(result.then((result) => handleTupleResult(result, payload, i)));
            }
            else {
                handleTupleResult(result, payload, i);
            }
        }
        if (def.rest) {
            const rest = input.slice(items.length);
            for (const el of rest) {
                i++;
                const result = def.rest._zod.run({
                    value: el,
                    issues: [],
                }, ctx);
                if (result instanceof Promise) {
                    proms.push(result.then((result) => handleTupleResult(result, payload, i)));
                }
                else {
                    handleTupleResult(result, payload, i);
                }
            }
        }
        if (proms.length)
            return Promise.all(proms).then(() => payload);
        return payload;
    };
});
function handleTupleResult(result, final, index) {
    if (result.issues.length) {
        final.issues.push(..._util_js__WEBPACK_IMPORTED_MODULE_5__.prefixIssues(index, result.issues));
    }
    final.value[index] = result.value;
}
const $ZodRecord = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodRecord", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.parse = (payload, ctx) => {
        const input = payload.value;
        if (!_util_js__WEBPACK_IMPORTED_MODULE_5__.isPlainObject(input)) {
            payload.issues.push({
                expected: "record",
                code: "invalid_type",
                input,
                inst,
            });
            return payload;
        }
        const proms = [];
        if (def.keyType._zod.values) {
            const values = def.keyType._zod.values;
            payload.value = {};
            for (const key of values) {
                if (typeof key === "string" || typeof key === "number" || typeof key === "symbol") {
                    const result = def.valueType._zod.run({ value: input[key], issues: [] }, ctx);
                    if (result instanceof Promise) {
                        proms.push(result.then((result) => {
                            if (result.issues.length) {
                                payload.issues.push(..._util_js__WEBPACK_IMPORTED_MODULE_5__.prefixIssues(key, result.issues));
                            }
                            payload.value[key] = result.value;
                        }));
                    }
                    else {
                        if (result.issues.length) {
                            payload.issues.push(..._util_js__WEBPACK_IMPORTED_MODULE_5__.prefixIssues(key, result.issues));
                        }
                        payload.value[key] = result.value;
                    }
                }
            }
            let unrecognized;
            for (const key in input) {
                if (!values.has(key)) {
                    unrecognized = unrecognized ?? [];
                    unrecognized.push(key);
                }
            }
            if (unrecognized && unrecognized.length > 0) {
                payload.issues.push({
                    code: "unrecognized_keys",
                    input,
                    inst,
                    keys: unrecognized,
                });
            }
        }
        else {
            payload.value = {};
            for (const key of Reflect.ownKeys(input)) {
                if (key === "__proto__")
                    continue;
                const keyResult = def.keyType._zod.run({ value: key, issues: [] }, ctx);
                if (keyResult instanceof Promise) {
                    throw new Error("Async schemas not supported in object keys currently");
                }
                if (keyResult.issues.length) {
                    payload.issues.push({
                        origin: "record",
                        code: "invalid_key",
                        issues: keyResult.issues.map((iss) => _util_js__WEBPACK_IMPORTED_MODULE_5__.finalizeIssue(iss, ctx, _core_js__WEBPACK_IMPORTED_MODULE_1__.config())),
                        input: key,
                        path: [key],
                        inst,
                    });
                    payload.value[keyResult.value] = keyResult.value;
                    continue;
                }
                const result = def.valueType._zod.run({ value: input[key], issues: [] }, ctx);
                if (result instanceof Promise) {
                    proms.push(result.then((result) => {
                        if (result.issues.length) {
                            payload.issues.push(..._util_js__WEBPACK_IMPORTED_MODULE_5__.prefixIssues(key, result.issues));
                        }
                        payload.value[keyResult.value] = result.value;
                    }));
                }
                else {
                    if (result.issues.length) {
                        payload.issues.push(..._util_js__WEBPACK_IMPORTED_MODULE_5__.prefixIssues(key, result.issues));
                    }
                    payload.value[keyResult.value] = result.value;
                }
            }
        }
        if (proms.length) {
            return Promise.all(proms).then(() => payload);
        }
        return payload;
    };
});
const $ZodMap = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodMap", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.parse = (payload, ctx) => {
        const input = payload.value;
        if (!(input instanceof Map)) {
            payload.issues.push({
                expected: "map",
                code: "invalid_type",
                input,
                inst,
            });
            return payload;
        }
        const proms = [];
        payload.value = new Map();
        for (const [key, value] of input) {
            const keyResult = def.keyType._zod.run({ value: key, issues: [] }, ctx);
            const valueResult = def.valueType._zod.run({ value: value, issues: [] }, ctx);
            if (keyResult instanceof Promise || valueResult instanceof Promise) {
                proms.push(Promise.all([keyResult, valueResult]).then(([keyResult, valueResult]) => {
                    handleMapResult(keyResult, valueResult, payload, key, input, inst, ctx);
                }));
            }
            else {
                handleMapResult(keyResult, valueResult, payload, key, input, inst, ctx);
            }
        }
        if (proms.length)
            return Promise.all(proms).then(() => payload);
        return payload;
    };
});
function handleMapResult(keyResult, valueResult, final, key, input, inst, ctx) {
    if (keyResult.issues.length) {
        if (_util_js__WEBPACK_IMPORTED_MODULE_5__.propertyKeyTypes.has(typeof key)) {
            final.issues.push(..._util_js__WEBPACK_IMPORTED_MODULE_5__.prefixIssues(key, keyResult.issues));
        }
        else {
            final.issues.push({
                origin: "map",
                code: "invalid_key",
                input,
                inst,
                issues: keyResult.issues.map((iss) => _util_js__WEBPACK_IMPORTED_MODULE_5__.finalizeIssue(iss, ctx, _core_js__WEBPACK_IMPORTED_MODULE_1__.config())),
            });
        }
    }
    if (valueResult.issues.length) {
        if (_util_js__WEBPACK_IMPORTED_MODULE_5__.propertyKeyTypes.has(typeof key)) {
            final.issues.push(..._util_js__WEBPACK_IMPORTED_MODULE_5__.prefixIssues(key, valueResult.issues));
        }
        else {
            final.issues.push({
                origin: "map",
                code: "invalid_element",
                input,
                inst,
                key: key,
                issues: valueResult.issues.map((iss) => _util_js__WEBPACK_IMPORTED_MODULE_5__.finalizeIssue(iss, ctx, _core_js__WEBPACK_IMPORTED_MODULE_1__.config())),
            });
        }
    }
    final.value.set(keyResult.value, valueResult.value);
}
const $ZodSet = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodSet", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.parse = (payload, ctx) => {
        const input = payload.value;
        if (!(input instanceof Set)) {
            payload.issues.push({
                input,
                inst,
                expected: "set",
                code: "invalid_type",
            });
            return payload;
        }
        const proms = [];
        payload.value = new Set();
        for (const item of input) {
            const result = def.valueType._zod.run({ value: item, issues: [] }, ctx);
            if (result instanceof Promise) {
                proms.push(result.then((result) => handleSetResult(result, payload)));
            }
            else
                handleSetResult(result, payload);
        }
        if (proms.length)
            return Promise.all(proms).then(() => payload);
        return payload;
    };
});
function handleSetResult(result, final) {
    if (result.issues.length) {
        final.issues.push(...result.issues);
    }
    final.value.add(result.value);
}
const $ZodEnum = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodEnum", (inst, def) => {
    $ZodType.init(inst, def);
    const values = _util_js__WEBPACK_IMPORTED_MODULE_5__.getEnumValues(def.entries);
    inst._zod.values = new Set(values);
    inst._zod.pattern = new RegExp(`^(${values
        .filter((k) => _util_js__WEBPACK_IMPORTED_MODULE_5__.propertyKeyTypes.has(typeof k))
        .map((o) => (typeof o === "string" ? _util_js__WEBPACK_IMPORTED_MODULE_5__.escapeRegex(o) : o.toString()))
        .join("|")})$`);
    inst._zod.parse = (payload, _ctx) => {
        const input = payload.value;
        if (inst._zod.values.has(input)) {
            return payload;
        }
        payload.issues.push({
            code: "invalid_value",
            values,
            input,
            inst,
        });
        return payload;
    };
});
const $ZodLiteral = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodLiteral", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.values = new Set(def.values);
    inst._zod.pattern = new RegExp(`^(${def.values
        .map((o) => (typeof o === "string" ? _util_js__WEBPACK_IMPORTED_MODULE_5__.escapeRegex(o) : o ? o.toString() : String(o)))
        .join("|")})$`);
    inst._zod.parse = (payload, _ctx) => {
        const input = payload.value;
        if (inst._zod.values.has(input)) {
            return payload;
        }
        payload.issues.push({
            code: "invalid_value",
            values: def.values,
            input,
            inst,
        });
        return payload;
    };
});
const $ZodFile = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodFile", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.parse = (payload, _ctx) => {
        const input = payload.value;
        if (input instanceof File)
            return payload;
        payload.issues.push({
            expected: "file",
            code: "invalid_type",
            input,
            inst,
        });
        return payload;
    };
});
const $ZodTransform = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodTransform", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.parse = (payload, _ctx) => {
        const _out = def.transform(payload.value, payload);
        if (_ctx.async) {
            const output = _out instanceof Promise ? _out : Promise.resolve(_out);
            return output.then((output) => {
                payload.value = output;
                return payload;
            });
        }
        if (_out instanceof Promise) {
            throw new _core_js__WEBPACK_IMPORTED_MODULE_1__.$ZodAsyncError();
        }
        payload.value = _out;
        return payload;
    };
});
const $ZodOptional = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodOptional", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.optin = "optional";
    inst._zod.optout = "optional";
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "values", () => {
        return def.innerType._zod.values ? new Set([...def.innerType._zod.values, undefined]) : undefined;
    });
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "pattern", () => {
        const pattern = def.innerType._zod.pattern;
        return pattern ? new RegExp(`^(${_util_js__WEBPACK_IMPORTED_MODULE_5__.cleanRegex(pattern.source)})?$`) : undefined;
    });
    inst._zod.parse = (payload, ctx) => {
        if (def.innerType._zod.optin === "optional") {
            return def.innerType._zod.run(payload, ctx);
        }
        if (payload.value === undefined) {
            return payload;
        }
        return def.innerType._zod.run(payload, ctx);
    };
});
const $ZodNullable = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodNullable", (inst, def) => {
    $ZodType.init(inst, def);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "optin", () => def.innerType._zod.optin);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "optout", () => def.innerType._zod.optout);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "pattern", () => {
        const pattern = def.innerType._zod.pattern;
        return pattern ? new RegExp(`^(${_util_js__WEBPACK_IMPORTED_MODULE_5__.cleanRegex(pattern.source)}|null)$`) : undefined;
    });
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "values", () => {
        return def.innerType._zod.values ? new Set([...def.innerType._zod.values, null]) : undefined;
    });
    inst._zod.parse = (payload, ctx) => {
        if (payload.value === null)
            return payload;
        return def.innerType._zod.run(payload, ctx);
    };
});
const $ZodDefault = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodDefault", (inst, def) => {
    $ZodType.init(inst, def);
    // inst._zod.qin = "true";
    inst._zod.optin = "optional";
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "values", () => def.innerType._zod.values);
    inst._zod.parse = (payload, ctx) => {
        if (payload.value === undefined) {
            payload.value = def.defaultValue;
            /**
             * $ZodDefault always returns the default value immediately.
             * It doesn't pass the default value into the validator ("prefault"). There's no reason to pass the default value through validation. The validity of the default is enforced by TypeScript statically. Otherwise, it's the responsibility of the user to ensure the default is valid. In the case of pipes with divergent in/out types, you can specify the default on the `in` schema of your ZodPipe to set a "prefault" for the pipe.   */
            return payload;
        }
        const result = def.innerType._zod.run(payload, ctx);
        if (result instanceof Promise) {
            return result.then((result) => handleDefaultResult(result, def));
        }
        return handleDefaultResult(result, def);
    };
});
function handleDefaultResult(payload, def) {
    if (payload.value === undefined) {
        payload.value = def.defaultValue;
    }
    return payload;
}
const $ZodPrefault = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodPrefault", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.optin = "optional";
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "values", () => def.innerType._zod.values);
    inst._zod.parse = (payload, ctx) => {
        if (payload.value === undefined) {
            payload.value = def.defaultValue;
        }
        return def.innerType._zod.run(payload, ctx);
    };
});
const $ZodNonOptional = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodNonOptional", (inst, def) => {
    $ZodType.init(inst, def);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "values", () => {
        const v = def.innerType._zod.values;
        return v ? new Set([...v].filter((x) => x !== undefined)) : undefined;
    });
    inst._zod.parse = (payload, ctx) => {
        const result = def.innerType._zod.run(payload, ctx);
        if (result instanceof Promise) {
            return result.then((result) => handleNonOptionalResult(result, inst));
        }
        return handleNonOptionalResult(result, inst);
    };
});
function handleNonOptionalResult(payload, inst) {
    if (!payload.issues.length && payload.value === undefined) {
        payload.issues.push({
            code: "invalid_type",
            expected: "nonoptional",
            input: payload.value,
            inst,
        });
    }
    return payload;
}
const $ZodSuccess = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodSuccess", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.parse = (payload, ctx) => {
        const result = def.innerType._zod.run(payload, ctx);
        if (result instanceof Promise) {
            return result.then((result) => {
                payload.value = result.issues.length === 0;
                return payload;
            });
        }
        payload.value = result.issues.length === 0;
        return payload;
    };
});
const $ZodCatch = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodCatch", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.optin = "optional";
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "optout", () => def.innerType._zod.optout);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "values", () => def.innerType._zod.values);
    inst._zod.parse = (payload, ctx) => {
        const result = def.innerType._zod.run(payload, ctx);
        if (result instanceof Promise) {
            return result.then((result) => {
                payload.value = result.value;
                if (result.issues.length) {
                    payload.value = def.catchValue({
                        ...payload,
                        error: {
                            issues: result.issues.map((iss) => _util_js__WEBPACK_IMPORTED_MODULE_5__.finalizeIssue(iss, ctx, _core_js__WEBPACK_IMPORTED_MODULE_1__.config())),
                        },
                        input: payload.value,
                    });
                    payload.issues = [];
                }
                return payload;
            });
        }
        payload.value = result.value;
        if (result.issues.length) {
            payload.value = def.catchValue({
                ...payload,
                error: {
                    issues: result.issues.map((iss) => _util_js__WEBPACK_IMPORTED_MODULE_5__.finalizeIssue(iss, ctx, _core_js__WEBPACK_IMPORTED_MODULE_1__.config())),
                },
                input: payload.value,
            });
            payload.issues = [];
        }
        return payload;
    };
});
const $ZodNaN = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodNaN", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.parse = (payload, _ctx) => {
        if (typeof payload.value !== "number" || !Number.isNaN(payload.value)) {
            payload.issues.push({
                input: payload.value,
                inst,
                expected: "nan",
                code: "invalid_type",
            });
            return payload;
        }
        return payload;
    };
});
const $ZodPipe = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodPipe", (inst, def) => {
    $ZodType.init(inst, def);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "values", () => def.in._zod.values);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "optin", () => def.in._zod.optin);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "optout", () => def.out._zod.optout);
    inst._zod.parse = (payload, ctx) => {
        const left = def.in._zod.run(payload, ctx);
        if (left instanceof Promise) {
            return left.then((left) => handlePipeResult(left, def, ctx));
        }
        return handlePipeResult(left, def, ctx);
    };
});
function handlePipeResult(left, def, ctx) {
    if (_util_js__WEBPACK_IMPORTED_MODULE_5__.aborted(left)) {
        return left;
    }
    return def.out._zod.run({ value: left.value, issues: left.issues }, ctx);
}
const $ZodReadonly = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodReadonly", (inst, def) => {
    $ZodType.init(inst, def);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "propValues", () => def.innerType._zod.propValues);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "values", () => def.innerType._zod.values);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "optin", () => def.innerType._zod.optin);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "optout", () => def.innerType._zod.optout);
    inst._zod.parse = (payload, ctx) => {
        const result = def.innerType._zod.run(payload, ctx);
        if (result instanceof Promise) {
            return result.then(handleReadonlyResult);
        }
        return handleReadonlyResult(result);
    };
});
function handleReadonlyResult(payload) {
    payload.value = Object.freeze(payload.value);
    return payload;
}
const $ZodTemplateLiteral = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodTemplateLiteral", (inst, def) => {
    $ZodType.init(inst, def);
    const regexParts = [];
    for (const part of def.parts) {
        if (part instanceof $ZodType) {
            if (!part._zod.pattern) {
                // if (!source)
                throw new Error(`Invalid template literal part, no pattern found: ${[...part._zod.traits].shift()}`);
            }
            const source = part._zod.pattern instanceof RegExp ? part._zod.pattern.source : part._zod.pattern;
            if (!source)
                throw new Error(`Invalid template literal part: ${part._zod.traits}`);
            const start = source.startsWith("^") ? 1 : 0;
            const end = source.endsWith("$") ? source.length - 1 : source.length;
            regexParts.push(source.slice(start, end));
        }
        else if (part === null || _util_js__WEBPACK_IMPORTED_MODULE_5__.primitiveTypes.has(typeof part)) {
            regexParts.push(_util_js__WEBPACK_IMPORTED_MODULE_5__.escapeRegex(`${part}`));
        }
        else {
            throw new Error(`Invalid template literal part: ${part}`);
        }
    }
    inst._zod.pattern = new RegExp(`^${regexParts.join("")}$`);
    inst._zod.parse = (payload, _ctx) => {
        if (typeof payload.value !== "string") {
            payload.issues.push({
                input: payload.value,
                inst,
                expected: "template_literal",
                code: "invalid_type",
            });
            return payload;
        }
        inst._zod.pattern.lastIndex = 0;
        if (!inst._zod.pattern.test(payload.value)) {
            payload.issues.push({
                input: payload.value,
                inst,
                code: "invalid_format",
                format: "template_literal",
                pattern: inst._zod.pattern.source,
            });
            return payload;
        }
        return payload;
    };
});
const $ZodPromise = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodPromise", (inst, def) => {
    $ZodType.init(inst, def);
    inst._zod.parse = (payload, ctx) => {
        return Promise.resolve(payload.value).then((inner) => def.innerType._zod.run({ value: inner, issues: [] }, ctx));
    };
});
const $ZodLazy = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodLazy", (inst, def) => {
    $ZodType.init(inst, def);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "innerType", () => def.getter());
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "pattern", () => inst._zod.innerType._zod.pattern);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "propValues", () => inst._zod.innerType._zod.propValues);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "optin", () => inst._zod.innerType._zod.optin);
    _util_js__WEBPACK_IMPORTED_MODULE_5__.defineLazy(inst._zod, "optout", () => inst._zod.innerType._zod.optout);
    inst._zod.parse = (payload, ctx) => {
        const inner = inst._zod.innerType;
        return inner._zod.run(payload, ctx);
    };
});
const $ZodCustom = /*@__PURE__*/ _core_js__WEBPACK_IMPORTED_MODULE_1__.$constructor("$ZodCustom", (inst, def) => {
    _checks_js__WEBPACK_IMPORTED_MODULE_0__.$ZodCheck.init(inst, def);
    $ZodType.init(inst, def);
    inst._zod.parse = (payload, _) => {
        return payload;
    };
    inst._zod.check = (payload) => {
        const input = payload.value;
        const r = def.fn(input);
        if (r instanceof Promise) {
            return r.then((r) => handleRefineResult(r, payload, input, inst));
        }
        handleRefineResult(r, payload, input, inst);
        return;
    };
});
function handleRefineResult(result, payload, input, inst) {
    if (!result) {
        const _iss = {
            code: "custom",
            input,
            inst, // incorporates params.error into issue reporting
            path: [...(inst._zod.def.path ?? [])], // incorporates params.error into issue reporting
            continue: !inst._zod.def.abort,
            // params: inst._zod.def.params,
        };
        if (inst._zod.def.params)
            _iss.params = inst._zod.def.params;
        payload.issues.push(_util_js__WEBPACK_IMPORTED_MODULE_5__.issue(_iss));
    }
}


/***/ }),

/***/ "./node_modules/zod/v4/core/to-json-schema.js":
/*!****************************************************!*\
  !*** ./node_modules/zod/v4/core/to-json-schema.js ***!
  \****************************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   JSONSchemaGenerator: function() { return /* binding */ JSONSchemaGenerator; },
/* harmony export */   toJSONSchema: function() { return /* binding */ toJSONSchema; }
/* harmony export */ });
/* harmony import */ var _registries_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./registries.js */ "./node_modules/zod/v4/core/registries.js");
/* harmony import */ var _util_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./util.js */ "./node_modules/zod/v4/core/util.js");


class JSONSchemaGenerator {
    constructor(params) {
        this.counter = 0;
        this.metadataRegistry = params?.metadata ?? _registries_js__WEBPACK_IMPORTED_MODULE_0__.globalRegistry;
        this.target = params?.target ?? "draft-2020-12";
        this.unrepresentable = params?.unrepresentable ?? "throw";
        this.override = params?.override ?? (() => { });
        this.io = params?.io ?? "output";
        this.seen = new Map();
    }
    process(schema, _params = { path: [], schemaPath: [] }) {
        var _a;
        const def = schema._zod.def;
        const formatMap = {
            guid: "uuid",
            url: "uri",
            datetime: "date-time",
            json_string: "json-string",
            regex: "", // do not set
        };
        // check for schema in seens
        const seen = this.seen.get(schema);
        if (seen) {
            seen.count++;
            // check if cycle
            const isCycle = _params.schemaPath.includes(schema);
            if (isCycle) {
                seen.cycle = _params.path;
            }
            return seen.schema;
        }
        // initialize
        const result = { schema: {}, count: 1, cycle: undefined, path: _params.path };
        this.seen.set(schema, result);
        // custom method overrides default behavior
        const overrideSchema = schema._zod.toJSONSchema?.();
        if (overrideSchema) {
            result.schema = overrideSchema;
        }
        else {
            const params = {
                ..._params,
                schemaPath: [..._params.schemaPath, schema],
                path: _params.path,
            };
            const parent = schema._zod.parent;
            if (parent) {
                // schema was cloned from another schema
                result.ref = parent;
                this.process(parent, params);
                this.seen.get(parent).isParent = true;
            }
            else {
                const _json = result.schema;
                switch (def.type) {
                    case "string": {
                        const json = _json;
                        json.type = "string";
                        const { minimum, maximum, format, patterns, contentEncoding } = schema._zod
                            .bag;
                        if (typeof minimum === "number")
                            json.minLength = minimum;
                        if (typeof maximum === "number")
                            json.maxLength = maximum;
                        // custom pattern overrides format
                        if (format) {
                            json.format = formatMap[format] ?? format;
                            if (json.format === "")
                                delete json.format; // empty format is not valid
                        }
                        if (contentEncoding)
                            json.contentEncoding = contentEncoding;
                        if (patterns && patterns.size > 0) {
                            const regexes = [...patterns];
                            if (regexes.length === 1)
                                json.pattern = regexes[0].source;
                            else if (regexes.length > 1) {
                                result.schema.allOf = [
                                    ...regexes.map((regex) => ({
                                        ...(this.target === "draft-7" ? { type: "string" } : {}),
                                        pattern: regex.source,
                                    })),
                                ];
                            }
                        }
                        break;
                    }
                    case "number": {
                        const json = _json;
                        const { minimum, maximum, format, multipleOf, exclusiveMaximum, exclusiveMinimum } = schema._zod.bag;
                        if (typeof format === "string" && format.includes("int"))
                            json.type = "integer";
                        else
                            json.type = "number";
                        if (typeof exclusiveMinimum === "number")
                            json.exclusiveMinimum = exclusiveMinimum;
                        if (typeof minimum === "number") {
                            json.minimum = minimum;
                            if (typeof exclusiveMinimum === "number") {
                                if (exclusiveMinimum >= minimum)
                                    delete json.minimum;
                                else
                                    delete json.exclusiveMinimum;
                            }
                        }
                        if (typeof exclusiveMaximum === "number")
                            json.exclusiveMaximum = exclusiveMaximum;
                        if (typeof maximum === "number") {
                            json.maximum = maximum;
                            if (typeof exclusiveMaximum === "number") {
                                if (exclusiveMaximum <= maximum)
                                    delete json.maximum;
                                else
                                    delete json.exclusiveMaximum;
                            }
                        }
                        if (typeof multipleOf === "number")
                            json.multipleOf = multipleOf;
                        break;
                    }
                    case "boolean": {
                        const json = _json;
                        json.type = "boolean";
                        break;
                    }
                    case "bigint": {
                        if (this.unrepresentable === "throw") {
                            throw new Error("BigInt cannot be represented in JSON Schema");
                        }
                        break;
                    }
                    case "symbol": {
                        if (this.unrepresentable === "throw") {
                            throw new Error("Symbols cannot be represented in JSON Schema");
                        }
                        break;
                    }
                    case "null": {
                        _json.type = "null";
                        break;
                    }
                    case "any": {
                        break;
                    }
                    case "unknown": {
                        break;
                    }
                    case "undefined": {
                        if (this.unrepresentable === "throw") {
                            throw new Error("Undefined cannot be represented in JSON Schema");
                        }
                        break;
                    }
                    case "void": {
                        if (this.unrepresentable === "throw") {
                            throw new Error("Void cannot be represented in JSON Schema");
                        }
                        break;
                    }
                    case "never": {
                        _json.not = {};
                        break;
                    }
                    case "date": {
                        if (this.unrepresentable === "throw") {
                            throw new Error("Date cannot be represented in JSON Schema");
                        }
                        break;
                    }
                    case "array": {
                        const json = _json;
                        const { minimum, maximum } = schema._zod.bag;
                        if (typeof minimum === "number")
                            json.minItems = minimum;
                        if (typeof maximum === "number")
                            json.maxItems = maximum;
                        json.type = "array";
                        json.items = this.process(def.element, { ...params, path: [...params.path, "items"] });
                        break;
                    }
                    case "object": {
                        const json = _json;
                        json.type = "object";
                        json.properties = {};
                        const shape = def.shape; // params.shapeCache.get(schema)!;
                        for (const key in shape) {
                            json.properties[key] = this.process(shape[key], {
                                ...params,
                                path: [...params.path, "properties", key],
                            });
                        }
                        // required keys
                        const allKeys = new Set(Object.keys(shape));
                        // const optionalKeys = new Set(def.optional);
                        const requiredKeys = new Set([...allKeys].filter((key) => {
                            const v = def.shape[key]._zod;
                            if (this.io === "input") {
                                return v.optin === undefined;
                            }
                            else {
                                return v.optout === undefined;
                            }
                        }));
                        if (requiredKeys.size > 0) {
                            json.required = Array.from(requiredKeys);
                        }
                        // catchall
                        if (def.catchall?._zod.def.type === "never") {
                            // strict
                            json.additionalProperties = false;
                        }
                        else if (!def.catchall) {
                            // regular
                            if (this.io === "output")
                                json.additionalProperties = false;
                        }
                        else if (def.catchall) {
                            json.additionalProperties = this.process(def.catchall, {
                                ...params,
                                path: [...params.path, "additionalProperties"],
                            });
                        }
                        break;
                    }
                    case "union": {
                        const json = _json;
                        json.anyOf = def.options.map((x, i) => this.process(x, {
                            ...params,
                            path: [...params.path, "anyOf", i],
                        }));
                        break;
                    }
                    case "intersection": {
                        const json = _json;
                        const a = this.process(def.left, {
                            ...params,
                            path: [...params.path, "allOf", 0],
                        });
                        const b = this.process(def.right, {
                            ...params,
                            path: [...params.path, "allOf", 1],
                        });
                        const isSimpleIntersection = (val) => "allOf" in val && Object.keys(val).length === 1;
                        const allOf = [
                            ...(isSimpleIntersection(a) ? a.allOf : [a]),
                            ...(isSimpleIntersection(b) ? b.allOf : [b]),
                        ];
                        json.allOf = allOf;
                        break;
                    }
                    case "tuple": {
                        const json = _json;
                        json.type = "array";
                        const prefixItems = def.items.map((x, i) => this.process(x, { ...params, path: [...params.path, "prefixItems", i] }));
                        if (this.target === "draft-2020-12") {
                            json.prefixItems = prefixItems;
                        }
                        else {
                            json.items = prefixItems;
                        }
                        if (def.rest) {
                            const rest = this.process(def.rest, {
                                ...params,
                                path: [...params.path, "items"],
                            });
                            if (this.target === "draft-2020-12") {
                                json.items = rest;
                            }
                            else {
                                json.additionalItems = rest;
                            }
                        }
                        // additionalItems
                        if (def.rest) {
                            json.items = this.process(def.rest, {
                                ...params,
                                path: [...params.path, "items"],
                            });
                        }
                        // length
                        const { minimum, maximum } = schema._zod.bag;
                        if (typeof minimum === "number")
                            json.minItems = minimum;
                        if (typeof maximum === "number")
                            json.maxItems = maximum;
                        break;
                    }
                    case "record": {
                        const json = _json;
                        json.type = "object";
                        json.propertyNames = this.process(def.keyType, { ...params, path: [...params.path, "propertyNames"] });
                        json.additionalProperties = this.process(def.valueType, {
                            ...params,
                            path: [...params.path, "additionalProperties"],
                        });
                        break;
                    }
                    case "map": {
                        if (this.unrepresentable === "throw") {
                            throw new Error("Map cannot be represented in JSON Schema");
                        }
                        break;
                    }
                    case "set": {
                        if (this.unrepresentable === "throw") {
                            throw new Error("Set cannot be represented in JSON Schema");
                        }
                        break;
                    }
                    case "enum": {
                        const json = _json;
                        const values = (0,_util_js__WEBPACK_IMPORTED_MODULE_1__.getEnumValues)(def.entries);
                        // Number enums can have both string and number values
                        if (values.every((v) => typeof v === "number"))
                            json.type = "number";
                        if (values.every((v) => typeof v === "string"))
                            json.type = "string";
                        json.enum = values;
                        break;
                    }
                    case "literal": {
                        const json = _json;
                        const vals = [];
                        for (const val of def.values) {
                            if (val === undefined) {
                                if (this.unrepresentable === "throw") {
                                    throw new Error("Literal `undefined` cannot be represented in JSON Schema");
                                }
                                else {
                                    // do not add to vals
                                }
                            }
                            else if (typeof val === "bigint") {
                                if (this.unrepresentable === "throw") {
                                    throw new Error("BigInt literals cannot be represented in JSON Schema");
                                }
                                else {
                                    vals.push(Number(val));
                                }
                            }
                            else {
                                vals.push(val);
                            }
                        }
                        if (vals.length === 0) {
                            // do nothing (an undefined literal was stripped)
                        }
                        else if (vals.length === 1) {
                            const val = vals[0];
                            json.type = val === null ? "null" : typeof val;
                            json.const = val;
                        }
                        else {
                            if (vals.every((v) => typeof v === "number"))
                                json.type = "number";
                            if (vals.every((v) => typeof v === "string"))
                                json.type = "string";
                            if (vals.every((v) => typeof v === "boolean"))
                                json.type = "string";
                            if (vals.every((v) => v === null))
                                json.type = "null";
                            json.enum = vals;
                        }
                        break;
                    }
                    case "file": {
                        const json = _json;
                        const file = {
                            type: "string",
                            format: "binary",
                            contentEncoding: "binary",
                        };
                        const { minimum, maximum, mime } = schema._zod.bag;
                        if (minimum !== undefined)
                            file.minLength = minimum;
                        if (maximum !== undefined)
                            file.maxLength = maximum;
                        if (mime) {
                            if (mime.length === 1) {
                                file.contentMediaType = mime[0];
                                Object.assign(json, file);
                            }
                            else {
                                json.anyOf = mime.map((m) => {
                                    const mFile = { ...file, contentMediaType: m };
                                    return mFile;
                                });
                            }
                        }
                        else {
                            Object.assign(json, file);
                        }
                        // if (this.unrepresentable === "throw") {
                        //   throw new Error("File cannot be represented in JSON Schema");
                        // }
                        break;
                    }
                    case "transform": {
                        if (this.unrepresentable === "throw") {
                            throw new Error("Transforms cannot be represented in JSON Schema");
                        }
                        break;
                    }
                    case "nullable": {
                        const inner = this.process(def.innerType, params);
                        _json.anyOf = [inner, { type: "null" }];
                        break;
                    }
                    case "nonoptional": {
                        this.process(def.innerType, params);
                        result.ref = def.innerType;
                        break;
                    }
                    case "success": {
                        const json = _json;
                        json.type = "boolean";
                        break;
                    }
                    case "default": {
                        this.process(def.innerType, params);
                        result.ref = def.innerType;
                        _json.default = JSON.parse(JSON.stringify(def.defaultValue));
                        break;
                    }
                    case "prefault": {
                        this.process(def.innerType, params);
                        result.ref = def.innerType;
                        if (this.io === "input")
                            _json._prefault = JSON.parse(JSON.stringify(def.defaultValue));
                        break;
                    }
                    case "catch": {
                        // use conditionals
                        this.process(def.innerType, params);
                        result.ref = def.innerType;
                        let catchValue;
                        try {
                            catchValue = def.catchValue(undefined);
                        }
                        catch {
                            throw new Error("Dynamic catch values are not supported in JSON Schema");
                        }
                        _json.default = catchValue;
                        break;
                    }
                    case "nan": {
                        if (this.unrepresentable === "throw") {
                            throw new Error("NaN cannot be represented in JSON Schema");
                        }
                        break;
                    }
                    case "template_literal": {
                        const json = _json;
                        const pattern = schema._zod.pattern;
                        if (!pattern)
                            throw new Error("Pattern not found in template literal");
                        json.type = "string";
                        json.pattern = pattern.source;
                        break;
                    }
                    case "pipe": {
                        const innerType = this.io === "input" ? (def.in._zod.def.type === "transform" ? def.out : def.in) : def.out;
                        this.process(innerType, params);
                        result.ref = innerType;
                        break;
                    }
                    case "readonly": {
                        this.process(def.innerType, params);
                        result.ref = def.innerType;
                        _json.readOnly = true;
                        break;
                    }
                    // passthrough types
                    case "promise": {
                        this.process(def.innerType, params);
                        result.ref = def.innerType;
                        break;
                    }
                    case "optional": {
                        this.process(def.innerType, params);
                        result.ref = def.innerType;
                        break;
                    }
                    case "lazy": {
                        const innerType = schema._zod.innerType;
                        this.process(innerType, params);
                        result.ref = innerType;
                        break;
                    }
                    case "custom": {
                        if (this.unrepresentable === "throw") {
                            throw new Error("Custom types cannot be represented in JSON Schema");
                        }
                        break;
                    }
                    default: {
                        def;
                    }
                }
            }
        }
        // metadata
        const meta = this.metadataRegistry.get(schema);
        if (meta)
            Object.assign(result.schema, meta);
        if (this.io === "input" && isTransforming(schema)) {
            // examples/defaults only apply to output type of pipe
            delete result.schema.examples;
            delete result.schema.default;
        }
        // set prefault as default
        if (this.io === "input" && result.schema._prefault)
            (_a = result.schema).default ?? (_a.default = result.schema._prefault);
        delete result.schema._prefault;
        // pulling fresh from this.seen in case it was overwritten
        const _result = this.seen.get(schema);
        return _result.schema;
    }
    emit(schema, _params) {
        const params = {
            cycles: _params?.cycles ?? "ref",
            reused: _params?.reused ?? "inline",
            // unrepresentable: _params?.unrepresentable ?? "throw",
            // uri: _params?.uri ?? ((id) => `${id}`),
            external: _params?.external ?? undefined,
        };
        // iterate over seen map;
        const root = this.seen.get(schema);
        if (!root)
            throw new Error("Unprocessed schema. This is a bug in Zod.");
        // initialize result with root schema fields
        // Object.assign(result, seen.cached);
        // returns a ref to the schema
        // defId will be empty if the ref points to an external schema (or #)
        const makeURI = (entry) => {
            // comparing the seen objects because sometimes
            // multiple schemas map to the same seen object.
            // e.g. lazy
            // external is configured
            const defsSegment = this.target === "draft-2020-12" ? "$defs" : "definitions";
            if (params.external) {
                const externalId = params.external.registry.get(entry[0])?.id; // ?? "__shared";// `__schema${this.counter++}`;
                // check if schema is in the external registry
                const uriGenerator = params.external.uri ?? ((id) => id);
                if (externalId) {
                    return { ref: uriGenerator(externalId) };
                }
                // otherwise, add to __shared
                const id = entry[1].defId ?? entry[1].schema.id ?? `schema${this.counter++}`;
                entry[1].defId = id; // set defId so it will be reused if needed
                return { defId: id, ref: `${uriGenerator("__shared")}#/${defsSegment}/${id}` };
            }
            if (entry[1] === root) {
                return { ref: "#" };
            }
            // self-contained schema
            const uriPrefix = `#`;
            const defUriPrefix = `${uriPrefix}/${defsSegment}/`;
            const defId = entry[1].schema.id ?? `__schema${this.counter++}`;
            return { defId, ref: defUriPrefix + defId };
        };
        // stored cached version in `def` property
        // remove all properties, set $ref
        const extractToDef = (entry) => {
            // if the schema is already a reference, do not extract it
            if (entry[1].schema.$ref) {
                return;
            }
            const seen = entry[1];
            const { ref, defId } = makeURI(entry);
            seen.def = { ...seen.schema };
            // defId won't be set if the schema is a reference to an external schema
            if (defId)
                seen.defId = defId;
            // wipe away all properties except $ref
            const schema = seen.schema;
            for (const key in schema) {
                delete schema[key];
            }
            schema.$ref = ref;
        };
        // throw on cycles
        // break cycles
        if (params.cycles === "throw") {
            for (const entry of this.seen.entries()) {
                const seen = entry[1];
                if (seen.cycle) {
                    throw new Error("Cycle detected: " +
                        `#/${seen.cycle?.join("/")}/<root>` +
                        '\n\nSet the `cycles` parameter to `"ref"` to resolve cyclical schemas with defs.');
                }
            }
        }
        // extract schemas into $defs
        for (const entry of this.seen.entries()) {
            const seen = entry[1];
            // convert root schema to # $ref
            if (schema === entry[0]) {
                extractToDef(entry); // this has special handling for the root schema
                continue;
            }
            // extract schemas that are in the external registry
            if (params.external) {
                const ext = params.external.registry.get(entry[0])?.id;
                if (schema !== entry[0] && ext) {
                    extractToDef(entry);
                    continue;
                }
            }
            // extract schemas with `id` meta
            const id = this.metadataRegistry.get(entry[0])?.id;
            if (id) {
                extractToDef(entry);
                continue;
            }
            // break cycles
            if (seen.cycle) {
                // any
                extractToDef(entry);
                continue;
            }
            // extract reused schemas
            if (seen.count > 1) {
                if (params.reused === "ref") {
                    extractToDef(entry);
                    // biome-ignore lint:
                    continue;
                }
            }
        }
        // flatten _refs
        const flattenRef = (zodSchema, params) => {
            const seen = this.seen.get(zodSchema);
            const schema = seen.def ?? seen.schema;
            const _cached = { ...schema };
            // already seen
            if (seen.ref === null) {
                return;
            }
            // flatten ref if defined
            const ref = seen.ref;
            seen.ref = null; // prevent recursion
            if (ref) {
                flattenRef(ref, params);
                // merge referenced schema into current
                const refSchema = this.seen.get(ref).schema;
                if (refSchema.$ref && params.target === "draft-7") {
                    schema.allOf = schema.allOf ?? [];
                    schema.allOf.push(refSchema);
                }
                else {
                    Object.assign(schema, refSchema);
                    Object.assign(schema, _cached); // prevent overwriting any fields in the original schema
                }
            }
            // execute overrides
            if (!seen.isParent)
                this.override({
                    zodSchema: zodSchema,
                    jsonSchema: schema,
                    path: seen.path ?? [],
                });
        };
        for (const entry of [...this.seen.entries()].reverse()) {
            flattenRef(entry[0], { target: this.target });
        }
        const result = {};
        if (this.target === "draft-2020-12") {
            result.$schema = "https://json-schema.org/draft/2020-12/schema";
        }
        else if (this.target === "draft-7") {
            result.$schema = "http://json-schema.org/draft-07/schema#";
        }
        else {
            console.warn(`Invalid target: ${this.target}`);
        }
        if (params.external?.uri) {
            const id = params.external.registry.get(schema)?.id;
            if (!id)
                throw new Error("Schema is missing an `id` property");
            result.$id = params.external.uri(id);
        }
        Object.assign(result, root.def);
        // build defs object
        const defs = params.external?.defs ?? {};
        for (const entry of this.seen.entries()) {
            const seen = entry[1];
            if (seen.def && seen.defId) {
                defs[seen.defId] = seen.def;
            }
        }
        // set definitions in result
        if (params.external) {
        }
        else {
            if (Object.keys(defs).length > 0) {
                if (this.target === "draft-2020-12") {
                    result.$defs = defs;
                }
                else {
                    result.definitions = defs;
                }
            }
        }
        try {
            // this "finalizes" this schema and ensures all cycles are removed
            // each call to .emit() is functionally independent
            // though the seen map is shared
            return JSON.parse(JSON.stringify(result));
        }
        catch (_err) {
            throw new Error("Error converting schema to JSON.");
        }
    }
}
function toJSONSchema(input, _params) {
    if (input instanceof _registries_js__WEBPACK_IMPORTED_MODULE_0__.$ZodRegistry) {
        const gen = new JSONSchemaGenerator(_params);
        const defs = {};
        for (const entry of input._idmap.entries()) {
            const [_, schema] = entry;
            gen.process(schema);
        }
        const schemas = {};
        const external = {
            registry: input,
            uri: _params?.uri,
            defs,
        };
        for (const entry of input._idmap.entries()) {
            const [key, schema] = entry;
            schemas[key] = gen.emit(schema, {
                ..._params,
                external,
            });
        }
        if (Object.keys(defs).length > 0) {
            const defsSegment = gen.target === "draft-2020-12" ? "$defs" : "definitions";
            schemas.__shared = {
                [defsSegment]: defs,
            };
        }
        return { schemas };
    }
    const gen = new JSONSchemaGenerator(_params);
    gen.process(input);
    return gen.emit(input, _params);
}
function isTransforming(_schema, _ctx) {
    const ctx = _ctx ?? { seen: new Set() };
    if (ctx.seen.has(_schema))
        return false;
    ctx.seen.add(_schema);
    const schema = _schema;
    const def = schema._zod.def;
    switch (def.type) {
        case "string":
        case "number":
        case "bigint":
        case "boolean":
        case "date":
        case "symbol":
        case "undefined":
        case "null":
        case "any":
        case "unknown":
        case "never":
        case "void":
        case "literal":
        case "enum":
        case "nan":
        case "file":
        case "template_literal":
            return false;
        case "array": {
            return isTransforming(def.element, ctx);
        }
        case "object": {
            for (const key in def.shape) {
                if (isTransforming(def.shape[key], ctx))
                    return true;
            }
            return false;
        }
        case "union": {
            for (const option of def.options) {
                if (isTransforming(option, ctx))
                    return true;
            }
            return false;
        }
        case "intersection": {
            return isTransforming(def.left, ctx) || isTransforming(def.right, ctx);
        }
        case "tuple": {
            for (const item of def.items) {
                if (isTransforming(item, ctx))
                    return true;
            }
            if (def.rest && isTransforming(def.rest, ctx))
                return true;
            return false;
        }
        case "record": {
            return isTransforming(def.keyType, ctx) || isTransforming(def.valueType, ctx);
        }
        case "map": {
            return isTransforming(def.keyType, ctx) || isTransforming(def.valueType, ctx);
        }
        case "set": {
            return isTransforming(def.valueType, ctx);
        }
        // inner types
        case "promise":
        case "optional":
        case "nonoptional":
        case "nullable":
        case "readonly":
            return isTransforming(def.innerType, ctx);
        case "lazy":
            return isTransforming(def.getter(), ctx);
        case "default": {
            return isTransforming(def.innerType, ctx);
        }
        case "prefault": {
            return isTransforming(def.innerType, ctx);
        }
        case "custom": {
            return false;
        }
        case "transform": {
            return true;
        }
        case "pipe": {
            return isTransforming(def.in, ctx) || isTransforming(def.out, ctx);
        }
        case "success": {
            return false;
        }
        case "catch": {
            return false;
        }
        default:
            def;
    }
    throw new Error(`Unknown schema type: ${def.type}`);
}


/***/ }),

/***/ "./node_modules/zod/v4/core/util.js":
/*!******************************************!*\
  !*** ./node_modules/zod/v4/core/util.js ***!
  \******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   BIGINT_FORMAT_RANGES: function() { return /* binding */ BIGINT_FORMAT_RANGES; },
/* harmony export */   Class: function() { return /* binding */ Class; },
/* harmony export */   NUMBER_FORMAT_RANGES: function() { return /* binding */ NUMBER_FORMAT_RANGES; },
/* harmony export */   aborted: function() { return /* binding */ aborted; },
/* harmony export */   allowsEval: function() { return /* binding */ allowsEval; },
/* harmony export */   assert: function() { return /* binding */ assert; },
/* harmony export */   assertEqual: function() { return /* binding */ assertEqual; },
/* harmony export */   assertIs: function() { return /* binding */ assertIs; },
/* harmony export */   assertNever: function() { return /* binding */ assertNever; },
/* harmony export */   assertNotEqual: function() { return /* binding */ assertNotEqual; },
/* harmony export */   assignProp: function() { return /* binding */ assignProp; },
/* harmony export */   cached: function() { return /* binding */ cached; },
/* harmony export */   captureStackTrace: function() { return /* binding */ captureStackTrace; },
/* harmony export */   cleanEnum: function() { return /* binding */ cleanEnum; },
/* harmony export */   cleanRegex: function() { return /* binding */ cleanRegex; },
/* harmony export */   clone: function() { return /* binding */ clone; },
/* harmony export */   createTransparentProxy: function() { return /* binding */ createTransparentProxy; },
/* harmony export */   defineLazy: function() { return /* binding */ defineLazy; },
/* harmony export */   esc: function() { return /* binding */ esc; },
/* harmony export */   escapeRegex: function() { return /* binding */ escapeRegex; },
/* harmony export */   extend: function() { return /* binding */ extend; },
/* harmony export */   finalizeIssue: function() { return /* binding */ finalizeIssue; },
/* harmony export */   floatSafeRemainder: function() { return /* binding */ floatSafeRemainder; },
/* harmony export */   getElementAtPath: function() { return /* binding */ getElementAtPath; },
/* harmony export */   getEnumValues: function() { return /* binding */ getEnumValues; },
/* harmony export */   getLengthableOrigin: function() { return /* binding */ getLengthableOrigin; },
/* harmony export */   getParsedType: function() { return /* binding */ getParsedType; },
/* harmony export */   getSizableOrigin: function() { return /* binding */ getSizableOrigin; },
/* harmony export */   isObject: function() { return /* binding */ isObject; },
/* harmony export */   isPlainObject: function() { return /* binding */ isPlainObject; },
/* harmony export */   issue: function() { return /* binding */ issue; },
/* harmony export */   joinValues: function() { return /* binding */ joinValues; },
/* harmony export */   jsonStringifyReplacer: function() { return /* binding */ jsonStringifyReplacer; },
/* harmony export */   merge: function() { return /* binding */ merge; },
/* harmony export */   normalizeParams: function() { return /* binding */ normalizeParams; },
/* harmony export */   nullish: function() { return /* binding */ nullish; },
/* harmony export */   numKeys: function() { return /* binding */ numKeys; },
/* harmony export */   omit: function() { return /* binding */ omit; },
/* harmony export */   optionalKeys: function() { return /* binding */ optionalKeys; },
/* harmony export */   partial: function() { return /* binding */ partial; },
/* harmony export */   pick: function() { return /* binding */ pick; },
/* harmony export */   prefixIssues: function() { return /* binding */ prefixIssues; },
/* harmony export */   primitiveTypes: function() { return /* binding */ primitiveTypes; },
/* harmony export */   promiseAllObject: function() { return /* binding */ promiseAllObject; },
/* harmony export */   propertyKeyTypes: function() { return /* binding */ propertyKeyTypes; },
/* harmony export */   randomString: function() { return /* binding */ randomString; },
/* harmony export */   required: function() { return /* binding */ required; },
/* harmony export */   stringifyPrimitive: function() { return /* binding */ stringifyPrimitive; },
/* harmony export */   unwrapMessage: function() { return /* binding */ unwrapMessage; }
/* harmony export */ });
// functions
function assertEqual(val) {
    return val;
}
function assertNotEqual(val) {
    return val;
}
function assertIs(_arg) { }
function assertNever(_x) {
    throw new Error();
}
function assert(_) { }
function getEnumValues(entries) {
    const numericValues = Object.values(entries).filter((v) => typeof v === "number");
    const values = Object.entries(entries)
        .filter(([k, _]) => numericValues.indexOf(+k) === -1)
        .map(([_, v]) => v);
    return values;
}
function joinValues(array, separator = "|") {
    return array.map((val) => stringifyPrimitive(val)).join(separator);
}
function jsonStringifyReplacer(_, value) {
    if (typeof value === "bigint")
        return value.toString();
    return value;
}
function cached(getter) {
    const set = false;
    return {
        get value() {
            if (!set) {
                const value = getter();
                Object.defineProperty(this, "value", { value });
                return value;
            }
            throw new Error("cached value already set");
        },
    };
}
function nullish(input) {
    return input === null || input === undefined;
}
function cleanRegex(source) {
    const start = source.startsWith("^") ? 1 : 0;
    const end = source.endsWith("$") ? source.length - 1 : source.length;
    return source.slice(start, end);
}
function floatSafeRemainder(val, step) {
    const valDecCount = (val.toString().split(".")[1] || "").length;
    const stepDecCount = (step.toString().split(".")[1] || "").length;
    const decCount = valDecCount > stepDecCount ? valDecCount : stepDecCount;
    const valInt = Number.parseInt(val.toFixed(decCount).replace(".", ""));
    const stepInt = Number.parseInt(step.toFixed(decCount).replace(".", ""));
    return (valInt % stepInt) / 10 ** decCount;
}
function defineLazy(object, key, getter) {
    const set = false;
    Object.defineProperty(object, key, {
        get() {
            if (!set) {
                const value = getter();
                object[key] = value;
                return value;
            }
            throw new Error("cached value already set");
        },
        set(v) {
            Object.defineProperty(object, key, {
                value: v,
                // configurable: true,
            });
            // object[key] = v;
        },
        configurable: true,
    });
}
function assignProp(target, prop, value) {
    Object.defineProperty(target, prop, {
        value,
        writable: true,
        enumerable: true,
        configurable: true,
    });
}
function getElementAtPath(obj, path) {
    if (!path)
        return obj;
    return path.reduce((acc, key) => acc?.[key], obj);
}
function promiseAllObject(promisesObj) {
    const keys = Object.keys(promisesObj);
    const promises = keys.map((key) => promisesObj[key]);
    return Promise.all(promises).then((results) => {
        const resolvedObj = {};
        for (let i = 0; i < keys.length; i++) {
            resolvedObj[keys[i]] = results[i];
        }
        return resolvedObj;
    });
}
function randomString(length = 10) {
    const chars = "abcdefghijklmnopqrstuvwxyz";
    let str = "";
    for (let i = 0; i < length; i++) {
        str += chars[Math.floor(Math.random() * chars.length)];
    }
    return str;
}
function esc(str) {
    return JSON.stringify(str);
}
const captureStackTrace = Error.captureStackTrace
    ? Error.captureStackTrace
    : (..._args) => { };
function isObject(data) {
    return typeof data === "object" && data !== null && !Array.isArray(data);
}
const allowsEval = cached(() => {
    if (typeof navigator !== "undefined" && navigator?.userAgent?.includes("Cloudflare")) {
        return false;
    }
    try {
        const F = Function;
        new F("");
        return true;
    }
    catch (_) {
        return false;
    }
});
function isPlainObject(o) {
    if (isObject(o) === false)
        return false;
    // modified constructor
    const ctor = o.constructor;
    if (ctor === undefined)
        return true;
    // modified prototype
    const prot = ctor.prototype;
    if (isObject(prot) === false)
        return false;
    // ctor doesn't have static `isPrototypeOf`
    if (Object.prototype.hasOwnProperty.call(prot, "isPrototypeOf") === false) {
        return false;
    }
    return true;
}
function numKeys(data) {
    let keyCount = 0;
    for (const key in data) {
        if (Object.prototype.hasOwnProperty.call(data, key)) {
            keyCount++;
        }
    }
    return keyCount;
}
const getParsedType = (data) => {
    const t = typeof data;
    switch (t) {
        case "undefined":
            return "undefined";
        case "string":
            return "string";
        case "number":
            return Number.isNaN(data) ? "nan" : "number";
        case "boolean":
            return "boolean";
        case "function":
            return "function";
        case "bigint":
            return "bigint";
        case "symbol":
            return "symbol";
        case "object":
            if (Array.isArray(data)) {
                return "array";
            }
            if (data === null) {
                return "null";
            }
            if (data.then && typeof data.then === "function" && data.catch && typeof data.catch === "function") {
                return "promise";
            }
            if (typeof Map !== "undefined" && data instanceof Map) {
                return "map";
            }
            if (typeof Set !== "undefined" && data instanceof Set) {
                return "set";
            }
            if (typeof Date !== "undefined" && data instanceof Date) {
                return "date";
            }
            if (typeof File !== "undefined" && data instanceof File) {
                return "file";
            }
            return "object";
        default:
            throw new Error(`Unknown data type: ${t}`);
    }
};
const propertyKeyTypes = new Set(["string", "number", "symbol"]);
const primitiveTypes = new Set(["string", "number", "bigint", "boolean", "symbol", "undefined"]);
function escapeRegex(str) {
    return str.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
}
// zod-specific utils
function clone(inst, def, params) {
    const cl = new inst._zod.constr(def ?? inst._zod.def);
    if (!def || params?.parent)
        cl._zod.parent = inst;
    return cl;
}
function normalizeParams(_params) {
    const params = _params;
    if (!params)
        return {};
    if (typeof params === "string")
        return { error: () => params };
    if (params?.message !== undefined) {
        if (params?.error !== undefined)
            throw new Error("Cannot specify both `message` and `error` params");
        params.error = params.message;
    }
    delete params.message;
    if (typeof params.error === "string")
        return { ...params, error: () => params.error };
    return params;
}
function createTransparentProxy(getter) {
    let target;
    return new Proxy({}, {
        get(_, prop, receiver) {
            target ?? (target = getter());
            return Reflect.get(target, prop, receiver);
        },
        set(_, prop, value, receiver) {
            target ?? (target = getter());
            return Reflect.set(target, prop, value, receiver);
        },
        has(_, prop) {
            target ?? (target = getter());
            return Reflect.has(target, prop);
        },
        deleteProperty(_, prop) {
            target ?? (target = getter());
            return Reflect.deleteProperty(target, prop);
        },
        ownKeys(_) {
            target ?? (target = getter());
            return Reflect.ownKeys(target);
        },
        getOwnPropertyDescriptor(_, prop) {
            target ?? (target = getter());
            return Reflect.getOwnPropertyDescriptor(target, prop);
        },
        defineProperty(_, prop, descriptor) {
            target ?? (target = getter());
            return Reflect.defineProperty(target, prop, descriptor);
        },
    });
}
function stringifyPrimitive(value) {
    if (typeof value === "bigint")
        return value.toString() + "n";
    if (typeof value === "string")
        return `"${value}"`;
    return `${value}`;
}
function optionalKeys(shape) {
    return Object.keys(shape).filter((k) => {
        return shape[k]._zod.optin === "optional" && shape[k]._zod.optout === "optional";
    });
}
const NUMBER_FORMAT_RANGES = {
    safeint: [Number.MIN_SAFE_INTEGER, Number.MAX_SAFE_INTEGER],
    int32: [-2147483648, 2147483647],
    uint32: [0, 4294967295],
    float32: [-3.4028234663852886e38, 3.4028234663852886e38],
    float64: [-Number.MAX_VALUE, Number.MAX_VALUE],
};
const BIGINT_FORMAT_RANGES = {
    int64: [/* @__PURE__*/ BigInt("-9223372036854775808"), /* @__PURE__*/ BigInt("9223372036854775807")],
    uint64: [/* @__PURE__*/ BigInt(0), /* @__PURE__*/ BigInt("18446744073709551615")],
};
function pick(schema, mask) {
    const newShape = {};
    const currDef = schema._zod.def; //.shape;
    for (const key in mask) {
        if (!(key in currDef.shape)) {
            throw new Error(`Unrecognized key: "${key}"`);
        }
        if (!mask[key])
            continue;
        // pick key
        newShape[key] = currDef.shape[key];
    }
    return clone(schema, {
        ...schema._zod.def,
        shape: newShape,
        checks: [],
    });
}
function omit(schema, mask) {
    const newShape = { ...schema._zod.def.shape };
    const currDef = schema._zod.def; //.shape;
    for (const key in mask) {
        if (!(key in currDef.shape)) {
            throw new Error(`Unrecognized key: "${key}"`);
        }
        if (!mask[key])
            continue;
        delete newShape[key];
    }
    return clone(schema, {
        ...schema._zod.def,
        shape: newShape,
        checks: [],
    });
}
function extend(schema, shape) {
    if (!isPlainObject(shape)) {
        throw new Error("Invalid input to extend: expected a plain object");
    }
    const def = {
        ...schema._zod.def,
        get shape() {
            const _shape = { ...schema._zod.def.shape, ...shape };
            assignProp(this, "shape", _shape); // self-caching
            return _shape;
        },
        checks: [], // delete existing checks
    };
    return clone(schema, def);
}
function merge(a, b) {
    return clone(a, {
        ...a._zod.def,
        get shape() {
            const _shape = { ...a._zod.def.shape, ...b._zod.def.shape };
            assignProp(this, "shape", _shape); // self-caching
            return _shape;
        },
        catchall: b._zod.def.catchall,
        checks: [], // delete existing checks
    });
}
function partial(Class, schema, mask) {
    const oldShape = schema._zod.def.shape;
    const shape = { ...oldShape };
    if (mask) {
        for (const key in mask) {
            if (!(key in oldShape)) {
                throw new Error(`Unrecognized key: "${key}"`);
            }
            if (!mask[key])
                continue;
            // if (oldShape[key]!._zod.optin === "optional") continue;
            shape[key] = Class
                ? new Class({
                    type: "optional",
                    innerType: oldShape[key],
                })
                : oldShape[key];
        }
    }
    else {
        for (const key in oldShape) {
            // if (oldShape[key]!._zod.optin === "optional") continue;
            shape[key] = Class
                ? new Class({
                    type: "optional",
                    innerType: oldShape[key],
                })
                : oldShape[key];
        }
    }
    return clone(schema, {
        ...schema._zod.def,
        shape,
        checks: [],
    });
}
function required(Class, schema, mask) {
    const oldShape = schema._zod.def.shape;
    const shape = { ...oldShape };
    if (mask) {
        for (const key in mask) {
            if (!(key in shape)) {
                throw new Error(`Unrecognized key: "${key}"`);
            }
            if (!mask[key])
                continue;
            // overwrite with non-optional
            shape[key] = new Class({
                type: "nonoptional",
                innerType: oldShape[key],
            });
        }
    }
    else {
        for (const key in oldShape) {
            // overwrite with non-optional
            shape[key] = new Class({
                type: "nonoptional",
                innerType: oldShape[key],
            });
        }
    }
    return clone(schema, {
        ...schema._zod.def,
        shape,
        // optional: [],
        checks: [],
    });
}
function aborted(x, startIndex = 0) {
    for (let i = startIndex; i < x.issues.length; i++) {
        if (x.issues[i]?.continue !== true)
            return true;
    }
    return false;
}
function prefixIssues(path, issues) {
    return issues.map((iss) => {
        var _a;
        (_a = iss).path ?? (_a.path = []);
        iss.path.unshift(path);
        return iss;
    });
}
function unwrapMessage(message) {
    return typeof message === "string" ? message : message?.message;
}
function finalizeIssue(iss, ctx, config) {
    const full = { ...iss, path: iss.path ?? [] };
    // for backwards compatibility
    if (!iss.message) {
        const message = unwrapMessage(iss.inst?._zod.def?.error?.(iss)) ??
            unwrapMessage(ctx?.error?.(iss)) ??
            unwrapMessage(config.customError?.(iss)) ??
            unwrapMessage(config.localeError?.(iss)) ??
            "Invalid input";
        full.message = message;
    }
    // delete (full as any).def;
    delete full.inst;
    delete full.continue;
    if (!ctx?.reportInput) {
        delete full.input;
    }
    return full;
}
function getSizableOrigin(input) {
    if (input instanceof Set)
        return "set";
    if (input instanceof Map)
        return "map";
    if (input instanceof File)
        return "file";
    return "unknown";
}
function getLengthableOrigin(input) {
    if (Array.isArray(input))
        return "array";
    if (typeof input === "string")
        return "string";
    return "unknown";
}
function issue(...args) {
    const [iss, input, inst] = args;
    if (typeof iss === "string") {
        return {
            message: iss,
            code: "custom",
            input,
            inst,
        };
    }
    return { ...iss };
}
function cleanEnum(obj) {
    return Object.entries(obj)
        .filter(([k, _]) => {
        // return true if NaN, meaning it's not a number, thus a string key
        return Number.isNaN(Number.parseInt(k, 10));
    })
        .map((el) => el[1]);
}
// instanceof
class Class {
    constructor(..._args) { }
}


/***/ }),

/***/ "./node_modules/zod/v4/core/versions.js":
/*!**********************************************!*\
  !*** ./node_modules/zod/v4/core/versions.js ***!
  \**********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   version: function() { return /* binding */ version; }
/* harmony export */ });
const version = {
    major: 4,
    minor: 0,
    patch: 0,
};


/***/ }),

/***/ "./node_modules/zod/v4/locales/ar.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/ar.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "حرف", verb: "أن يحوي" },
        file: { unit: "بايت", verb: "أن يحوي" },
        array: { unit: "عنصر", verb: "أن يحوي" },
        set: { unit: "عنصر", verb: "أن يحوي" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "number";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "array";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "مدخل",
        email: "بريد إلكتروني",
        url: "رابط",
        emoji: "إيموجي",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "تاريخ ووقت بمعيار ISO",
        date: "تاريخ بمعيار ISO",
        time: "وقت بمعيار ISO",
        duration: "مدة بمعيار ISO",
        ipv4: "عنوان IPv4",
        ipv6: "عنوان IPv6",
        cidrv4: "مدى عناوين بصيغة IPv4",
        cidrv6: "مدى عناوين بصيغة IPv6",
        base64: "نَص بترميز base64-encoded",
        base64url: "نَص بترميز base64url-encoded",
        json_string: "نَص على هيئة JSON",
        e164: "رقم هاتف بمعيار E.164",
        jwt: "JWT",
        template_literal: "مدخل",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `مدخلات غير مقبولة: يفترض إدخال ${issue.expected}، ولكن تم إدخال ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `مدخلات غير مقبولة: يفترض إدخال ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `اختيار غير مقبول: يتوقع انتقاء أحد هذه الخيارات: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return ` أكبر من اللازم: يفترض أن تكون ${issue.origin ?? "القيمة"} ${adj} ${issue.maximum.toString()} ${sizing.unit ?? "عنصر"}`;
                return `أكبر من اللازم: يفترض أن تكون ${issue.origin ?? "القيمة"} ${adj} ${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `أصغر من اللازم: يفترض لـ ${issue.origin} أن يكون ${adj} ${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `أصغر من اللازم: يفترض لـ ${issue.origin} أن يكون ${adj} ${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `نَص غير مقبول: يجب أن يبدأ بـ "${issue.prefix}"`;
                if (_issue.format === "ends_with")
                    return `نَص غير مقبول: يجب أن ينتهي بـ "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `نَص غير مقبول: يجب أن يتضمَّن "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `نَص غير مقبول: يجب أن يطابق النمط ${_issue.pattern}`;
                return `${Nouns[_issue.format] ?? issue.format} غير مقبول`;
            }
            case "not_multiple_of":
                return `رقم غير مقبول: يجب أن يكون من مضاعفات ${issue.divisor}`;
            case "unrecognized_keys":
                return `معرف${issue.keys.length > 1 ? "ات" : ""} غريب${issue.keys.length > 1 ? "ة" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, "، ")}`;
            case "invalid_key":
                return `معرف غير مقبول في ${issue.origin}`;
            case "invalid_union":
                return "مدخل غير مقبول";
            case "invalid_element":
                return `مدخل غير مقبول في ${issue.origin}`;
            default:
                return "مدخل غير مقبول";
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/az.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/az.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "simvol", verb: "olmalıdır" },
        file: { unit: "bayt", verb: "olmalıdır" },
        array: { unit: "element", verb: "olmalıdır" },
        set: { unit: "element", verb: "olmalıdır" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "number";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "array";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "input",
        email: "email address",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO datetime",
        date: "ISO date",
        time: "ISO time",
        duration: "ISO duration",
        ipv4: "IPv4 address",
        ipv6: "IPv6 address",
        cidrv4: "IPv4 range",
        cidrv6: "IPv6 range",
        base64: "base64-encoded string",
        base64url: "base64url-encoded string",
        json_string: "JSON string",
        e164: "E.164 number",
        jwt: "JWT",
        template_literal: "input",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Yanlış dəyər: gözlənilən ${issue.expected}, daxil olan ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Yanlış dəyər: gözlənilən ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Yanlış seçim: aşağıdakılardan biri olmalıdır: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Çox böyük: gözlənilən ${issue.origin ?? "dəyər"} ${adj}${issue.maximum.toString()} ${sizing.unit ?? "element"}`;
                return `Çox böyük: gözlənilən ${issue.origin ?? "dəyər"} ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Çox kiçik: gözlənilən ${issue.origin} ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                return `Çox kiçik: gözlənilən ${issue.origin} ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Yanlış mətn: "${_issue.prefix}" ilə başlamalıdır`;
                if (_issue.format === "ends_with")
                    return `Yanlış mətn: "${_issue.suffix}" ilə bitməlidir`;
                if (_issue.format === "includes")
                    return `Yanlış mətn: "${_issue.includes}" daxil olmalıdır`;
                if (_issue.format === "regex")
                    return `Yanlış mətn: ${_issue.pattern} şablonuna uyğun olmalıdır`;
                return `Yanlış ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Yanlış ədəd: ${issue.divisor} ilə bölünə bilən olmalıdır`;
            case "unrecognized_keys":
                return `Tanınmayan açar${issue.keys.length > 1 ? "lar" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `${issue.origin} daxilində yanlış açar`;
            case "invalid_union":
                return "Yanlış dəyər";
            case "invalid_element":
                return `${issue.origin} daxilində yanlış dəyər`;
            default:
                return `Yanlış dəyər`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/be.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/be.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

function getBelarusianPlural(count, one, few, many) {
    const absCount = Math.abs(count);
    const lastDigit = absCount % 10;
    const lastTwoDigits = absCount % 100;
    if (lastTwoDigits >= 11 && lastTwoDigits <= 19) {
        return many;
    }
    if (lastDigit === 1) {
        return one;
    }
    if (lastDigit >= 2 && lastDigit <= 4) {
        return few;
    }
    return many;
}
const error = () => {
    const Sizable = {
        string: {
            unit: {
                one: "сімвал",
                few: "сімвалы",
                many: "сімвалаў",
            },
            verb: "мець",
        },
        array: {
            unit: {
                one: "элемент",
                few: "элементы",
                many: "элементаў",
            },
            verb: "мець",
        },
        set: {
            unit: {
                one: "элемент",
                few: "элементы",
                many: "элементаў",
            },
            verb: "мець",
        },
        file: {
            unit: {
                one: "байт",
                few: "байты",
                many: "байтаў",
            },
            verb: "мець",
        },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "лік";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "масіў";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "увод",
        email: "email адрас",
        url: "URL",
        emoji: "эмодзі",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO дата і час",
        date: "ISO дата",
        time: "ISO час",
        duration: "ISO працягласць",
        ipv4: "IPv4 адрас",
        ipv6: "IPv6 адрас",
        cidrv4: "IPv4 дыяпазон",
        cidrv6: "IPv6 дыяпазон",
        base64: "радок у фармаце base64",
        base64url: "радок у фармаце base64url",
        json_string: "JSON радок",
        e164: "нумар E.164",
        jwt: "JWT",
        template_literal: "увод",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Няправільны ўвод: чакаўся ${issue.expected}, атрымана ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Няправільны ўвод: чакалася ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Няправільны варыянт: чакаўся адзін з ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    const maxValue = Number(issue.maximum);
                    const unit = getBelarusianPlural(maxValue, sizing.unit.one, sizing.unit.few, sizing.unit.many);
                    return `Занадта вялікі: чакалася, што ${issue.origin ?? "значэнне"} павінна ${sizing.verb} ${adj}${issue.maximum.toString()} ${unit}`;
                }
                return `Занадта вялікі: чакалася, што ${issue.origin ?? "значэнне"} павінна быць ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    const minValue = Number(issue.minimum);
                    const unit = getBelarusianPlural(minValue, sizing.unit.one, sizing.unit.few, sizing.unit.many);
                    return `Занадта малы: чакалася, што ${issue.origin} павінна ${sizing.verb} ${adj}${issue.minimum.toString()} ${unit}`;
                }
                return `Занадта малы: чакалася, што ${issue.origin} павінна быць ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Няправільны радок: павінен пачынацца з "${_issue.prefix}"`;
                if (_issue.format === "ends_with")
                    return `Няправільны радок: павінен заканчвацца на "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Няправільны радок: павінен змяшчаць "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Няправільны радок: павінен адпавядаць шаблону ${_issue.pattern}`;
                return `Няправільны ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Няправільны лік: павінен быць кратным ${issue.divisor}`;
            case "unrecognized_keys":
                return `Нераспазнаны ${issue.keys.length > 1 ? "ключы" : "ключ"}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Няправільны ключ у ${issue.origin}`;
            case "invalid_union":
                return "Няправільны ўвод";
            case "invalid_element":
                return `Няправільнае значэнне ў ${issue.origin}`;
            default:
                return `Няправільны ўвод`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/ca.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/ca.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "caràcters", verb: "contenir" },
        file: { unit: "bytes", verb: "contenir" },
        array: { unit: "elements", verb: "contenir" },
        set: { unit: "elements", verb: "contenir" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "number";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "array";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "entrada",
        email: "adreça electrònica",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "data i hora ISO",
        date: "data ISO",
        time: "hora ISO",
        duration: "durada ISO",
        ipv4: "adreça IPv4",
        ipv6: "adreça IPv6",
        cidrv4: "rang IPv4",
        cidrv6: "rang IPv6",
        base64: "cadena codificada en base64",
        base64url: "cadena codificada en base64url",
        json_string: "cadena JSON",
        e164: "número E.164",
        jwt: "JWT",
        template_literal: "entrada",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Tipus invàlid: s'esperava ${issue.expected}, s'ha rebut ${parsedType(issue.input)}`;
            // return `Tipus invàlid: s'esperava ${issue.expected}, s'ha rebut ${util.getParsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Valor invàlid: s'esperava ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Opció invàlida: s'esperava una de ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, " o ")}`;
            case "too_big": {
                const adj = issue.inclusive ? "com a màxim" : "menys de";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Massa gran: s'esperava que ${issue.origin ?? "el valor"} contingués ${adj} ${issue.maximum.toString()} ${sizing.unit ?? "elements"}`;
                return `Massa gran: s'esperava que ${issue.origin ?? "el valor"} fos ${adj} ${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? "com a mínim" : "més de";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Massa petit: s'esperava que ${issue.origin} contingués ${adj} ${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `Massa petit: s'esperava que ${issue.origin} fos ${adj} ${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with") {
                    return `Format invàlid: ha de començar amb "${_issue.prefix}"`;
                }
                if (_issue.format === "ends_with")
                    return `Format invàlid: ha d'acabar amb "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Format invàlid: ha d'incloure "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Format invàlid: ha de coincidir amb el patró ${_issue.pattern}`;
                return `Format invàlid per a ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Número invàlid: ha de ser múltiple de ${issue.divisor}`;
            case "unrecognized_keys":
                return `Clau${issue.keys.length > 1 ? "s" : ""} no reconeguda${issue.keys.length > 1 ? "s" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Clau invàlida a ${issue.origin}`;
            case "invalid_union":
                return "Entrada invàlida"; // Could also be "Tipus d'unió invàlid" but "Entrada invàlida" is more general
            case "invalid_element":
                return `Element invàlid a ${issue.origin}`;
            default:
                return `Entrada invàlida`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/cs.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/cs.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "znaků", verb: "mít" },
        file: { unit: "bajtů", verb: "mít" },
        array: { unit: "prvků", verb: "mít" },
        set: { unit: "prvků", verb: "mít" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "číslo";
            }
            case "string": {
                return "řetězec";
            }
            case "boolean": {
                return "boolean";
            }
            case "bigint": {
                return "bigint";
            }
            case "function": {
                return "funkce";
            }
            case "symbol": {
                return "symbol";
            }
            case "undefined": {
                return "undefined";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "pole";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "regulární výraz",
        email: "e-mailová adresa",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "datum a čas ve formátu ISO",
        date: "datum ve formátu ISO",
        time: "čas ve formátu ISO",
        duration: "doba trvání ISO",
        ipv4: "IPv4 adresa",
        ipv6: "IPv6 adresa",
        cidrv4: "rozsah IPv4",
        cidrv6: "rozsah IPv6",
        base64: "řetězec zakódovaný ve formátu base64",
        base64url: "řetězec zakódovaný ve formátu base64url",
        json_string: "řetězec ve formátu JSON",
        e164: "číslo E.164",
        jwt: "JWT",
        template_literal: "vstup",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Neplatný vstup: očekáváno ${issue.expected}, obdrženo ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Neplatný vstup: očekáváno ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Neplatná možnost: očekávána jedna z hodnot ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Hodnota je příliš velká: ${issue.origin ?? "hodnota"} musí mít ${adj}${issue.maximum.toString()} ${sizing.unit ?? "prvků"}`;
                }
                return `Hodnota je příliš velká: ${issue.origin ?? "hodnota"} musí být ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Hodnota je příliš malá: ${issue.origin ?? "hodnota"} musí mít ${adj}${issue.minimum.toString()} ${sizing.unit ?? "prvků"}`;
                }
                return `Hodnota je příliš malá: ${issue.origin ?? "hodnota"} musí být ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Neplatný řetězec: musí začínat na "${_issue.prefix}"`;
                if (_issue.format === "ends_with")
                    return `Neplatný řetězec: musí končit na "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Neplatný řetězec: musí obsahovat "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Neplatný řetězec: musí odpovídat vzoru ${_issue.pattern}`;
                return `Neplatný formát ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Neplatné číslo: musí být násobkem ${issue.divisor}`;
            case "unrecognized_keys":
                return `Neznámé klíče: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Neplatný klíč v ${issue.origin}`;
            case "invalid_union":
                return "Neplatný vstup";
            case "invalid_element":
                return `Neplatná hodnota v ${issue.origin}`;
            default:
                return `Neplatný vstup`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/de.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/de.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "Zeichen", verb: "zu haben" },
        file: { unit: "Bytes", verb: "zu haben" },
        array: { unit: "Elemente", verb: "zu haben" },
        set: { unit: "Elemente", verb: "zu haben" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "Zahl";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "Array";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "Eingabe",
        email: "E-Mail-Adresse",
        url: "URL",
        emoji: "Emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO-Datum und -Uhrzeit",
        date: "ISO-Datum",
        time: "ISO-Uhrzeit",
        duration: "ISO-Dauer",
        ipv4: "IPv4-Adresse",
        ipv6: "IPv6-Adresse",
        cidrv4: "IPv4-Bereich",
        cidrv6: "IPv6-Bereich",
        base64: "Base64-codierter String",
        base64url: "Base64-URL-codierter String",
        json_string: "JSON-String",
        e164: "E.164-Nummer",
        jwt: "JWT",
        template_literal: "Eingabe",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Ungültige Eingabe: erwartet ${issue.expected}, erhalten ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Ungültige Eingabe: erwartet ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Ungültige Option: erwartet eine von ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Zu groß: erwartet, dass ${issue.origin ?? "Wert"} ${adj}${issue.maximum.toString()} ${sizing.unit ?? "Elemente"} hat`;
                return `Zu groß: erwartet, dass ${issue.origin ?? "Wert"} ${adj}${issue.maximum.toString()} ist`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Zu klein: erwartet, dass ${issue.origin} ${adj}${issue.minimum.toString()} ${sizing.unit} hat`;
                }
                return `Zu klein: erwartet, dass ${issue.origin} ${adj}${issue.minimum.toString()} ist`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Ungültiger String: muss mit "${_issue.prefix}" beginnen`;
                if (_issue.format === "ends_with")
                    return `Ungültiger String: muss mit "${_issue.suffix}" enden`;
                if (_issue.format === "includes")
                    return `Ungültiger String: muss "${_issue.includes}" enthalten`;
                if (_issue.format === "regex")
                    return `Ungültiger String: muss dem Muster ${_issue.pattern} entsprechen`;
                return `Ungültig: ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Ungültige Zahl: muss ein Vielfaches von ${issue.divisor} sein`;
            case "unrecognized_keys":
                return `${issue.keys.length > 1 ? "Unbekannte Schlüssel" : "Unbekannter Schlüssel"}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Ungültiger Schlüssel in ${issue.origin}`;
            case "invalid_union":
                return "Ungültige Eingabe";
            case "invalid_element":
                return `Ungültiger Wert in ${issue.origin}`;
            default:
                return `Ungültige Eingabe`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/en.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/en.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; },
/* harmony export */   parsedType: function() { return /* binding */ parsedType; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const parsedType = (data) => {
    const t = typeof data;
    switch (t) {
        case "number": {
            return Number.isNaN(data) ? "NaN" : "number";
        }
        case "object": {
            if (Array.isArray(data)) {
                return "array";
            }
            if (data === null) {
                return "null";
            }
            if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                return data.constructor.name;
            }
        }
    }
    return t;
};
const error = () => {
    const Sizable = {
        string: { unit: "characters", verb: "to have" },
        file: { unit: "bytes", verb: "to have" },
        array: { unit: "items", verb: "to have" },
        set: { unit: "items", verb: "to have" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const Nouns = {
        regex: "input",
        email: "email address",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO datetime",
        date: "ISO date",
        time: "ISO time",
        duration: "ISO duration",
        ipv4: "IPv4 address",
        ipv6: "IPv6 address",
        cidrv4: "IPv4 range",
        cidrv6: "IPv6 range",
        base64: "base64-encoded string",
        base64url: "base64url-encoded string",
        json_string: "JSON string",
        e164: "E.164 number",
        jwt: "JWT",
        template_literal: "input",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Invalid input: expected ${issue.expected}, received ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Invalid input: expected ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Invalid option: expected one of ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Too big: expected ${issue.origin ?? "value"} to have ${adj}${issue.maximum.toString()} ${sizing.unit ?? "elements"}`;
                return `Too big: expected ${issue.origin ?? "value"} to be ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Too small: expected ${issue.origin} to have ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `Too small: expected ${issue.origin} to be ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with") {
                    return `Invalid string: must start with "${_issue.prefix}"`;
                }
                if (_issue.format === "ends_with")
                    return `Invalid string: must end with "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Invalid string: must include "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Invalid string: must match pattern ${_issue.pattern}`;
                return `Invalid ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Invalid number: must be a multiple of ${issue.divisor}`;
            case "unrecognized_keys":
                return `Unrecognized key${issue.keys.length > 1 ? "s" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Invalid key in ${issue.origin}`;
            case "invalid_union":
                return "Invalid input";
            case "invalid_element":
                return `Invalid value in ${issue.origin}`;
            default:
                return `Invalid input`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/eo.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/eo.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; },
/* harmony export */   parsedType: function() { return /* binding */ parsedType; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const parsedType = (data) => {
    const t = typeof data;
    switch (t) {
        case "number": {
            return Number.isNaN(data) ? "NaN" : "nombro";
        }
        case "object": {
            if (Array.isArray(data)) {
                return "tabelo";
            }
            if (data === null) {
                return "senvalora";
            }
            if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                return data.constructor.name;
            }
        }
    }
    return t;
};
const error = () => {
    const Sizable = {
        string: { unit: "karaktrojn", verb: "havi" },
        file: { unit: "bajtojn", verb: "havi" },
        array: { unit: "elementojn", verb: "havi" },
        set: { unit: "elementojn", verb: "havi" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const Nouns = {
        regex: "enigo",
        email: "retadreso",
        url: "URL",
        emoji: "emoĝio",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO-datotempo",
        date: "ISO-dato",
        time: "ISO-tempo",
        duration: "ISO-daŭro",
        ipv4: "IPv4-adreso",
        ipv6: "IPv6-adreso",
        cidrv4: "IPv4-rango",
        cidrv6: "IPv6-rango",
        base64: "64-ume kodita karaktraro",
        base64url: "URL-64-ume kodita karaktraro",
        json_string: "JSON-karaktraro",
        e164: "E.164-nombro",
        jwt: "JWT",
        template_literal: "enigo",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Nevalida enigo: atendiĝis ${issue.expected}, riceviĝis ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Nevalida enigo: atendiĝis ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Nevalida opcio: atendiĝis unu el ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Tro granda: atendiĝis ke ${issue.origin ?? "valoro"} havu ${adj}${issue.maximum.toString()} ${sizing.unit ?? "elementojn"}`;
                return `Tro granda: atendiĝis ke ${issue.origin ?? "valoro"} havu ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Tro malgranda: atendiĝis ke ${issue.origin} havu ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `Tro malgranda: atendiĝis ke ${issue.origin} estu ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Nevalida karaktraro: devas komenciĝi per "${_issue.prefix}"`;
                if (_issue.format === "ends_with")
                    return `Nevalida karaktraro: devas finiĝi per "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Nevalida karaktraro: devas inkluzivi "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Nevalida karaktraro: devas kongrui kun la modelo ${_issue.pattern}`;
                return `Nevalida ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Nevalida nombro: devas esti oblo de ${issue.divisor}`;
            case "unrecognized_keys":
                return `Nekonata${issue.keys.length > 1 ? "j" : ""} ŝlosilo${issue.keys.length > 1 ? "j" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Nevalida ŝlosilo en ${issue.origin}`;
            case "invalid_union":
                return "Nevalida enigo";
            case "invalid_element":
                return `Nevalida valoro en ${issue.origin}`;
            default:
                return `Nevalida enigo`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/es.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/es.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "caracteres", verb: "tener" },
        file: { unit: "bytes", verb: "tener" },
        array: { unit: "elementos", verb: "tener" },
        set: { unit: "elementos", verb: "tener" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "número";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "arreglo";
                }
                if (data === null) {
                    return "nulo";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "entrada",
        email: "dirección de correo electrónico",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "fecha y hora ISO",
        date: "fecha ISO",
        time: "hora ISO",
        duration: "duración ISO",
        ipv4: "dirección IPv4",
        ipv6: "dirección IPv6",
        cidrv4: "rango IPv4",
        cidrv6: "rango IPv6",
        base64: "cadena codificada en base64",
        base64url: "URL codificada en base64",
        json_string: "cadena JSON",
        e164: "número E.164",
        jwt: "JWT",
        template_literal: "entrada",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Entrada inválida: se esperaba ${issue.expected}, recibido ${parsedType(issue.input)}`;
            // return `Entrada inválida: se esperaba ${issue.expected}, recibido ${util.getParsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Entrada inválida: se esperaba ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Opción inválida: se esperaba una de ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Demasiado grande: se esperaba que ${issue.origin ?? "valor"} tuviera ${adj}${issue.maximum.toString()} ${sizing.unit ?? "elementos"}`;
                return `Demasiado grande: se esperaba que ${issue.origin ?? "valor"} fuera ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Demasiado pequeño: se esperaba que ${issue.origin} tuviera ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `Demasiado pequeño: se esperaba que ${issue.origin} fuera ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Cadena inválida: debe comenzar con "${_issue.prefix}"`;
                if (_issue.format === "ends_with")
                    return `Cadena inválida: debe terminar en "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Cadena inválida: debe incluir "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Cadena inválida: debe coincidir con el patrón ${_issue.pattern}`;
                return `Inválido ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Número inválido: debe ser múltiplo de ${issue.divisor}`;
            case "unrecognized_keys":
                return `Llave${issue.keys.length > 1 ? "s" : ""} desconocida${issue.keys.length > 1 ? "s" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Llave inválida en ${issue.origin}`;
            case "invalid_union":
                return "Entrada inválida";
            case "invalid_element":
                return `Valor inválido en ${issue.origin}`;
            default:
                return `Entrada inválida`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/fa.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/fa.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "کاراکتر", verb: "داشته باشد" },
        file: { unit: "بایت", verb: "داشته باشد" },
        array: { unit: "آیتم", verb: "داشته باشد" },
        set: { unit: "آیتم", verb: "داشته باشد" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "عدد";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "آرایه";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "ورودی",
        email: "آدرس ایمیل",
        url: "URL",
        emoji: "ایموجی",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "تاریخ و زمان ایزو",
        date: "تاریخ ایزو",
        time: "زمان ایزو",
        duration: "مدت زمان ایزو",
        ipv4: "IPv4 آدرس",
        ipv6: "IPv6 آدرس",
        cidrv4: "IPv4 دامنه",
        cidrv6: "IPv6 دامنه",
        base64: "base64-encoded رشته",
        base64url: "base64url-encoded رشته",
        json_string: "JSON رشته",
        e164: "E.164 عدد",
        jwt: "JWT",
        template_literal: "ورودی",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `ورودی نامعتبر: می‌بایست ${issue.expected} می‌بود، ${parsedType(issue.input)} دریافت شد`;
            case "invalid_value":
                if (issue.values.length === 1) {
                    return `ورودی نامعتبر: می‌بایست ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])} می‌بود`;
                }
                return `گزینه نامعتبر: می‌بایست یکی از ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")} می‌بود`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `خیلی بزرگ: ${issue.origin ?? "مقدار"} باید ${adj}${issue.maximum.toString()} ${sizing.unit ?? "عنصر"} باشد`;
                }
                return `خیلی بزرگ: ${issue.origin ?? "مقدار"} باید ${adj}${issue.maximum.toString()} باشد`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `خیلی کوچک: ${issue.origin} باید ${adj}${issue.minimum.toString()} ${sizing.unit} باشد`;
                }
                return `خیلی کوچک: ${issue.origin} باید ${adj}${issue.minimum.toString()} باشد`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with") {
                    return `رشته نامعتبر: باید با "${_issue.prefix}" شروع شود`;
                }
                if (_issue.format === "ends_with") {
                    return `رشته نامعتبر: باید با "${_issue.suffix}" تمام شود`;
                }
                if (_issue.format === "includes") {
                    return `رشته نامعتبر: باید شامل "${_issue.includes}" باشد`;
                }
                if (_issue.format === "regex") {
                    return `رشته نامعتبر: باید با الگوی ${_issue.pattern} مطابقت داشته باشد`;
                }
                return `${Nouns[_issue.format] ?? issue.format} نامعتبر`;
            }
            case "not_multiple_of":
                return `عدد نامعتبر: باید مضرب ${issue.divisor} باشد`;
            case "unrecognized_keys":
                return `کلید${issue.keys.length > 1 ? "های" : ""} ناشناس: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `کلید ناشناس در ${issue.origin}`;
            case "invalid_union":
                return `ورودی نامعتبر`;
            case "invalid_element":
                return `مقدار نامعتبر در ${issue.origin}`;
            default:
                return `ورودی نامعتبر`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/fi.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/fi.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "merkkiä", subject: "merkkijonon" },
        file: { unit: "tavua", subject: "tiedoston" },
        array: { unit: "alkiota", subject: "listan" },
        set: { unit: "alkiota", subject: "joukon" },
        number: { unit: "", subject: "luvun" },
        bigint: { unit: "", subject: "suuren kokonaisluvun" },
        int: { unit: "", subject: "kokonaisluvun" },
        date: { unit: "", subject: "päivämäärän" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "number";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "array";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "säännöllinen lauseke",
        email: "sähköpostiosoite",
        url: "URL-osoite",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO-aikaleima",
        date: "ISO-päivämäärä",
        time: "ISO-aika",
        duration: "ISO-kesto",
        ipv4: "IPv4-osoite",
        ipv6: "IPv6-osoite",
        cidrv4: "IPv4-alue",
        cidrv6: "IPv6-alue",
        base64: "base64-koodattu merkkijono",
        base64url: "base64url-koodattu merkkijono",
        json_string: "JSON-merkkijono",
        e164: "E.164-luku",
        jwt: "JWT",
        template_literal: "templaattimerkkijono",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Virheellinen tyyppi: odotettiin ${issue.expected}, oli ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Virheellinen syöte: täytyy olla ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Virheellinen valinta: täytyy olla yksi seuraavista: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Liian suuri: ${sizing.subject} täytyy olla ${adj}${issue.maximum.toString()} ${sizing.unit}`.trim();
                }
                return `Liian suuri: arvon täytyy olla ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Liian pieni: ${sizing.subject} täytyy olla ${adj}${issue.minimum.toString()} ${sizing.unit}`.trim();
                }
                return `Liian pieni: arvon täytyy olla ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Virheellinen syöte: täytyy alkaa "${_issue.prefix}"`;
                if (_issue.format === "ends_with")
                    return `Virheellinen syöte: täytyy loppua "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Virheellinen syöte: täytyy sisältää "${_issue.includes}"`;
                if (_issue.format === "regex") {
                    return `Virheellinen syöte: täytyy vastata säännöllistä lauseketta ${_issue.pattern}`;
                }
                return `Virheellinen ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Virheellinen luku: täytyy olla luvun ${issue.divisor} monikerta`;
            case "unrecognized_keys":
                return `${issue.keys.length > 1 ? "Tuntemattomat avaimet" : "Tuntematon avain"}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return "Virheellinen avain tietueessa";
            case "invalid_union":
                return "Virheellinen unioni";
            case "invalid_element":
                return "Virheellinen arvo joukossa";
            default:
                return `Virheellinen syöte`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/fr-CA.js":
/*!**********************************************!*\
  !*** ./node_modules/zod/v4/locales/fr-CA.js ***!
  \**********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "caractères", verb: "avoir" },
        file: { unit: "octets", verb: "avoir" },
        array: { unit: "éléments", verb: "avoir" },
        set: { unit: "éléments", verb: "avoir" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "number";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "array";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "entrée",
        email: "adresse courriel",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "date-heure ISO",
        date: "date ISO",
        time: "heure ISO",
        duration: "durée ISO",
        ipv4: "adresse IPv4",
        ipv6: "adresse IPv6",
        cidrv4: "plage IPv4",
        cidrv6: "plage IPv6",
        base64: "chaîne encodée en base64",
        base64url: "chaîne encodée en base64url",
        json_string: "chaîne JSON",
        e164: "numéro E.164",
        jwt: "JWT",
        template_literal: "entrée",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Entrée invalide : attendu ${issue.expected}, reçu ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Entrée invalide : attendu ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Option invalide : attendu l'une des valeurs suivantes ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "≤" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Trop grand : attendu que ${issue.origin ?? "la valeur"} ait ${adj}${issue.maximum.toString()} ${sizing.unit}`;
                return `Trop grand : attendu que ${issue.origin ?? "la valeur"} soit ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? "≥" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Trop petit : attendu que ${issue.origin} ait ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `Trop petit : attendu que ${issue.origin} soit ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with") {
                    return `Chaîne invalide : doit commencer par "${_issue.prefix}"`;
                }
                if (_issue.format === "ends_with")
                    return `Chaîne invalide : doit se terminer par "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Chaîne invalide : doit inclure "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Chaîne invalide : doit correspondre au motif ${_issue.pattern}`;
                return `${Nouns[_issue.format] ?? issue.format} invalide`;
            }
            case "not_multiple_of":
                return `Nombre invalide : doit être un multiple de ${issue.divisor}`;
            case "unrecognized_keys":
                return `Clé${issue.keys.length > 1 ? "s" : ""} non reconnue${issue.keys.length > 1 ? "s" : ""} : ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Clé invalide dans ${issue.origin}`;
            case "invalid_union":
                return "Entrée invalide";
            case "invalid_element":
                return `Valeur invalide dans ${issue.origin}`;
            default:
                return `Entrée invalide`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/fr.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/fr.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "caractères", verb: "avoir" },
        file: { unit: "octets", verb: "avoir" },
        array: { unit: "éléments", verb: "avoir" },
        set: { unit: "éléments", verb: "avoir" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "nombre";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "tableau";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "entrée",
        email: "adresse e-mail",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "date et heure ISO",
        date: "date ISO",
        time: "heure ISO",
        duration: "durée ISO",
        ipv4: "adresse IPv4",
        ipv6: "adresse IPv6",
        cidrv4: "plage IPv4",
        cidrv6: "plage IPv6",
        base64: "chaîne encodée en base64",
        base64url: "chaîne encodée en base64url",
        json_string: "chaîne JSON",
        e164: "numéro E.164",
        jwt: "JWT",
        template_literal: "entrée",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Entrée invalide : ${issue.expected} attendu, ${parsedType(issue.input)} reçu`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Entrée invalide : ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])} attendu`;
                return `Option invalide : une valeur parmi ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")} attendue`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Trop grand : ${issue.origin ?? "valeur"} doit ${sizing.verb} ${adj}${issue.maximum.toString()} ${sizing.unit ?? "élément(s)"}`;
                return `Trop grand : ${issue.origin ?? "valeur"} doit être ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Trop petit : ${issue.origin} doit ${sizing.verb} ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `Trop petit : ${issue.origin} doit être ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Chaîne invalide : doit commencer par "${_issue.prefix}"`;
                if (_issue.format === "ends_with")
                    return `Chaîne invalide : doit se terminer par "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Chaîne invalide : doit inclure "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Chaîne invalide : doit correspondre au modèle ${_issue.pattern}`;
                return `${Nouns[_issue.format] ?? issue.format} invalide`;
            }
            case "not_multiple_of":
                return `Nombre invalide : doit être un multiple de ${issue.divisor}`;
            case "unrecognized_keys":
                return `Clé${issue.keys.length > 1 ? "s" : ""} non reconnue${issue.keys.length > 1 ? "s" : ""} : ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Clé invalide dans ${issue.origin}`;
            case "invalid_union":
                return "Entrée invalide";
            case "invalid_element":
                return `Valeur invalide dans ${issue.origin}`;
            default:
                return `Entrée invalide`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/he.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/he.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "אותיות", verb: "לכלול" },
        file: { unit: "בייטים", verb: "לכלול" },
        array: { unit: "פריטים", verb: "לכלול" },
        set: { unit: "פריטים", verb: "לכלול" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "number";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "array";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "קלט",
        email: "כתובת אימייל",
        url: "כתובת רשת",
        emoji: "אימוג'י",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "תאריך וזמן ISO",
        date: "תאריך ISO",
        time: "זמן ISO",
        duration: "משך זמן ISO",
        ipv4: "כתובת IPv4",
        ipv6: "כתובת IPv6",
        cidrv4: "טווח IPv4",
        cidrv6: "טווח IPv6",
        base64: "מחרוזת בבסיס 64",
        base64url: "מחרוזת בבסיס 64 לכתובות רשת",
        json_string: "מחרוזת JSON",
        e164: "מספר E.164",
        jwt: "JWT",
        template_literal: "קלט",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `קלט לא תקין: צריך ${issue.expected}, התקבל ${parsedType(issue.input)}`;
            // return `Invalid input: expected ${issue.expected}, received ${util.getParsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `קלט לא תקין: צריך ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `קלט לא תקין: צריך אחת מהאפשרויות  ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `גדול מדי: ${issue.origin ?? "value"} צריך להיות ${adj}${issue.maximum.toString()} ${sizing.unit ?? "elements"}`;
                return `גדול מדי: ${issue.origin ?? "value"} צריך להיות ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `קטן מדי: ${issue.origin} צריך להיות ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `קטן מדי: ${issue.origin} צריך להיות ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `מחרוזת לא תקינה: חייבת להתחיל ב"${_issue.prefix}"`;
                if (_issue.format === "ends_with")
                    return `מחרוזת לא תקינה: חייבת להסתיים ב "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `מחרוזת לא תקינה: חייבת לכלול "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `מחרוזת לא תקינה: חייבת להתאים לתבנית ${_issue.pattern}`;
                return `${Nouns[_issue.format] ?? issue.format} לא תקין`;
            }
            case "not_multiple_of":
                return `מספר לא תקין: חייב להיות מכפלה של ${issue.divisor}`;
            case "unrecognized_keys":
                return `מפתח${issue.keys.length > 1 ? "ות" : ""} לא מזוה${issue.keys.length > 1 ? "ים" : "ה"}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `מפתח לא תקין ב${issue.origin}`;
            case "invalid_union":
                return "קלט לא תקין";
            case "invalid_element":
                return `ערך לא תקין ב${issue.origin}`;
            default:
                return `קלט לא תקין`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/hu.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/hu.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "karakter", verb: "legyen" },
        file: { unit: "byte", verb: "legyen" },
        array: { unit: "elem", verb: "legyen" },
        set: { unit: "elem", verb: "legyen" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "szám";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "tömb";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "bemenet",
        email: "email cím",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO időbélyeg",
        date: "ISO dátum",
        time: "ISO idő",
        duration: "ISO időintervallum",
        ipv4: "IPv4 cím",
        ipv6: "IPv6 cím",
        cidrv4: "IPv4 tartomány",
        cidrv6: "IPv6 tartomány",
        base64: "base64-kódolt string",
        base64url: "base64url-kódolt string",
        json_string: "JSON string",
        e164: "E.164 szám",
        jwt: "JWT",
        template_literal: "bemenet",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Érvénytelen bemenet: a várt érték ${issue.expected}, a kapott érték ${parsedType(issue.input)}`;
            // return `Invalid input: expected ${issue.expected}, received ${util.getParsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Érvénytelen bemenet: a várt érték ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Érvénytelen opció: valamelyik érték várt ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Túl nagy: ${issue.origin ?? "érték"} mérete túl nagy ${adj}${issue.maximum.toString()} ${sizing.unit ?? "elem"}`;
                return `Túl nagy: a bemeneti érték ${issue.origin ?? "érték"} túl nagy: ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Túl kicsi: a bemeneti érték ${issue.origin} mérete túl kicsi ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `Túl kicsi: a bemeneti érték ${issue.origin} túl kicsi ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Érvénytelen string: "${_issue.prefix}" értékkel kell kezdődnie`;
                if (_issue.format === "ends_with")
                    return `Érvénytelen string: "${_issue.suffix}" értékkel kell végződnie`;
                if (_issue.format === "includes")
                    return `Érvénytelen string: "${_issue.includes}" értéket kell tartalmaznia`;
                if (_issue.format === "regex")
                    return `Érvénytelen string: ${_issue.pattern} mintának kell megfelelnie`;
                return `Érvénytelen ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Érvénytelen szám: ${issue.divisor} többszörösének kell lennie`;
            case "unrecognized_keys":
                return `Ismeretlen kulcs${issue.keys.length > 1 ? "s" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Érvénytelen kulcs ${issue.origin}`;
            case "invalid_union":
                return "Érvénytelen bemenet";
            case "invalid_element":
                return `Érvénytelen érték: ${issue.origin}`;
            default:
                return `Érvénytelen bemenet`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/id.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/id.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "karakter", verb: "memiliki" },
        file: { unit: "byte", verb: "memiliki" },
        array: { unit: "item", verb: "memiliki" },
        set: { unit: "item", verb: "memiliki" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "number";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "array";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "input",
        email: "alamat email",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "tanggal dan waktu format ISO",
        date: "tanggal format ISO",
        time: "jam format ISO",
        duration: "durasi format ISO",
        ipv4: "alamat IPv4",
        ipv6: "alamat IPv6",
        cidrv4: "rentang alamat IPv4",
        cidrv6: "rentang alamat IPv6",
        base64: "string dengan enkode base64",
        base64url: "string dengan enkode base64url",
        json_string: "string JSON",
        e164: "angka E.164",
        jwt: "JWT",
        template_literal: "input",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Input tidak valid: diharapkan ${issue.expected}, diterima ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Input tidak valid: diharapkan ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Pilihan tidak valid: diharapkan salah satu dari ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Terlalu besar: diharapkan ${issue.origin ?? "value"} memiliki ${adj}${issue.maximum.toString()} ${sizing.unit ?? "elemen"}`;
                return `Terlalu besar: diharapkan ${issue.origin ?? "value"} menjadi ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Terlalu kecil: diharapkan ${issue.origin} memiliki ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `Terlalu kecil: diharapkan ${issue.origin} menjadi ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `String tidak valid: harus dimulai dengan "${_issue.prefix}"`;
                if (_issue.format === "ends_with")
                    return `String tidak valid: harus berakhir dengan "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `String tidak valid: harus menyertakan "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `String tidak valid: harus sesuai pola ${_issue.pattern}`;
                return `${Nouns[_issue.format] ?? issue.format} tidak valid`;
            }
            case "not_multiple_of":
                return `Angka tidak valid: harus kelipatan dari ${issue.divisor}`;
            case "unrecognized_keys":
                return `Kunci tidak dikenali ${issue.keys.length > 1 ? "s" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Kunci tidak valid di ${issue.origin}`;
            case "invalid_union":
                return "Input tidak valid";
            case "invalid_element":
                return `Nilai tidak valid di ${issue.origin}`;
            default:
                return `Input tidak valid`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/index.js":
/*!**********************************************!*\
  !*** ./node_modules/zod/v4/locales/index.js ***!
  \**********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ar: function() { return /* reexport safe */ _ar_js__WEBPACK_IMPORTED_MODULE_0__["default"]; },
/* harmony export */   az: function() { return /* reexport safe */ _az_js__WEBPACK_IMPORTED_MODULE_1__["default"]; },
/* harmony export */   be: function() { return /* reexport safe */ _be_js__WEBPACK_IMPORTED_MODULE_2__["default"]; },
/* harmony export */   ca: function() { return /* reexport safe */ _ca_js__WEBPACK_IMPORTED_MODULE_3__["default"]; },
/* harmony export */   cs: function() { return /* reexport safe */ _cs_js__WEBPACK_IMPORTED_MODULE_4__["default"]; },
/* harmony export */   de: function() { return /* reexport safe */ _de_js__WEBPACK_IMPORTED_MODULE_5__["default"]; },
/* harmony export */   en: function() { return /* reexport safe */ _en_js__WEBPACK_IMPORTED_MODULE_6__["default"]; },
/* harmony export */   eo: function() { return /* reexport safe */ _eo_js__WEBPACK_IMPORTED_MODULE_7__["default"]; },
/* harmony export */   es: function() { return /* reexport safe */ _es_js__WEBPACK_IMPORTED_MODULE_8__["default"]; },
/* harmony export */   fa: function() { return /* reexport safe */ _fa_js__WEBPACK_IMPORTED_MODULE_9__["default"]; },
/* harmony export */   fi: function() { return /* reexport safe */ _fi_js__WEBPACK_IMPORTED_MODULE_10__["default"]; },
/* harmony export */   fr: function() { return /* reexport safe */ _fr_js__WEBPACK_IMPORTED_MODULE_11__["default"]; },
/* harmony export */   frCA: function() { return /* reexport safe */ _fr_CA_js__WEBPACK_IMPORTED_MODULE_12__["default"]; },
/* harmony export */   he: function() { return /* reexport safe */ _he_js__WEBPACK_IMPORTED_MODULE_13__["default"]; },
/* harmony export */   hu: function() { return /* reexport safe */ _hu_js__WEBPACK_IMPORTED_MODULE_14__["default"]; },
/* harmony export */   id: function() { return /* reexport safe */ _id_js__WEBPACK_IMPORTED_MODULE_15__["default"]; },
/* harmony export */   it: function() { return /* reexport safe */ _it_js__WEBPACK_IMPORTED_MODULE_16__["default"]; },
/* harmony export */   ja: function() { return /* reexport safe */ _ja_js__WEBPACK_IMPORTED_MODULE_17__["default"]; },
/* harmony export */   kh: function() { return /* reexport safe */ _kh_js__WEBPACK_IMPORTED_MODULE_18__["default"]; },
/* harmony export */   ko: function() { return /* reexport safe */ _ko_js__WEBPACK_IMPORTED_MODULE_19__["default"]; },
/* harmony export */   mk: function() { return /* reexport safe */ _mk_js__WEBPACK_IMPORTED_MODULE_20__["default"]; },
/* harmony export */   ms: function() { return /* reexport safe */ _ms_js__WEBPACK_IMPORTED_MODULE_21__["default"]; },
/* harmony export */   nl: function() { return /* reexport safe */ _nl_js__WEBPACK_IMPORTED_MODULE_22__["default"]; },
/* harmony export */   no: function() { return /* reexport safe */ _no_js__WEBPACK_IMPORTED_MODULE_23__["default"]; },
/* harmony export */   ota: function() { return /* reexport safe */ _ota_js__WEBPACK_IMPORTED_MODULE_24__["default"]; },
/* harmony export */   pl: function() { return /* reexport safe */ _pl_js__WEBPACK_IMPORTED_MODULE_26__["default"]; },
/* harmony export */   ps: function() { return /* reexport safe */ _ps_js__WEBPACK_IMPORTED_MODULE_25__["default"]; },
/* harmony export */   pt: function() { return /* reexport safe */ _pt_js__WEBPACK_IMPORTED_MODULE_27__["default"]; },
/* harmony export */   ru: function() { return /* reexport safe */ _ru_js__WEBPACK_IMPORTED_MODULE_28__["default"]; },
/* harmony export */   sl: function() { return /* reexport safe */ _sl_js__WEBPACK_IMPORTED_MODULE_29__["default"]; },
/* harmony export */   sv: function() { return /* reexport safe */ _sv_js__WEBPACK_IMPORTED_MODULE_30__["default"]; },
/* harmony export */   ta: function() { return /* reexport safe */ _ta_js__WEBPACK_IMPORTED_MODULE_31__["default"]; },
/* harmony export */   th: function() { return /* reexport safe */ _th_js__WEBPACK_IMPORTED_MODULE_32__["default"]; },
/* harmony export */   tr: function() { return /* reexport safe */ _tr_js__WEBPACK_IMPORTED_MODULE_33__["default"]; },
/* harmony export */   ua: function() { return /* reexport safe */ _ua_js__WEBPACK_IMPORTED_MODULE_34__["default"]; },
/* harmony export */   ur: function() { return /* reexport safe */ _ur_js__WEBPACK_IMPORTED_MODULE_35__["default"]; },
/* harmony export */   vi: function() { return /* reexport safe */ _vi_js__WEBPACK_IMPORTED_MODULE_36__["default"]; },
/* harmony export */   zhCN: function() { return /* reexport safe */ _zh_CN_js__WEBPACK_IMPORTED_MODULE_37__["default"]; },
/* harmony export */   zhTW: function() { return /* reexport safe */ _zh_TW_js__WEBPACK_IMPORTED_MODULE_38__["default"]; }
/* harmony export */ });
/* harmony import */ var _ar_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ar.js */ "./node_modules/zod/v4/locales/ar.js");
/* harmony import */ var _az_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./az.js */ "./node_modules/zod/v4/locales/az.js");
/* harmony import */ var _be_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./be.js */ "./node_modules/zod/v4/locales/be.js");
/* harmony import */ var _ca_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./ca.js */ "./node_modules/zod/v4/locales/ca.js");
/* harmony import */ var _cs_js__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./cs.js */ "./node_modules/zod/v4/locales/cs.js");
/* harmony import */ var _de_js__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./de.js */ "./node_modules/zod/v4/locales/de.js");
/* harmony import */ var _en_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./en.js */ "./node_modules/zod/v4/locales/en.js");
/* harmony import */ var _eo_js__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./eo.js */ "./node_modules/zod/v4/locales/eo.js");
/* harmony import */ var _es_js__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./es.js */ "./node_modules/zod/v4/locales/es.js");
/* harmony import */ var _fa_js__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./fa.js */ "./node_modules/zod/v4/locales/fa.js");
/* harmony import */ var _fi_js__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./fi.js */ "./node_modules/zod/v4/locales/fi.js");
/* harmony import */ var _fr_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./fr.js */ "./node_modules/zod/v4/locales/fr.js");
/* harmony import */ var _fr_CA_js__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./fr-CA.js */ "./node_modules/zod/v4/locales/fr-CA.js");
/* harmony import */ var _he_js__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./he.js */ "./node_modules/zod/v4/locales/he.js");
/* harmony import */ var _hu_js__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./hu.js */ "./node_modules/zod/v4/locales/hu.js");
/* harmony import */ var _id_js__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./id.js */ "./node_modules/zod/v4/locales/id.js");
/* harmony import */ var _it_js__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./it.js */ "./node_modules/zod/v4/locales/it.js");
/* harmony import */ var _ja_js__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./ja.js */ "./node_modules/zod/v4/locales/ja.js");
/* harmony import */ var _kh_js__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./kh.js */ "./node_modules/zod/v4/locales/kh.js");
/* harmony import */ var _ko_js__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./ko.js */ "./node_modules/zod/v4/locales/ko.js");
/* harmony import */ var _mk_js__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./mk.js */ "./node_modules/zod/v4/locales/mk.js");
/* harmony import */ var _ms_js__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./ms.js */ "./node_modules/zod/v4/locales/ms.js");
/* harmony import */ var _nl_js__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./nl.js */ "./node_modules/zod/v4/locales/nl.js");
/* harmony import */ var _no_js__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./no.js */ "./node_modules/zod/v4/locales/no.js");
/* harmony import */ var _ota_js__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./ota.js */ "./node_modules/zod/v4/locales/ota.js");
/* harmony import */ var _ps_js__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./ps.js */ "./node_modules/zod/v4/locales/ps.js");
/* harmony import */ var _pl_js__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ./pl.js */ "./node_modules/zod/v4/locales/pl.js");
/* harmony import */ var _pt_js__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! ./pt.js */ "./node_modules/zod/v4/locales/pt.js");
/* harmony import */ var _ru_js__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(/*! ./ru.js */ "./node_modules/zod/v4/locales/ru.js");
/* harmony import */ var _sl_js__WEBPACK_IMPORTED_MODULE_29__ = __webpack_require__(/*! ./sl.js */ "./node_modules/zod/v4/locales/sl.js");
/* harmony import */ var _sv_js__WEBPACK_IMPORTED_MODULE_30__ = __webpack_require__(/*! ./sv.js */ "./node_modules/zod/v4/locales/sv.js");
/* harmony import */ var _ta_js__WEBPACK_IMPORTED_MODULE_31__ = __webpack_require__(/*! ./ta.js */ "./node_modules/zod/v4/locales/ta.js");
/* harmony import */ var _th_js__WEBPACK_IMPORTED_MODULE_32__ = __webpack_require__(/*! ./th.js */ "./node_modules/zod/v4/locales/th.js");
/* harmony import */ var _tr_js__WEBPACK_IMPORTED_MODULE_33__ = __webpack_require__(/*! ./tr.js */ "./node_modules/zod/v4/locales/tr.js");
/* harmony import */ var _ua_js__WEBPACK_IMPORTED_MODULE_34__ = __webpack_require__(/*! ./ua.js */ "./node_modules/zod/v4/locales/ua.js");
/* harmony import */ var _ur_js__WEBPACK_IMPORTED_MODULE_35__ = __webpack_require__(/*! ./ur.js */ "./node_modules/zod/v4/locales/ur.js");
/* harmony import */ var _vi_js__WEBPACK_IMPORTED_MODULE_36__ = __webpack_require__(/*! ./vi.js */ "./node_modules/zod/v4/locales/vi.js");
/* harmony import */ var _zh_CN_js__WEBPACK_IMPORTED_MODULE_37__ = __webpack_require__(/*! ./zh-CN.js */ "./node_modules/zod/v4/locales/zh-CN.js");
/* harmony import */ var _zh_TW_js__WEBPACK_IMPORTED_MODULE_38__ = __webpack_require__(/*! ./zh-TW.js */ "./node_modules/zod/v4/locales/zh-TW.js");









































/***/ }),

/***/ "./node_modules/zod/v4/locales/it.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/it.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "caratteri", verb: "avere" },
        file: { unit: "byte", verb: "avere" },
        array: { unit: "elementi", verb: "avere" },
        set: { unit: "elementi", verb: "avere" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "numero";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "vettore";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "input",
        email: "indirizzo email",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "data e ora ISO",
        date: "data ISO",
        time: "ora ISO",
        duration: "durata ISO",
        ipv4: "indirizzo IPv4",
        ipv6: "indirizzo IPv6",
        cidrv4: "intervallo IPv4",
        cidrv6: "intervallo IPv6",
        base64: "stringa codificata in base64",
        base64url: "URL codificata in base64",
        json_string: "stringa JSON",
        e164: "numero E.164",
        jwt: "JWT",
        template_literal: "input",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Input non valido: atteso ${issue.expected}, ricevuto ${parsedType(issue.input)}`;
            // return `Input non valido: atteso ${issue.expected}, ricevuto ${util.getParsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Input non valido: atteso ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Opzione non valida: atteso uno tra ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Troppo grande: ${issue.origin ?? "valore"} deve avere ${adj}${issue.maximum.toString()} ${sizing.unit ?? "elementi"}`;
                return `Troppo grande: ${issue.origin ?? "valore"} deve essere ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Troppo piccolo: ${issue.origin} deve avere ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `Troppo piccolo: ${issue.origin} deve essere ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Stringa non valida: deve iniziare con "${_issue.prefix}"`;
                if (_issue.format === "ends_with")
                    return `Stringa non valida: deve terminare con "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Stringa non valida: deve includere "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Stringa non valida: deve corrispondere al pattern ${_issue.pattern}`;
                return `Invalid ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Numero non valido: deve essere un multiplo di ${issue.divisor}`;
            case "unrecognized_keys":
                return `Chiav${issue.keys.length > 1 ? "i" : "e"} non riconosciut${issue.keys.length > 1 ? "e" : "a"}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Chiave non valida in ${issue.origin}`;
            case "invalid_union":
                return "Input non valido";
            case "invalid_element":
                return `Valore non valido in ${issue.origin}`;
            default:
                return `Input non valido`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/ja.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/ja.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "文字", verb: "である" },
        file: { unit: "バイト", verb: "である" },
        array: { unit: "要素", verb: "である" },
        set: { unit: "要素", verb: "である" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "数値";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "配列";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "入力値",
        email: "メールアドレス",
        url: "URL",
        emoji: "絵文字",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO日時",
        date: "ISO日付",
        time: "ISO時刻",
        duration: "ISO期間",
        ipv4: "IPv4アドレス",
        ipv6: "IPv6アドレス",
        cidrv4: "IPv4範囲",
        cidrv6: "IPv6範囲",
        base64: "base64エンコード文字列",
        base64url: "base64urlエンコード文字列",
        json_string: "JSON文字列",
        e164: "E.164番号",
        jwt: "JWT",
        template_literal: "入力値",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `無効な入力: ${issue.expected}が期待されましたが、${parsedType(issue.input)}が入力されました`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `無効な入力: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}が期待されました`;
                return `無効な選択: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "、")}のいずれかである必要があります`;
            case "too_big": {
                const adj = issue.inclusive ? "以下である" : "より小さい";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `大きすぎる値: ${issue.origin ?? "値"}は${issue.maximum.toString()}${sizing.unit ?? "要素"}${adj}必要があります`;
                return `大きすぎる値: ${issue.origin ?? "値"}は${issue.maximum.toString()}${adj}必要があります`;
            }
            case "too_small": {
                const adj = issue.inclusive ? "以上である" : "より大きい";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `小さすぎる値: ${issue.origin}は${issue.minimum.toString()}${sizing.unit}${adj}必要があります`;
                return `小さすぎる値: ${issue.origin}は${issue.minimum.toString()}${adj}必要があります`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `無効な文字列: "${_issue.prefix}"で始まる必要があります`;
                if (_issue.format === "ends_with")
                    return `無効な文字列: "${_issue.suffix}"で終わる必要があります`;
                if (_issue.format === "includes")
                    return `無効な文字列: "${_issue.includes}"を含む必要があります`;
                if (_issue.format === "regex")
                    return `無効な文字列: パターン${_issue.pattern}に一致する必要があります`;
                return `無効な${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `無効な数値: ${issue.divisor}の倍数である必要があります`;
            case "unrecognized_keys":
                return `認識されていないキー${issue.keys.length > 1 ? "群" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, "、")}`;
            case "invalid_key":
                return `${issue.origin}内の無効なキー`;
            case "invalid_union":
                return "無効な入力";
            case "invalid_element":
                return `${issue.origin}内の無効な値`;
            default:
                return `無効な入力`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/kh.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/kh.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "តួអក្សរ", verb: "គួរមាន" },
        file: { unit: "បៃ", verb: "គួរមាន" },
        array: { unit: "ធាតុ", verb: "គួរមាន" },
        set: { unit: "ធាតុ", verb: "គួរមាន" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "មិនមែនជាលេខ (NaN)" : "លេខ";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "អារេ (Array)";
                }
                if (data === null) {
                    return "គ្មានតម្លៃ (null)";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "ទិន្នន័យបញ្ចូល",
        email: "អាសយដ្ឋានអ៊ីមែល",
        url: "URL",
        emoji: "សញ្ញាអារម្មណ៍",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "កាលបរិច្ឆេទ និងម៉ោង ISO",
        date: "កាលបរិច្ឆេទ ISO",
        time: "ម៉ោង ISO",
        duration: "រយៈពេល ISO",
        ipv4: "អាសយដ្ឋាន IPv4",
        ipv6: "អាសយដ្ឋាន IPv6",
        cidrv4: "ដែនអាសយដ្ឋាន IPv4",
        cidrv6: "ដែនអាសយដ្ឋាន IPv6",
        base64: "ខ្សែអក្សរអ៊ិកូដ base64",
        base64url: "ខ្សែអក្សរអ៊ិកូដ base64url",
        json_string: "ខ្សែអក្សរ JSON",
        e164: "លេខ E.164",
        jwt: "JWT",
        template_literal: "ទិន្នន័យបញ្ចូល",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `ទិន្នន័យបញ្ចូលមិនត្រឹមត្រូវ៖ ត្រូវការ ${issue.expected} ប៉ុន្តែទទួលបាន ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `ទិន្នន័យបញ្ចូលមិនត្រឹមត្រូវ៖ ត្រូវការ ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `ជម្រើសមិនត្រឹមត្រូវ៖ ត្រូវជាមួយក្នុងចំណោម ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `ធំពេក៖ ត្រូវការ ${issue.origin ?? "តម្លៃ"} ${adj} ${issue.maximum.toString()} ${sizing.unit ?? "ធាតុ"}`;
                return `ធំពេក៖ ត្រូវការ ${issue.origin ?? "តម្លៃ"} ${adj} ${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `តូចពេក៖ ត្រូវការ ${issue.origin} ${adj} ${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `តូចពេក៖ ត្រូវការ ${issue.origin} ${adj} ${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with") {
                    return `ខ្សែអក្សរមិនត្រឹមត្រូវ៖ ត្រូវចាប់ផ្តើមដោយ "${_issue.prefix}"`;
                }
                if (_issue.format === "ends_with")
                    return `ខ្សែអក្សរមិនត្រឹមត្រូវ៖ ត្រូវបញ្ចប់ដោយ "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `ខ្សែអក្សរមិនត្រឹមត្រូវ៖ ត្រូវមាន "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `ខ្សែអក្សរមិនត្រឹមត្រូវ៖ ត្រូវតែផ្គូផ្គងនឹងទម្រង់ដែលបានកំណត់ ${_issue.pattern}`;
                return `មិនត្រឹមត្រូវ៖ ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `លេខមិនត្រឹមត្រូវ៖ ត្រូវតែជាពហុគុណនៃ ${issue.divisor}`;
            case "unrecognized_keys":
                return `រកឃើញសោមិនស្គាល់៖ ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `សោមិនត្រឹមត្រូវនៅក្នុង ${issue.origin}`;
            case "invalid_union":
                return `ទិន្នន័យមិនត្រឹមត្រូវ`;
            case "invalid_element":
                return `ទិន្នន័យមិនត្រឹមត្រូវនៅក្នុង ${issue.origin}`;
            default:
                return `ទិន្នន័យមិនត្រឹមត្រូវ`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/ko.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/ko.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "문자", verb: "to have" },
        file: { unit: "바이트", verb: "to have" },
        array: { unit: "개", verb: "to have" },
        set: { unit: "개", verb: "to have" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "number";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "array";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "입력",
        email: "이메일 주소",
        url: "URL",
        emoji: "이모지",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO 날짜시간",
        date: "ISO 날짜",
        time: "ISO 시간",
        duration: "ISO 기간",
        ipv4: "IPv4 주소",
        ipv6: "IPv6 주소",
        cidrv4: "IPv4 범위",
        cidrv6: "IPv6 범위",
        base64: "base64 인코딩 문자열",
        base64url: "base64url 인코딩 문자열",
        json_string: "JSON 문자열",
        e164: "E.164 번호",
        jwt: "JWT",
        template_literal: "입력",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `잘못된 입력: 예상 타입은 ${issue.expected}, 받은 타입은 ${parsedType(issue.input)}입니다`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `잘못된 입력: 값은 ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])} 이어야 합니다`;
                return `잘못된 옵션: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "또는 ")} 중 하나여야 합니다`;
            case "too_big": {
                const adj = issue.inclusive ? "이하" : "미만";
                const suffix = adj === "미만" ? "이어야 합니다" : "여야 합니다";
                const sizing = getSizing(issue.origin);
                const unit = sizing?.unit ?? "요소";
                if (sizing)
                    return `${issue.origin ?? "값"}이 너무 큽니다: ${issue.maximum.toString()}${unit} ${adj}${suffix}`;
                return `${issue.origin ?? "값"}이 너무 큽니다: ${issue.maximum.toString()} ${adj}${suffix}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? "이상" : "초과";
                const suffix = adj === "이상" ? "이어야 합니다" : "여야 합니다";
                const sizing = getSizing(issue.origin);
                const unit = sizing?.unit ?? "요소";
                if (sizing) {
                    return `${issue.origin ?? "값"}이 너무 작습니다: ${issue.minimum.toString()}${unit} ${adj}${suffix}`;
                }
                return `${issue.origin ?? "값"}이 너무 작습니다: ${issue.minimum.toString()} ${adj}${suffix}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with") {
                    return `잘못된 문자열: "${_issue.prefix}"(으)로 시작해야 합니다`;
                }
                if (_issue.format === "ends_with")
                    return `잘못된 문자열: "${_issue.suffix}"(으)로 끝나야 합니다`;
                if (_issue.format === "includes")
                    return `잘못된 문자열: "${_issue.includes}"을(를) 포함해야 합니다`;
                if (_issue.format === "regex")
                    return `잘못된 문자열: 정규식 ${_issue.pattern} 패턴과 일치해야 합니다`;
                return `잘못된 ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `잘못된 숫자: ${issue.divisor}의 배수여야 합니다`;
            case "unrecognized_keys":
                return `인식할 수 없는 키: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `잘못된 키: ${issue.origin}`;
            case "invalid_union":
                return `잘못된 입력`;
            case "invalid_element":
                return `잘못된 값: ${issue.origin}`;
            default:
                return `잘못된 입력`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/mk.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/mk.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "знаци", verb: "да имаат" },
        file: { unit: "бајти", verb: "да имаат" },
        array: { unit: "ставки", verb: "да имаат" },
        set: { unit: "ставки", verb: "да имаат" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "број";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "низа";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "внес",
        email: "адреса на е-пошта",
        url: "URL",
        emoji: "емоџи",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO датум и време",
        date: "ISO датум",
        time: "ISO време",
        duration: "ISO времетраење",
        ipv4: "IPv4 адреса",
        ipv6: "IPv6 адреса",
        cidrv4: "IPv4 опсег",
        cidrv6: "IPv6 опсег",
        base64: "base64-енкодирана низа",
        base64url: "base64url-енкодирана низа",
        json_string: "JSON низа",
        e164: "E.164 број",
        jwt: "JWT",
        template_literal: "внес",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Грешен внес: се очекува ${issue.expected}, примено ${parsedType(issue.input)}`;
            // return `Invalid input: expected ${issue.expected}, received ${util.getParsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Invalid input: expected ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Грешана опција: се очекува една ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Премногу голем: се очекува ${issue.origin ?? "вредноста"} да има ${adj}${issue.maximum.toString()} ${sizing.unit ?? "елементи"}`;
                return `Премногу голем: се очекува ${issue.origin ?? "вредноста"} да биде ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Премногу мал: се очекува ${issue.origin} да има ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `Премногу мал: се очекува ${issue.origin} да биде ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with") {
                    return `Неважечка низа: мора да започнува со "${_issue.prefix}"`;
                }
                if (_issue.format === "ends_with")
                    return `Неважечка низа: мора да завршува со "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Неважечка низа: мора да вклучува "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Неважечка низа: мора да одгоара на патернот ${_issue.pattern}`;
                return `Invalid ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Грешен број: мора да биде делив со ${issue.divisor}`;
            case "unrecognized_keys":
                return `${issue.keys.length > 1 ? "Непрепознаени клучеви" : "Непрепознаен клуч"}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Грешен клуч во ${issue.origin}`;
            case "invalid_union":
                return "Грешен внес";
            case "invalid_element":
                return `Грешна вредност во ${issue.origin}`;
            default:
                return `Грешен внес`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/ms.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/ms.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "aksara", verb: "mempunyai" },
        file: { unit: "bait", verb: "mempunyai" },
        array: { unit: "elemen", verb: "mempunyai" },
        set: { unit: "elemen", verb: "mempunyai" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "nombor";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "array";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "input",
        email: "alamat e-mel",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "tarikh masa ISO",
        date: "tarikh ISO",
        time: "masa ISO",
        duration: "tempoh ISO",
        ipv4: "alamat IPv4",
        ipv6: "alamat IPv6",
        cidrv4: "julat IPv4",
        cidrv6: "julat IPv6",
        base64: "string dikodkan base64",
        base64url: "string dikodkan base64url",
        json_string: "string JSON",
        e164: "nombor E.164",
        jwt: "JWT",
        template_literal: "input",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Input tidak sah: dijangka ${issue.expected}, diterima ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Input tidak sah: dijangka ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Pilihan tidak sah: dijangka salah satu daripada ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Terlalu besar: dijangka ${issue.origin ?? "nilai"} ${sizing.verb} ${adj}${issue.maximum.toString()} ${sizing.unit ?? "elemen"}`;
                return `Terlalu besar: dijangka ${issue.origin ?? "nilai"} adalah ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Terlalu kecil: dijangka ${issue.origin} ${sizing.verb} ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `Terlalu kecil: dijangka ${issue.origin} adalah ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `String tidak sah: mesti bermula dengan "${_issue.prefix}"`;
                if (_issue.format === "ends_with")
                    return `String tidak sah: mesti berakhir dengan "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `String tidak sah: mesti mengandungi "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `String tidak sah: mesti sepadan dengan corak ${_issue.pattern}`;
                return `${Nouns[_issue.format] ?? issue.format} tidak sah`;
            }
            case "not_multiple_of":
                return `Nombor tidak sah: perlu gandaan ${issue.divisor}`;
            case "unrecognized_keys":
                return `Kunci tidak dikenali: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Kunci tidak sah dalam ${issue.origin}`;
            case "invalid_union":
                return "Input tidak sah";
            case "invalid_element":
                return `Nilai tidak sah dalam ${issue.origin}`;
            default:
                return `Input tidak sah`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/nl.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/nl.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "tekens" },
        file: { unit: "bytes" },
        array: { unit: "elementen" },
        set: { unit: "elementen" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "getal";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "array";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "invoer",
        email: "emailadres",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO datum en tijd",
        date: "ISO datum",
        time: "ISO tijd",
        duration: "ISO duur",
        ipv4: "IPv4-adres",
        ipv6: "IPv6-adres",
        cidrv4: "IPv4-bereik",
        cidrv6: "IPv6-bereik",
        base64: "base64-gecodeerde tekst",
        base64url: "base64 URL-gecodeerde tekst",
        json_string: "JSON string",
        e164: "E.164-nummer",
        jwt: "JWT",
        template_literal: "invoer",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Ongeldige invoer: verwacht ${issue.expected}, ontving ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Ongeldige invoer: verwacht ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Ongeldige optie: verwacht één van ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Te lang: verwacht dat ${issue.origin ?? "waarde"} ${adj}${issue.maximum.toString()} ${sizing.unit ?? "elementen"} bevat`;
                return `Te lang: verwacht dat ${issue.origin ?? "waarde"} ${adj}${issue.maximum.toString()} is`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Te kort: verwacht dat ${issue.origin} ${adj}${issue.minimum.toString()} ${sizing.unit} bevat`;
                }
                return `Te kort: verwacht dat ${issue.origin} ${adj}${issue.minimum.toString()} is`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with") {
                    return `Ongeldige tekst: moet met "${_issue.prefix}" beginnen`;
                }
                if (_issue.format === "ends_with")
                    return `Ongeldige tekst: moet op "${_issue.suffix}" eindigen`;
                if (_issue.format === "includes")
                    return `Ongeldige tekst: moet "${_issue.includes}" bevatten`;
                if (_issue.format === "regex")
                    return `Ongeldige tekst: moet overeenkomen met patroon ${_issue.pattern}`;
                return `Ongeldig: ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Ongeldig getal: moet een veelvoud van ${issue.divisor} zijn`;
            case "unrecognized_keys":
                return `Onbekende key${issue.keys.length > 1 ? "s" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Ongeldige key in ${issue.origin}`;
            case "invalid_union":
                return "Ongeldige invoer";
            case "invalid_element":
                return `Ongeldige waarde in ${issue.origin}`;
            default:
                return `Ongeldige invoer`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/no.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/no.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "tegn", verb: "å ha" },
        file: { unit: "bytes", verb: "å ha" },
        array: { unit: "elementer", verb: "å inneholde" },
        set: { unit: "elementer", verb: "å inneholde" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "tall";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "liste";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "input",
        email: "e-postadresse",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO dato- og klokkeslett",
        date: "ISO-dato",
        time: "ISO-klokkeslett",
        duration: "ISO-varighet",
        ipv4: "IPv4-område",
        ipv6: "IPv6-område",
        cidrv4: "IPv4-spekter",
        cidrv6: "IPv6-spekter",
        base64: "base64-enkodet streng",
        base64url: "base64url-enkodet streng",
        json_string: "JSON-streng",
        e164: "E.164-nummer",
        jwt: "JWT",
        template_literal: "input",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Ugyldig input: forventet ${issue.expected}, fikk ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Ugyldig verdi: forventet ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Ugyldig valg: forventet en av ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `For stor(t): forventet ${issue.origin ?? "value"} til å ha ${adj}${issue.maximum.toString()} ${sizing.unit ?? "elementer"}`;
                return `For stor(t): forventet ${issue.origin ?? "value"} til å ha ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `For lite(n): forventet ${issue.origin} til å ha ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `For lite(n): forventet ${issue.origin} til å ha ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Ugyldig streng: må starte med "${_issue.prefix}"`;
                if (_issue.format === "ends_with")
                    return `Ugyldig streng: må ende med "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Ugyldig streng: må inneholde "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Ugyldig streng: må matche mønsteret ${_issue.pattern}`;
                return `Ugyldig ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Ugyldig tall: må være et multiplum av ${issue.divisor}`;
            case "unrecognized_keys":
                return `${issue.keys.length > 1 ? "Ukjente nøkler" : "Ukjent nøkkel"}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Ugyldig nøkkel i ${issue.origin}`;
            case "invalid_union":
                return "Ugyldig input";
            case "invalid_element":
                return `Ugyldig verdi i ${issue.origin}`;
            default:
                return `Ugyldig input`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/ota.js":
/*!********************************************!*\
  !*** ./node_modules/zod/v4/locales/ota.js ***!
  \********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "harf", verb: "olmalıdır" },
        file: { unit: "bayt", verb: "olmalıdır" },
        array: { unit: "unsur", verb: "olmalıdır" },
        set: { unit: "unsur", verb: "olmalıdır" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "numara";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "saf";
                }
                if (data === null) {
                    return "gayb";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "giren",
        email: "epostagâh",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO hengâmı",
        date: "ISO tarihi",
        time: "ISO zamanı",
        duration: "ISO müddeti",
        ipv4: "IPv4 nişânı",
        ipv6: "IPv6 nişânı",
        cidrv4: "IPv4 menzili",
        cidrv6: "IPv6 menzili",
        base64: "base64-şifreli metin",
        base64url: "base64url-şifreli metin",
        json_string: "JSON metin",
        e164: "E.164 sayısı",
        jwt: "JWT",
        template_literal: "giren",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Fâsit giren: umulan ${issue.expected}, alınan ${parsedType(issue.input)}`;
            // return `Fâsit giren: umulan ${issue.expected}, alınan ${util.getParsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Fâsit giren: umulan ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Fâsit tercih: mûteberler ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Fazla büyük: ${issue.origin ?? "value"}, ${adj}${issue.maximum.toString()} ${sizing.unit ?? "elements"} sahip olmalıydı.`;
                return `Fazla büyük: ${issue.origin ?? "value"}, ${adj}${issue.maximum.toString()} olmalıydı.`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Fazla küçük: ${issue.origin}, ${adj}${issue.minimum.toString()} ${sizing.unit} sahip olmalıydı.`;
                }
                return `Fazla küçük: ${issue.origin}, ${adj}${issue.minimum.toString()} olmalıydı.`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Fâsit metin: "${_issue.prefix}" ile başlamalı.`;
                if (_issue.format === "ends_with")
                    return `Fâsit metin: "${_issue.suffix}" ile bitmeli.`;
                if (_issue.format === "includes")
                    return `Fâsit metin: "${_issue.includes}" ihtivâ etmeli.`;
                if (_issue.format === "regex")
                    return `Fâsit metin: ${_issue.pattern} nakşına uymalı.`;
                return `Fâsit ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Fâsit sayı: ${issue.divisor} katı olmalıydı.`;
            case "unrecognized_keys":
                return `Tanınmayan anahtar ${issue.keys.length > 1 ? "s" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `${issue.origin} için tanınmayan anahtar var.`;
            case "invalid_union":
                return "Giren tanınamadı.";
            case "invalid_element":
                return `${issue.origin} için tanınmayan kıymet var.`;
            default:
                return `Kıymet tanınamadı.`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/pl.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/pl.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "znaków", verb: "mieć" },
        file: { unit: "bajtów", verb: "mieć" },
        array: { unit: "elementów", verb: "mieć" },
        set: { unit: "elementów", verb: "mieć" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "liczba";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "tablica";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "wyrażenie",
        email: "adres email",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "data i godzina w formacie ISO",
        date: "data w formacie ISO",
        time: "godzina w formacie ISO",
        duration: "czas trwania ISO",
        ipv4: "adres IPv4",
        ipv6: "adres IPv6",
        cidrv4: "zakres IPv4",
        cidrv6: "zakres IPv6",
        base64: "ciąg znaków zakodowany w formacie base64",
        base64url: "ciąg znaków zakodowany w formacie base64url",
        json_string: "ciąg znaków w formacie JSON",
        e164: "liczba E.164",
        jwt: "JWT",
        template_literal: "wejście",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Nieprawidłowe dane wejściowe: oczekiwano ${issue.expected}, otrzymano ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Nieprawidłowe dane wejściowe: oczekiwano ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Nieprawidłowa opcja: oczekiwano jednej z wartości ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Za duża wartość: oczekiwano, że ${issue.origin ?? "wartość"} będzie mieć ${adj}${issue.maximum.toString()} ${sizing.unit ?? "elementów"}`;
                }
                return `Zbyt duż(y/a/e): oczekiwano, że ${issue.origin ?? "wartość"} będzie wynosić ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Za mała wartość: oczekiwano, że ${issue.origin ?? "wartość"} będzie mieć ${adj}${issue.minimum.toString()} ${sizing.unit ?? "elementów"}`;
                }
                return `Zbyt mał(y/a/e): oczekiwano, że ${issue.origin ?? "wartość"} będzie wynosić ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Nieprawidłowy ciąg znaków: musi zaczynać się od "${_issue.prefix}"`;
                if (_issue.format === "ends_with")
                    return `Nieprawidłowy ciąg znaków: musi kończyć się na "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Nieprawidłowy ciąg znaków: musi zawierać "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Nieprawidłowy ciąg znaków: musi odpowiadać wzorcowi ${_issue.pattern}`;
                return `Nieprawidłow(y/a/e) ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Nieprawidłowa liczba: musi być wielokrotnością ${issue.divisor}`;
            case "unrecognized_keys":
                return `Nierozpoznane klucze${issue.keys.length > 1 ? "s" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Nieprawidłowy klucz w ${issue.origin}`;
            case "invalid_union":
                return "Nieprawidłowe dane wejściowe";
            case "invalid_element":
                return `Nieprawidłowa wartość w ${issue.origin}`;
            default:
                return `Nieprawidłowe dane wejściowe`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/ps.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/ps.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "توکي", verb: "ولري" },
        file: { unit: "بایټس", verb: "ولري" },
        array: { unit: "توکي", verb: "ولري" },
        set: { unit: "توکي", verb: "ولري" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "عدد";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "ارې";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "ورودي",
        email: "بریښنالیک",
        url: "یو آر ال",
        emoji: "ایموجي",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "نیټه او وخت",
        date: "نېټه",
        time: "وخت",
        duration: "موده",
        ipv4: "د IPv4 پته",
        ipv6: "د IPv6 پته",
        cidrv4: "د IPv4 ساحه",
        cidrv6: "د IPv6 ساحه",
        base64: "base64-encoded متن",
        base64url: "base64url-encoded متن",
        json_string: "JSON متن",
        e164: "د E.164 شمېره",
        jwt: "JWT",
        template_literal: "ورودي",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `ناسم ورودي: باید ${issue.expected} وای, مګر ${parsedType(issue.input)} ترلاسه شو`;
            case "invalid_value":
                if (issue.values.length === 1) {
                    return `ناسم ورودي: باید ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])} وای`;
                }
                return `ناسم انتخاب: باید یو له ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")} څخه وای`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `ډیر لوی: ${issue.origin ?? "ارزښت"} باید ${adj}${issue.maximum.toString()} ${sizing.unit ?? "عنصرونه"} ولري`;
                }
                return `ډیر لوی: ${issue.origin ?? "ارزښت"} باید ${adj}${issue.maximum.toString()} وي`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `ډیر کوچنی: ${issue.origin} باید ${adj}${issue.minimum.toString()} ${sizing.unit} ولري`;
                }
                return `ډیر کوچنی: ${issue.origin} باید ${adj}${issue.minimum.toString()} وي`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with") {
                    return `ناسم متن: باید د "${_issue.prefix}" سره پیل شي`;
                }
                if (_issue.format === "ends_with") {
                    return `ناسم متن: باید د "${_issue.suffix}" سره پای ته ورسيږي`;
                }
                if (_issue.format === "includes") {
                    return `ناسم متن: باید "${_issue.includes}" ولري`;
                }
                if (_issue.format === "regex") {
                    return `ناسم متن: باید د ${_issue.pattern} سره مطابقت ولري`;
                }
                return `${Nouns[_issue.format] ?? issue.format} ناسم دی`;
            }
            case "not_multiple_of":
                return `ناسم عدد: باید د ${issue.divisor} مضرب وي`;
            case "unrecognized_keys":
                return `ناسم ${issue.keys.length > 1 ? "کلیډونه" : "کلیډ"}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `ناسم کلیډ په ${issue.origin} کې`;
            case "invalid_union":
                return `ناسمه ورودي`;
            case "invalid_element":
                return `ناسم عنصر په ${issue.origin} کې`;
            default:
                return `ناسمه ورودي`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/pt.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/pt.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "caracteres", verb: "ter" },
        file: { unit: "bytes", verb: "ter" },
        array: { unit: "itens", verb: "ter" },
        set: { unit: "itens", verb: "ter" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "número";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "array";
                }
                if (data === null) {
                    return "nulo";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "padrão",
        email: "endereço de e-mail",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "data e hora ISO",
        date: "data ISO",
        time: "hora ISO",
        duration: "duração ISO",
        ipv4: "endereço IPv4",
        ipv6: "endereço IPv6",
        cidrv4: "faixa de IPv4",
        cidrv6: "faixa de IPv6",
        base64: "texto codificado em base64",
        base64url: "URL codificada em base64",
        json_string: "texto JSON",
        e164: "número E.164",
        jwt: "JWT",
        template_literal: "entrada",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Tipo inválido: esperado ${issue.expected}, recebido ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Entrada inválida: esperado ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Opção inválida: esperada uma das ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Muito grande: esperado que ${issue.origin ?? "valor"} tivesse ${adj}${issue.maximum.toString()} ${sizing.unit ?? "elementos"}`;
                return `Muito grande: esperado que ${issue.origin ?? "valor"} fosse ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Muito pequeno: esperado que ${issue.origin} tivesse ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `Muito pequeno: esperado que ${issue.origin} fosse ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Texto inválido: deve começar com "${_issue.prefix}"`;
                if (_issue.format === "ends_with")
                    return `Texto inválido: deve terminar com "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Texto inválido: deve incluir "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Texto inválido: deve corresponder ao padrão ${_issue.pattern}`;
                return `${Nouns[_issue.format] ?? issue.format} inválido`;
            }
            case "not_multiple_of":
                return `Número inválido: deve ser múltiplo de ${issue.divisor}`;
            case "unrecognized_keys":
                return `Chave${issue.keys.length > 1 ? "s" : ""} desconhecida${issue.keys.length > 1 ? "s" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Chave inválida em ${issue.origin}`;
            case "invalid_union":
                return "Entrada inválida";
            case "invalid_element":
                return `Valor inválido em ${issue.origin}`;
            default:
                return `Campo inválido`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/ru.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/ru.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

function getRussianPlural(count, one, few, many) {
    const absCount = Math.abs(count);
    const lastDigit = absCount % 10;
    const lastTwoDigits = absCount % 100;
    if (lastTwoDigits >= 11 && lastTwoDigits <= 19) {
        return many;
    }
    if (lastDigit === 1) {
        return one;
    }
    if (lastDigit >= 2 && lastDigit <= 4) {
        return few;
    }
    return many;
}
const error = () => {
    const Sizable = {
        string: {
            unit: {
                one: "символ",
                few: "символа",
                many: "символов",
            },
            verb: "иметь",
        },
        file: {
            unit: {
                one: "байт",
                few: "байта",
                many: "байт",
            },
            verb: "иметь",
        },
        array: {
            unit: {
                one: "элемент",
                few: "элемента",
                many: "элементов",
            },
            verb: "иметь",
        },
        set: {
            unit: {
                one: "элемент",
                few: "элемента",
                many: "элементов",
            },
            verb: "иметь",
        },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "число";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "массив";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "ввод",
        email: "email адрес",
        url: "URL",
        emoji: "эмодзи",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO дата и время",
        date: "ISO дата",
        time: "ISO время",
        duration: "ISO длительность",
        ipv4: "IPv4 адрес",
        ipv6: "IPv6 адрес",
        cidrv4: "IPv4 диапазон",
        cidrv6: "IPv6 диапазон",
        base64: "строка в формате base64",
        base64url: "строка в формате base64url",
        json_string: "JSON строка",
        e164: "номер E.164",
        jwt: "JWT",
        template_literal: "ввод",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Неверный ввод: ожидалось ${issue.expected}, получено ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Неверный ввод: ожидалось ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Неверный вариант: ожидалось одно из ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    const maxValue = Number(issue.maximum);
                    const unit = getRussianPlural(maxValue, sizing.unit.one, sizing.unit.few, sizing.unit.many);
                    return `Слишком большое значение: ожидалось, что ${issue.origin ?? "значение"} будет иметь ${adj}${issue.maximum.toString()} ${unit}`;
                }
                return `Слишком большое значение: ожидалось, что ${issue.origin ?? "значение"} будет ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    const minValue = Number(issue.minimum);
                    const unit = getRussianPlural(minValue, sizing.unit.one, sizing.unit.few, sizing.unit.many);
                    return `Слишком маленькое значение: ожидалось, что ${issue.origin} будет иметь ${adj}${issue.minimum.toString()} ${unit}`;
                }
                return `Слишком маленькое значение: ожидалось, что ${issue.origin} будет ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Неверная строка: должна начинаться с "${_issue.prefix}"`;
                if (_issue.format === "ends_with")
                    return `Неверная строка: должна заканчиваться на "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Неверная строка: должна содержать "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Неверная строка: должна соответствовать шаблону ${_issue.pattern}`;
                return `Неверный ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Неверное число: должно быть кратным ${issue.divisor}`;
            case "unrecognized_keys":
                return `Нераспознанн${issue.keys.length > 1 ? "ые" : "ый"} ключ${issue.keys.length > 1 ? "и" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Неверный ключ в ${issue.origin}`;
            case "invalid_union":
                return "Неверные входные данные";
            case "invalid_element":
                return `Неверное значение в ${issue.origin}`;
            default:
                return `Неверные входные данные`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/sl.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/sl.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "znakov", verb: "imeti" },
        file: { unit: "bajtov", verb: "imeti" },
        array: { unit: "elementov", verb: "imeti" },
        set: { unit: "elementov", verb: "imeti" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "število";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "tabela";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "vnos",
        email: "e-poštni naslov",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO datum in čas",
        date: "ISO datum",
        time: "ISO čas",
        duration: "ISO trajanje",
        ipv4: "IPv4 naslov",
        ipv6: "IPv6 naslov",
        cidrv4: "obseg IPv4",
        cidrv6: "obseg IPv6",
        base64: "base64 kodiran niz",
        base64url: "base64url kodiran niz",
        json_string: "JSON niz",
        e164: "E.164 številka",
        jwt: "JWT",
        template_literal: "vnos",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Neveljaven vnos: pričakovano ${issue.expected}, prejeto ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Neveljaven vnos: pričakovano ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Neveljavna možnost: pričakovano eno izmed ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Preveliko: pričakovano, da bo ${issue.origin ?? "vrednost"} imelo ${adj}${issue.maximum.toString()} ${sizing.unit ?? "elementov"}`;
                return `Preveliko: pričakovano, da bo ${issue.origin ?? "vrednost"} ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Premajhno: pričakovano, da bo ${issue.origin} imelo ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `Premajhno: pričakovano, da bo ${issue.origin} ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with") {
                    return `Neveljaven niz: mora se začeti z "${_issue.prefix}"`;
                }
                if (_issue.format === "ends_with")
                    return `Neveljaven niz: mora se končati z "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Neveljaven niz: mora vsebovati "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Neveljaven niz: mora ustrezati vzorcu ${_issue.pattern}`;
                return `Neveljaven ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Neveljavno število: mora biti večkratnik ${issue.divisor}`;
            case "unrecognized_keys":
                return `Neprepoznan${issue.keys.length > 1 ? "i ključi" : " ključ"}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Neveljaven ključ v ${issue.origin}`;
            case "invalid_union":
                return "Neveljaven vnos";
            case "invalid_element":
                return `Neveljavna vrednost v ${issue.origin}`;
            default:
                return "Neveljaven vnos";
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/sv.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/sv.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "tecken", verb: "att ha" },
        file: { unit: "bytes", verb: "att ha" },
        array: { unit: "objekt", verb: "att innehålla" },
        set: { unit: "objekt", verb: "att innehålla" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "antal";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "lista";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "reguljärt uttryck",
        email: "e-postadress",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO-datum och tid",
        date: "ISO-datum",
        time: "ISO-tid",
        duration: "ISO-varaktighet",
        ipv4: "IPv4-intervall",
        ipv6: "IPv6-intervall",
        cidrv4: "IPv4-spektrum",
        cidrv6: "IPv6-spektrum",
        base64: "base64-kodad sträng",
        base64url: "base64url-kodad sträng",
        json_string: "JSON-sträng",
        e164: "E.164-nummer",
        jwt: "JWT",
        template_literal: "mall-literal",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Ogiltig inmatning: förväntat ${issue.expected}, fick ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Ogiltig inmatning: förväntat ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Ogiltigt val: förväntade en av ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `För stor(t): förväntade ${issue.origin ?? "värdet"} att ha ${adj}${issue.maximum.toString()} ${sizing.unit ?? "element"}`;
                }
                return `För stor(t): förväntat ${issue.origin ?? "värdet"} att ha ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `För lite(t): förväntade ${issue.origin ?? "värdet"} att ha ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `För lite(t): förväntade ${issue.origin ?? "värdet"} att ha ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with") {
                    return `Ogiltig sträng: måste börja med "${_issue.prefix}"`;
                }
                if (_issue.format === "ends_with")
                    return `Ogiltig sträng: måste sluta med "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Ogiltig sträng: måste innehålla "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Ogiltig sträng: måste matcha mönstret "${_issue.pattern}"`;
                return `Ogiltig(t) ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Ogiltigt tal: måste vara en multipel av ${issue.divisor}`;
            case "unrecognized_keys":
                return `${issue.keys.length > 1 ? "Okända nycklar" : "Okänd nyckel"}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Ogiltig nyckel i ${issue.origin ?? "värdet"}`;
            case "invalid_union":
                return "Ogiltig input";
            case "invalid_element":
                return `Ogiltigt värde i ${issue.origin ?? "värdet"}`;
            default:
                return `Ogiltig input`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/ta.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/ta.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "எழுத்துக்கள்", verb: "கொண்டிருக்க வேண்டும்" },
        file: { unit: "பைட்டுகள்", verb: "கொண்டிருக்க வேண்டும்" },
        array: { unit: "உறுப்புகள்", verb: "கொண்டிருக்க வேண்டும்" },
        set: { unit: "உறுப்புகள்", verb: "கொண்டிருக்க வேண்டும்" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "எண் அல்லாதது" : "எண்";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "அணி";
                }
                if (data === null) {
                    return "வெறுமை";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "உள்ளீடு",
        email: "மின்னஞ்சல் முகவரி",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO தேதி நேரம்",
        date: "ISO தேதி",
        time: "ISO நேரம்",
        duration: "ISO கால அளவு",
        ipv4: "IPv4 முகவரி",
        ipv6: "IPv6 முகவரி",
        cidrv4: "IPv4 வரம்பு",
        cidrv6: "IPv6 வரம்பு",
        base64: "base64-encoded சரம்",
        base64url: "base64url-encoded சரம்",
        json_string: "JSON சரம்",
        e164: "E.164 எண்",
        jwt: "JWT",
        template_literal: "input",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `தவறான உள்ளீடு: எதிர்பார்க்கப்பட்டது ${issue.expected}, பெறப்பட்டது ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `தவறான உள்ளீடு: எதிர்பார்க்கப்பட்டது ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `தவறான விருப்பம்: எதிர்பார்க்கப்பட்டது ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")} இல் ஒன்று`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `மிக பெரியது: எதிர்பார்க்கப்பட்டது ${issue.origin ?? "மதிப்பு"} ${adj}${issue.maximum.toString()} ${sizing.unit ?? "உறுப்புகள்"} ஆக இருக்க வேண்டும்`;
                }
                return `மிக பெரியது: எதிர்பார்க்கப்பட்டது ${issue.origin ?? "மதிப்பு"} ${adj}${issue.maximum.toString()} ஆக இருக்க வேண்டும்`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `மிகச் சிறியது: எதிர்பார்க்கப்பட்டது ${issue.origin} ${adj}${issue.minimum.toString()} ${sizing.unit} ஆக இருக்க வேண்டும்`; //
                }
                return `மிகச் சிறியது: எதிர்பார்க்கப்பட்டது ${issue.origin} ${adj}${issue.minimum.toString()} ஆக இருக்க வேண்டும்`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `தவறான சரம்: "${_issue.prefix}" இல் தொடங்க வேண்டும்`;
                if (_issue.format === "ends_with")
                    return `தவறான சரம்: "${_issue.suffix}" இல் முடிவடைய வேண்டும்`;
                if (_issue.format === "includes")
                    return `தவறான சரம்: "${_issue.includes}" ஐ உள்ளடக்க வேண்டும்`;
                if (_issue.format === "regex")
                    return `தவறான சரம்: ${_issue.pattern} முறைபாட்டுடன் பொருந்த வேண்டும்`;
                return `தவறான ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `தவறான எண்: ${issue.divisor} இன் பலமாக இருக்க வேண்டும்`;
            case "unrecognized_keys":
                return `அடையாளம் தெரியாத விசை${issue.keys.length > 1 ? "கள்" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `${issue.origin} இல் தவறான விசை`;
            case "invalid_union":
                return "தவறான உள்ளீடு";
            case "invalid_element":
                return `${issue.origin} இல் தவறான மதிப்பு`;
            default:
                return `தவறான உள்ளீடு`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/th.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/th.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "ตัวอักษร", verb: "ควรมี" },
        file: { unit: "ไบต์", verb: "ควรมี" },
        array: { unit: "รายการ", verb: "ควรมี" },
        set: { unit: "รายการ", verb: "ควรมี" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "ไม่ใช่ตัวเลข (NaN)" : "ตัวเลข";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "อาร์เรย์ (Array)";
                }
                if (data === null) {
                    return "ไม่มีค่า (null)";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "ข้อมูลที่ป้อน",
        email: "ที่อยู่อีเมล",
        url: "URL",
        emoji: "อิโมจิ",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "วันที่เวลาแบบ ISO",
        date: "วันที่แบบ ISO",
        time: "เวลาแบบ ISO",
        duration: "ช่วงเวลาแบบ ISO",
        ipv4: "ที่อยู่ IPv4",
        ipv6: "ที่อยู่ IPv6",
        cidrv4: "ช่วง IP แบบ IPv4",
        cidrv6: "ช่วง IP แบบ IPv6",
        base64: "ข้อความแบบ Base64",
        base64url: "ข้อความแบบ Base64 สำหรับ URL",
        json_string: "ข้อความแบบ JSON",
        e164: "เบอร์โทรศัพท์ระหว่างประเทศ (E.164)",
        jwt: "โทเคน JWT",
        template_literal: "ข้อมูลที่ป้อน",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `ประเภทข้อมูลไม่ถูกต้อง: ควรเป็น ${issue.expected} แต่ได้รับ ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `ค่าไม่ถูกต้อง: ควรเป็น ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `ตัวเลือกไม่ถูกต้อง: ควรเป็นหนึ่งใน ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "ไม่เกิน" : "น้อยกว่า";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `เกินกำหนด: ${issue.origin ?? "ค่า"} ควรมี${adj} ${issue.maximum.toString()} ${sizing.unit ?? "รายการ"}`;
                return `เกินกำหนด: ${issue.origin ?? "ค่า"} ควรมี${adj} ${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? "อย่างน้อย" : "มากกว่า";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `น้อยกว่ากำหนด: ${issue.origin} ควรมี${adj} ${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `น้อยกว่ากำหนด: ${issue.origin} ควรมี${adj} ${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with") {
                    return `รูปแบบไม่ถูกต้อง: ข้อความต้องขึ้นต้นด้วย "${_issue.prefix}"`;
                }
                if (_issue.format === "ends_with")
                    return `รูปแบบไม่ถูกต้อง: ข้อความต้องลงท้ายด้วย "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `รูปแบบไม่ถูกต้อง: ข้อความต้องมี "${_issue.includes}" อยู่ในข้อความ`;
                if (_issue.format === "regex")
                    return `รูปแบบไม่ถูกต้อง: ต้องตรงกับรูปแบบที่กำหนด ${_issue.pattern}`;
                return `รูปแบบไม่ถูกต้อง: ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `ตัวเลขไม่ถูกต้อง: ต้องเป็นจำนวนที่หารด้วย ${issue.divisor} ได้ลงตัว`;
            case "unrecognized_keys":
                return `พบคีย์ที่ไม่รู้จัก: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `คีย์ไม่ถูกต้องใน ${issue.origin}`;
            case "invalid_union":
                return "ข้อมูลไม่ถูกต้อง: ไม่ตรงกับรูปแบบยูเนียนที่กำหนดไว้";
            case "invalid_element":
                return `ข้อมูลไม่ถูกต้องใน ${issue.origin}`;
            default:
                return `ข้อมูลไม่ถูกต้อง`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/tr.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/tr.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; },
/* harmony export */   parsedType: function() { return /* binding */ parsedType; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const parsedType = (data) => {
    const t = typeof data;
    switch (t) {
        case "number": {
            return Number.isNaN(data) ? "NaN" : "number";
        }
        case "object": {
            if (Array.isArray(data)) {
                return "array";
            }
            if (data === null) {
                return "null";
            }
            if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                return data.constructor.name;
            }
        }
    }
    return t;
};
const error = () => {
    const Sizable = {
        string: { unit: "karakter", verb: "olmalı" },
        file: { unit: "bayt", verb: "olmalı" },
        array: { unit: "öğe", verb: "olmalı" },
        set: { unit: "öğe", verb: "olmalı" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const Nouns = {
        regex: "girdi",
        email: "e-posta adresi",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO tarih ve saat",
        date: "ISO tarih",
        time: "ISO saat",
        duration: "ISO süre",
        ipv4: "IPv4 adresi",
        ipv6: "IPv6 adresi",
        cidrv4: "IPv4 aralığı",
        cidrv6: "IPv6 aralığı",
        base64: "base64 ile şifrelenmiş metin",
        base64url: "base64url ile şifrelenmiş metin",
        json_string: "JSON dizesi",
        e164: "E.164 sayısı",
        jwt: "JWT",
        template_literal: "Şablon dizesi",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Geçersiz değer: beklenen ${issue.expected}, alınan ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Geçersiz değer: beklenen ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Geçersiz seçenek: aşağıdakilerden biri olmalı: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Çok büyük: beklenen ${issue.origin ?? "değer"} ${adj}${issue.maximum.toString()} ${sizing.unit ?? "öğe"}`;
                return `Çok büyük: beklenen ${issue.origin ?? "değer"} ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Çok küçük: beklenen ${issue.origin} ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                return `Çok küçük: beklenen ${issue.origin} ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Geçersiz metin: "${_issue.prefix}" ile başlamalı`;
                if (_issue.format === "ends_with")
                    return `Geçersiz metin: "${_issue.suffix}" ile bitmeli`;
                if (_issue.format === "includes")
                    return `Geçersiz metin: "${_issue.includes}" içermeli`;
                if (_issue.format === "regex")
                    return `Geçersiz metin: ${_issue.pattern} desenine uymalı`;
                return `Geçersiz ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Geçersiz sayı: ${issue.divisor} ile tam bölünebilmeli`;
            case "unrecognized_keys":
                return `Tanınmayan anahtar${issue.keys.length > 1 ? "lar" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `${issue.origin} içinde geçersiz anahtar`;
            case "invalid_union":
                return "Geçersiz değer";
            case "invalid_element":
                return `${issue.origin} içinde geçersiz değer`;
            default:
                return `Geçersiz değer`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/ua.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/ua.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "символів", verb: "матиме" },
        file: { unit: "байтів", verb: "матиме" },
        array: { unit: "елементів", verb: "матиме" },
        set: { unit: "елементів", verb: "матиме" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "число";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "масив";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "вхідні дані",
        email: "адреса електронної пошти",
        url: "URL",
        emoji: "емодзі",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "дата та час ISO",
        date: "дата ISO",
        time: "час ISO",
        duration: "тривалість ISO",
        ipv4: "адреса IPv4",
        ipv6: "адреса IPv6",
        cidrv4: "діапазон IPv4",
        cidrv6: "діапазон IPv6",
        base64: "рядок у кодуванні base64",
        base64url: "рядок у кодуванні base64url",
        json_string: "рядок JSON",
        e164: "номер E.164",
        jwt: "JWT",
        template_literal: "вхідні дані",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Неправильні вхідні дані: очікується ${issue.expected}, отримано ${parsedType(issue.input)}`;
            // return `Неправильні вхідні дані: очікується ${issue.expected}, отримано ${util.getParsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Неправильні вхідні дані: очікується ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Неправильна опція: очікується одне з ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Занадто велике: очікується, що ${issue.origin ?? "значення"} ${sizing.verb} ${adj}${issue.maximum.toString()} ${sizing.unit ?? "елементів"}`;
                return `Занадто велике: очікується, що ${issue.origin ?? "значення"} буде ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Занадто мале: очікується, що ${issue.origin} ${sizing.verb} ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `Занадто мале: очікується, що ${issue.origin} буде ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Неправильний рядок: повинен починатися з "${_issue.prefix}"`;
                if (_issue.format === "ends_with")
                    return `Неправильний рядок: повинен закінчуватися на "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Неправильний рядок: повинен містити "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Неправильний рядок: повинен відповідати шаблону ${_issue.pattern}`;
                return `Неправильний ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `Неправильне число: повинно бути кратним ${issue.divisor}`;
            case "unrecognized_keys":
                return `Нерозпізнаний ключ${issue.keys.length > 1 ? "і" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Неправильний ключ у ${issue.origin}`;
            case "invalid_union":
                return "Неправильні вхідні дані";
            case "invalid_element":
                return `Неправильне значення у ${issue.origin}`;
            default:
                return `Неправильні вхідні дані`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/ur.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/ur.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "حروف", verb: "ہونا" },
        file: { unit: "بائٹس", verb: "ہونا" },
        array: { unit: "آئٹمز", verb: "ہونا" },
        set: { unit: "آئٹمز", verb: "ہونا" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "نمبر";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "آرے";
                }
                if (data === null) {
                    return "نل";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "ان پٹ",
        email: "ای میل ایڈریس",
        url: "یو آر ایل",
        emoji: "ایموجی",
        uuid: "یو یو آئی ڈی",
        uuidv4: "یو یو آئی ڈی وی 4",
        uuidv6: "یو یو آئی ڈی وی 6",
        nanoid: "نینو آئی ڈی",
        guid: "جی یو آئی ڈی",
        cuid: "سی یو آئی ڈی",
        cuid2: "سی یو آئی ڈی 2",
        ulid: "یو ایل آئی ڈی",
        xid: "ایکس آئی ڈی",
        ksuid: "کے ایس یو آئی ڈی",
        datetime: "آئی ایس او ڈیٹ ٹائم",
        date: "آئی ایس او تاریخ",
        time: "آئی ایس او وقت",
        duration: "آئی ایس او مدت",
        ipv4: "آئی پی وی 4 ایڈریس",
        ipv6: "آئی پی وی 6 ایڈریس",
        cidrv4: "آئی پی وی 4 رینج",
        cidrv6: "آئی پی وی 6 رینج",
        base64: "بیس 64 ان کوڈڈ سٹرنگ",
        base64url: "بیس 64 یو آر ایل ان کوڈڈ سٹرنگ",
        json_string: "جے ایس او این سٹرنگ",
        e164: "ای 164 نمبر",
        jwt: "جے ڈبلیو ٹی",
        template_literal: "ان پٹ",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `غلط ان پٹ: ${issue.expected} متوقع تھا، ${parsedType(issue.input)} موصول ہوا`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `غلط ان پٹ: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])} متوقع تھا`;
                return `غلط آپشن: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")} میں سے ایک متوقع تھا`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `بہت بڑا: ${issue.origin ?? "ویلیو"} کے ${adj}${issue.maximum.toString()} ${sizing.unit ?? "عناصر"} ہونے متوقع تھے`;
                return `بہت بڑا: ${issue.origin ?? "ویلیو"} کا ${adj}${issue.maximum.toString()} ہونا متوقع تھا`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `بہت چھوٹا: ${issue.origin} کے ${adj}${issue.minimum.toString()} ${sizing.unit} ہونے متوقع تھے`;
                }
                return `بہت چھوٹا: ${issue.origin} کا ${adj}${issue.minimum.toString()} ہونا متوقع تھا`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with") {
                    return `غلط سٹرنگ: "${_issue.prefix}" سے شروع ہونا چاہیے`;
                }
                if (_issue.format === "ends_with")
                    return `غلط سٹرنگ: "${_issue.suffix}" پر ختم ہونا چاہیے`;
                if (_issue.format === "includes")
                    return `غلط سٹرنگ: "${_issue.includes}" شامل ہونا چاہیے`;
                if (_issue.format === "regex")
                    return `غلط سٹرنگ: پیٹرن ${_issue.pattern} سے میچ ہونا چاہیے`;
                return `غلط ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `غلط نمبر: ${issue.divisor} کا مضاعف ہونا چاہیے`;
            case "unrecognized_keys":
                return `غیر تسلیم شدہ کی${issue.keys.length > 1 ? "ز" : ""}: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, "، ")}`;
            case "invalid_key":
                return `${issue.origin} میں غلط کی`;
            case "invalid_union":
                return "غلط ان پٹ";
            case "invalid_element":
                return `${issue.origin} میں غلط ویلیو`;
            default:
                return `غلط ان پٹ`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/vi.js":
/*!*******************************************!*\
  !*** ./node_modules/zod/v4/locales/vi.js ***!
  \*******************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "ký tự", verb: "có" },
        file: { unit: "byte", verb: "có" },
        array: { unit: "phần tử", verb: "có" },
        set: { unit: "phần tử", verb: "có" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "số";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "mảng";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "đầu vào",
        email: "địa chỉ email",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ngày giờ ISO",
        date: "ngày ISO",
        time: "giờ ISO",
        duration: "khoảng thời gian ISO",
        ipv4: "địa chỉ IPv4",
        ipv6: "địa chỉ IPv6",
        cidrv4: "dải IPv4",
        cidrv6: "dải IPv6",
        base64: "chuỗi mã hóa base64",
        base64url: "chuỗi mã hóa base64url",
        json_string: "chuỗi JSON",
        e164: "số E.164",
        jwt: "JWT",
        template_literal: "đầu vào",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `Đầu vào không hợp lệ: mong đợi ${issue.expected}, nhận được ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `Đầu vào không hợp lệ: mong đợi ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `Tùy chọn không hợp lệ: mong đợi một trong các giá trị ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `Quá lớn: mong đợi ${issue.origin ?? "giá trị"} ${sizing.verb} ${adj}${issue.maximum.toString()} ${sizing.unit ?? "phần tử"}`;
                return `Quá lớn: mong đợi ${issue.origin ?? "giá trị"} ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `Quá nhỏ: mong đợi ${issue.origin} ${sizing.verb} ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `Quá nhỏ: mong đợi ${issue.origin} ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `Chuỗi không hợp lệ: phải bắt đầu bằng "${_issue.prefix}"`;
                if (_issue.format === "ends_with")
                    return `Chuỗi không hợp lệ: phải kết thúc bằng "${_issue.suffix}"`;
                if (_issue.format === "includes")
                    return `Chuỗi không hợp lệ: phải bao gồm "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `Chuỗi không hợp lệ: phải khớp với mẫu ${_issue.pattern}`;
                return `${Nouns[_issue.format] ?? issue.format} không hợp lệ`;
            }
            case "not_multiple_of":
                return `Số không hợp lệ: phải là bội số của ${issue.divisor}`;
            case "unrecognized_keys":
                return `Khóa không được nhận dạng: ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `Khóa không hợp lệ trong ${issue.origin}`;
            case "invalid_union":
                return "Đầu vào không hợp lệ";
            case "invalid_element":
                return `Giá trị không hợp lệ trong ${issue.origin}`;
            default:
                return `Đầu vào không hợp lệ`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/zh-CN.js":
/*!**********************************************!*\
  !*** ./node_modules/zod/v4/locales/zh-CN.js ***!
  \**********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "字符", verb: "包含" },
        file: { unit: "字节", verb: "包含" },
        array: { unit: "项", verb: "包含" },
        set: { unit: "项", verb: "包含" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "非数字(NaN)" : "数字";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "数组";
                }
                if (data === null) {
                    return "空值(null)";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "输入",
        email: "电子邮件",
        url: "URL",
        emoji: "表情符号",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO日期时间",
        date: "ISO日期",
        time: "ISO时间",
        duration: "ISO时长",
        ipv4: "IPv4地址",
        ipv6: "IPv6地址",
        cidrv4: "IPv4网段",
        cidrv6: "IPv6网段",
        base64: "base64编码字符串",
        base64url: "base64url编码字符串",
        json_string: "JSON字符串",
        e164: "E.164号码",
        jwt: "JWT",
        template_literal: "输入",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `无效输入：期望 ${issue.expected}，实际接收 ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `无效输入：期望 ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `无效选项：期望以下之一 ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `数值过大：期望 ${issue.origin ?? "值"} ${adj}${issue.maximum.toString()} ${sizing.unit ?? "个元素"}`;
                return `数值过大：期望 ${issue.origin ?? "值"} ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `数值过小：期望 ${issue.origin} ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `数值过小：期望 ${issue.origin} ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with")
                    return `无效字符串：必须以 "${_issue.prefix}" 开头`;
                if (_issue.format === "ends_with")
                    return `无效字符串：必须以 "${_issue.suffix}" 结尾`;
                if (_issue.format === "includes")
                    return `无效字符串：必须包含 "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `无效字符串：必须满足正则表达式 ${_issue.pattern}`;
                return `无效${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `无效数字：必须是 ${issue.divisor} 的倍数`;
            case "unrecognized_keys":
                return `出现未知的键(key): ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, ", ")}`;
            case "invalid_key":
                return `${issue.origin} 中的键(key)无效`;
            case "invalid_union":
                return "无效输入";
            case "invalid_element":
                return `${issue.origin} 中包含无效值(value)`;
            default:
                return `无效输入`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ }),

/***/ "./node_modules/zod/v4/locales/zh-TW.js":
/*!**********************************************!*\
  !*** ./node_modules/zod/v4/locales/zh-TW.js ***!
  \**********************************************/
/***/ (function(__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": function() { return /* export default binding */ __WEBPACK_DEFAULT_EXPORT__; }
/* harmony export */ });
/* harmony import */ var _core_util_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../core/util.js */ "./node_modules/zod/v4/core/util.js");

const error = () => {
    const Sizable = {
        string: { unit: "字元", verb: "擁有" },
        file: { unit: "位元組", verb: "擁有" },
        array: { unit: "項目", verb: "擁有" },
        set: { unit: "項目", verb: "擁有" },
    };
    function getSizing(origin) {
        return Sizable[origin] ?? null;
    }
    const parsedType = (data) => {
        const t = typeof data;
        switch (t) {
            case "number": {
                return Number.isNaN(data) ? "NaN" : "number";
            }
            case "object": {
                if (Array.isArray(data)) {
                    return "array";
                }
                if (data === null) {
                    return "null";
                }
                if (Object.getPrototypeOf(data) !== Object.prototype && data.constructor) {
                    return data.constructor.name;
                }
            }
        }
        return t;
    };
    const Nouns = {
        regex: "輸入",
        email: "郵件地址",
        url: "URL",
        emoji: "emoji",
        uuid: "UUID",
        uuidv4: "UUIDv4",
        uuidv6: "UUIDv6",
        nanoid: "nanoid",
        guid: "GUID",
        cuid: "cuid",
        cuid2: "cuid2",
        ulid: "ULID",
        xid: "XID",
        ksuid: "KSUID",
        datetime: "ISO 日期時間",
        date: "ISO 日期",
        time: "ISO 時間",
        duration: "ISO 期間",
        ipv4: "IPv4 位址",
        ipv6: "IPv6 位址",
        cidrv4: "IPv4 範圍",
        cidrv6: "IPv6 範圍",
        base64: "base64 編碼字串",
        base64url: "base64url 編碼字串",
        json_string: "JSON 字串",
        e164: "E.164 數值",
        jwt: "JWT",
        template_literal: "輸入",
    };
    return (issue) => {
        switch (issue.code) {
            case "invalid_type":
                return `無效的輸入值：預期為 ${issue.expected}，但收到 ${parsedType(issue.input)}`;
            case "invalid_value":
                if (issue.values.length === 1)
                    return `無效的輸入值：預期為 ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.stringifyPrimitive(issue.values[0])}`;
                return `無效的選項：預期為以下其中之一 ${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.values, "|")}`;
            case "too_big": {
                const adj = issue.inclusive ? "<=" : "<";
                const sizing = getSizing(issue.origin);
                if (sizing)
                    return `數值過大：預期 ${issue.origin ?? "值"} 應為 ${adj}${issue.maximum.toString()} ${sizing.unit ?? "個元素"}`;
                return `數值過大：預期 ${issue.origin ?? "值"} 應為 ${adj}${issue.maximum.toString()}`;
            }
            case "too_small": {
                const adj = issue.inclusive ? ">=" : ">";
                const sizing = getSizing(issue.origin);
                if (sizing) {
                    return `數值過小：預期 ${issue.origin} 應為 ${adj}${issue.minimum.toString()} ${sizing.unit}`;
                }
                return `數值過小：預期 ${issue.origin} 應為 ${adj}${issue.minimum.toString()}`;
            }
            case "invalid_format": {
                const _issue = issue;
                if (_issue.format === "starts_with") {
                    return `無效的字串：必須以 "${_issue.prefix}" 開頭`;
                }
                if (_issue.format === "ends_with")
                    return `無效的字串：必須以 "${_issue.suffix}" 結尾`;
                if (_issue.format === "includes")
                    return `無效的字串：必須包含 "${_issue.includes}"`;
                if (_issue.format === "regex")
                    return `無效的字串：必須符合格式 ${_issue.pattern}`;
                return `無效的 ${Nouns[_issue.format] ?? issue.format}`;
            }
            case "not_multiple_of":
                return `無效的數字：必須為 ${issue.divisor} 的倍數`;
            case "unrecognized_keys":
                return `無法識別的鍵值${issue.keys.length > 1 ? "們" : ""}：${_core_util_js__WEBPACK_IMPORTED_MODULE_0__.joinValues(issue.keys, "、")}`;
            case "invalid_key":
                return `${issue.origin} 中有無效的鍵值`;
            case "invalid_union":
                return "無效的輸入值";
            case "invalid_element":
                return `${issue.origin} 中有無效的值`;
            default:
                return `無效的輸入值`;
        }
    };
};
/* harmony default export */ function __WEBPACK_DEFAULT_EXPORT__() {
    return {
        localeError: error(),
    };
}


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
!function() {
/*!****************************************************!*\
  !*** ./packages/packages/libs/schema/src/index.ts ***!
  \****************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   z: function() { return /* reexport module object */ zod__WEBPACK_IMPORTED_MODULE_0__; },
/* harmony export */   z4: function() { return /* reexport module object */ zod_v4__WEBPACK_IMPORTED_MODULE_1__; }
/* harmony export */ });
/* harmony import */ var zod__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! zod */ "./node_modules/zod/v3/external.js");
/* harmony import */ var zod_v4__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! zod/v4 */ "./node_modules/zod/v4/classic/external.js");


}();
(window.elementorV2 = window.elementorV2 || {}).schema = __webpack_exports__;
/******/ })()
;
window.elementorV2.schema?.init?.();
//# sourceMappingURL=schema.js.map
/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/jsonschema/lib/attribute.js":
/*!**************************************************!*\
  !*** ./node_modules/jsonschema/lib/attribute.js ***!
  \**************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {



var helpers = __webpack_require__(/*! ./helpers */ "./node_modules/jsonschema/lib/helpers.js");

/** @type ValidatorResult */
var ValidatorResult = helpers.ValidatorResult;
/** @type SchemaError */
var SchemaError = helpers.SchemaError;

var attribute = {};

attribute.ignoreProperties = {
  // informative properties
  'id': true,
  'default': true,
  'description': true,
  'title': true,
  // arguments to other properties
  'additionalItems': true,
  'then': true,
  'else': true,
  // special-handled properties
  '$schema': true,
  '$ref': true,
  'extends': true,
};

/**
 * @name validators
 */
var validators = attribute.validators = {};

/**
 * Validates whether the instance if of a certain type
 * @param instance
 * @param schema
 * @param options
 * @param ctx
 * @return {ValidatorResult|null}
 */
validators.type = function validateType (instance, schema, options, ctx) {
  // Ignore undefined instances
  if (instance === undefined) {
    return null;
  }
  var result = new ValidatorResult(instance, schema, options, ctx);
  var types = Array.isArray(schema.type) ? schema.type : [schema.type];
  if (!types.some(this.testType.bind(this, instance, schema, options, ctx))) {
    var list = types.map(function (v) {
      if(!v) return;
      var id = v.$id || v.id;
      return id ? ('<' + id + '>') : (v+'');
    });
    result.addError({
      name: 'type',
      argument: list,
      message: "is not of a type(s) " + list,
    });
  }
  return result;
};

function testSchemaNoThrow(instance, options, ctx, callback, schema){
  var throwError = options.throwError;
  var throwAll = options.throwAll;
  options.throwError = false;
  options.throwAll = false;
  var res = this.validateSchema(instance, schema, options, ctx);
  options.throwError = throwError;
  options.throwAll = throwAll;

  if (!res.valid && callback instanceof Function) {
    callback(res);
  }
  return res.valid;
}

/**
 * Validates whether the instance matches some of the given schemas
 * @param instance
 * @param schema
 * @param options
 * @param ctx
 * @return {ValidatorResult|null}
 */
validators.anyOf = function validateAnyOf (instance, schema, options, ctx) {
  // Ignore undefined instances
  if (instance === undefined) {
    return null;
  }
  var result = new ValidatorResult(instance, schema, options, ctx);
  var inner = new ValidatorResult(instance, schema, options, ctx);
  if (!Array.isArray(schema.anyOf)){
    throw new SchemaError("anyOf must be an array");
  }
  if (!schema.anyOf.some(
    testSchemaNoThrow.bind(
      this, instance, options, ctx, function(res){inner.importErrors(res);}
    ))) {
    var list = schema.anyOf.map(function (v, i) {
      var id = v.$id || v.id;
      if(id) return '<' + id + '>';
      return(v.title && JSON.stringify(v.title)) || (v['$ref'] && ('<' + v['$ref'] + '>')) || '[subschema '+i+']';
    });
    if (options.nestedErrors) {
      result.importErrors(inner);
    }
    result.addError({
      name: 'anyOf',
      argument: list,
      message: "is not any of " + list.join(','),
    });
  }
  return result;
};

/**
 * Validates whether the instance matches every given schema
 * @param instance
 * @param schema
 * @param options
 * @param ctx
 * @return {String|null}
 */
validators.allOf = function validateAllOf (instance, schema, options, ctx) {
  // Ignore undefined instances
  if (instance === undefined) {
    return null;
  }
  if (!Array.isArray(schema.allOf)){
    throw new SchemaError("allOf must be an array");
  }
  var result = new ValidatorResult(instance, schema, options, ctx);
  var self = this;
  schema.allOf.forEach(function(v, i){
    var valid = self.validateSchema(instance, v, options, ctx);
    if(!valid.valid){
      var id = v.$id || v.id;
      var msg = id || (v.title && JSON.stringify(v.title)) || (v['$ref'] && ('<' + v['$ref'] + '>')) || '[subschema '+i+']';
      result.addError({
        name: 'allOf',
        argument: { id: msg, length: valid.errors.length, valid: valid },
        message: 'does not match allOf schema ' + msg + ' with ' + valid.errors.length + ' error[s]:',
      });
      result.importErrors(valid);
    }
  });
  return result;
};

/**
 * Validates whether the instance matches exactly one of the given schemas
 * @param instance
 * @param schema
 * @param options
 * @param ctx
 * @return {String|null}
 */
validators.oneOf = function validateOneOf (instance, schema, options, ctx) {
  // Ignore undefined instances
  if (instance === undefined) {
    return null;
  }
  if (!Array.isArray(schema.oneOf)){
    throw new SchemaError("oneOf must be an array");
  }
  var result = new ValidatorResult(instance, schema, options, ctx);
  var inner = new ValidatorResult(instance, schema, options, ctx);
  var count = schema.oneOf.filter(
    testSchemaNoThrow.bind(
      this, instance, options, ctx, function(res) {inner.importErrors(res);}
    ) ).length;
  var list = schema.oneOf.map(function (v, i) {
    var id = v.$id || v.id;
    return id || (v.title && JSON.stringify(v.title)) || (v['$ref'] && ('<' + v['$ref'] + '>')) || '[subschema '+i+']';
  });
  if (count!==1) {
    if (options.nestedErrors) {
      result.importErrors(inner);
    }
    result.addError({
      name: 'oneOf',
      argument: list,
      message: "is not exactly one from " + list.join(','),
    });
  }
  return result;
};

/**
 * Validates "then" or "else" depending on the result of validating "if"
 * @param instance
 * @param schema
 * @param options
 * @param ctx
 * @return {String|null}
 */
validators.if = function validateIf (instance, schema, options, ctx) {
  // Ignore undefined instances
  if (instance === undefined) return null;
  if (!helpers.isSchema(schema.if)) throw new Error('Expected "if" keyword to be a schema');
  var ifValid = testSchemaNoThrow.call(this, instance, options, ctx, null, schema.if);
  var result = new ValidatorResult(instance, schema, options, ctx);
  var res;
  if(ifValid){
    if (schema.then === undefined) return;
    if (!helpers.isSchema(schema.then)) throw new Error('Expected "then" keyword to be a schema');
    res = this.validateSchema(instance, schema.then, options, ctx.makeChild(schema.then));
    result.importErrors(res);
  }else{
    if (schema.else === undefined) return;
    if (!helpers.isSchema(schema.else)) throw new Error('Expected "else" keyword to be a schema');
    res = this.validateSchema(instance, schema.else, options, ctx.makeChild(schema.else));
    result.importErrors(res);
  }
  return result;
};

function getEnumerableProperty(object, key){
  // Determine if `key` shows up in `for(var key in object)`
  // First test Object.hasOwnProperty.call as an optimization: that guarantees it does
  if(Object.hasOwnProperty.call(object, key)) return object[key];
  // Test `key in object` as an optimization; false means it won't
  if(!(key in object)) return;
  while( (object = Object.getPrototypeOf(object)) ){
    if(Object.propertyIsEnumerable.call(object, key)) return object[key];
  }
}

/**
 * Validates propertyNames
 * @param instance
 * @param schema
 * @param options
 * @param ctx
 * @return {String|null|ValidatorResult}
 */
validators.propertyNames = function validatePropertyNames (instance, schema, options, ctx) {
  if(!this.types.object(instance)) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  var subschema = schema.propertyNames!==undefined ? schema.propertyNames : {};
  if(!helpers.isSchema(subschema)) throw new SchemaError('Expected "propertyNames" to be a schema (object or boolean)');

  for (var property in instance) {
    if(getEnumerableProperty(instance, property) !== undefined){
      var res = this.validateSchema(property, subschema, options, ctx.makeChild(subschema));
      result.importErrors(res);
    }
  }

  return result;
};

/**
 * Validates properties
 * @param instance
 * @param schema
 * @param options
 * @param ctx
 * @return {String|null|ValidatorResult}
 */
validators.properties = function validateProperties (instance, schema, options, ctx) {
  if(!this.types.object(instance)) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  var properties = schema.properties || {};
  for (var property in properties) {
    var subschema = properties[property];
    if(subschema===undefined){
      continue;
    }else if(subschema===null){
      throw new SchemaError('Unexpected null, expected schema in "properties"');
    }
    if (typeof options.preValidateProperty == 'function') {
      options.preValidateProperty(instance, property, subschema, options, ctx);
    }
    var prop = getEnumerableProperty(instance, property);
    var res = this.validateSchema(prop, subschema, options, ctx.makeChild(subschema, property));
    if(res.instance !== result.instance[property]) result.instance[property] = res.instance;
    result.importErrors(res);
  }
  return result;
};

/**
 * Test a specific property within in instance against the additionalProperties schema attribute
 * This ignores properties with definitions in the properties schema attribute, but no other attributes.
 * If too many more types of property-existence tests pop up they may need their own class of tests (like `type` has)
 * @private
 * @return {boolean}
 */
function testAdditionalProperty (instance, schema, options, ctx, property, result) {
  if(!this.types.object(instance)) return;
  if (schema.properties && schema.properties[property] !== undefined) {
    return;
  }
  if (schema.additionalProperties === false) {
    result.addError({
      name: 'additionalProperties',
      argument: property,
      message: "is not allowed to have the additional property " + JSON.stringify(property),
    });
  } else {
    var additionalProperties = schema.additionalProperties || {};

    if (typeof options.preValidateProperty == 'function') {
      options.preValidateProperty(instance, property, additionalProperties, options, ctx);
    }

    var res = this.validateSchema(instance[property], additionalProperties, options, ctx.makeChild(additionalProperties, property));
    if(res.instance !== result.instance[property]) result.instance[property] = res.instance;
    result.importErrors(res);
  }
}

/**
 * Validates patternProperties
 * @param instance
 * @param schema
 * @param options
 * @param ctx
 * @return {String|null|ValidatorResult}
 */
validators.patternProperties = function validatePatternProperties (instance, schema, options, ctx) {
  if(!this.types.object(instance)) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  var patternProperties = schema.patternProperties || {};

  for (var property in instance) {
    var test = true;
    for (var pattern in patternProperties) {
      var subschema = patternProperties[pattern];
      if(subschema===undefined){
        continue;
      }else if(subschema===null){
        throw new SchemaError('Unexpected null, expected schema in "patternProperties"');
      }
      try {
        var regexp = new RegExp(pattern, 'u');
      } catch(_e) {
        // In the event the stricter handling causes an error, fall back on the forgiving handling
        // DEPRECATED
        regexp = new RegExp(pattern);
      }
      if (!regexp.test(property)) {
        continue;
      }
      test = false;

      if (typeof options.preValidateProperty == 'function') {
        options.preValidateProperty(instance, property, subschema, options, ctx);
      }

      var res = this.validateSchema(instance[property], subschema, options, ctx.makeChild(subschema, property));
      if(res.instance !== result.instance[property]) result.instance[property] = res.instance;
      result.importErrors(res);
    }
    if (test) {
      testAdditionalProperty.call(this, instance, schema, options, ctx, property, result);
    }
  }

  return result;
};

/**
 * Validates additionalProperties
 * @param instance
 * @param schema
 * @param options
 * @param ctx
 * @return {String|null|ValidatorResult}
 */
validators.additionalProperties = function validateAdditionalProperties (instance, schema, options, ctx) {
  if(!this.types.object(instance)) return;
  // if patternProperties is defined then we'll test when that one is called instead
  if (schema.patternProperties) {
    return null;
  }
  var result = new ValidatorResult(instance, schema, options, ctx);
  for (var property in instance) {
    testAdditionalProperty.call(this, instance, schema, options, ctx, property, result);
  }
  return result;
};

/**
 * Validates whether the instance value is at least of a certain length, when the instance value is a string.
 * @param instance
 * @param schema
 * @return {String|null}
 */
validators.minProperties = function validateMinProperties (instance, schema, options, ctx) {
  if (!this.types.object(instance)) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  var keys = Object.keys(instance);
  if (!(keys.length >= schema.minProperties)) {
    result.addError({
      name: 'minProperties',
      argument: schema.minProperties,
      message: "does not meet minimum property length of " + schema.minProperties,
    });
  }
  return result;
};

/**
 * Validates whether the instance value is at most of a certain length, when the instance value is a string.
 * @param instance
 * @param schema
 * @return {String|null}
 */
validators.maxProperties = function validateMaxProperties (instance, schema, options, ctx) {
  if (!this.types.object(instance)) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  var keys = Object.keys(instance);
  if (!(keys.length <= schema.maxProperties)) {
    result.addError({
      name: 'maxProperties',
      argument: schema.maxProperties,
      message: "does not meet maximum property length of " + schema.maxProperties,
    });
  }
  return result;
};

/**
 * Validates items when instance is an array
 * @param instance
 * @param schema
 * @param options
 * @param ctx
 * @return {String|null|ValidatorResult}
 */
validators.items = function validateItems (instance, schema, options, ctx) {
  var self = this;
  if (!this.types.array(instance)) return;
  if (schema.items===undefined) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  instance.every(function (value, i) {
    if(Array.isArray(schema.items)){
      var items =  schema.items[i]===undefined ? schema.additionalItems : schema.items[i];
    }else{
      var items = schema.items;
    }
    if (items === undefined) {
      return true;
    }
    if (items === false) {
      result.addError({
        name: 'items',
        message: "additionalItems not permitted",
      });
      return false;
    }
    var res = self.validateSchema(value, items, options, ctx.makeChild(items, i));
    if(res.instance !== result.instance[i]) result.instance[i] = res.instance;
    result.importErrors(res);
    return true;
  });
  return result;
};

/**
 * Validates the "contains" keyword
 * @param instance
 * @param schema
 * @param options
 * @param ctx
 * @return {String|null|ValidatorResult}
 */
validators.contains = function validateContains (instance, schema, options, ctx) {
  var self = this;
  if (!this.types.array(instance)) return;
  if (schema.contains===undefined) return;
  if (!helpers.isSchema(schema.contains)) throw new Error('Expected "contains" keyword to be a schema');
  var result = new ValidatorResult(instance, schema, options, ctx);
  var count = instance.some(function (value, i) {
    var res = self.validateSchema(value, schema.contains, options, ctx.makeChild(schema.contains, i));
    return res.errors.length===0;
  });
  if(count===false){
    result.addError({
      name: 'contains',
      argument: schema.contains,
      message: "must contain an item matching given schema",
    });
  }
  return result;
};

/**
 * Validates minimum and exclusiveMinimum when the type of the instance value is a number.
 * @param instance
 * @param schema
 * @return {String|null}
 */
validators.minimum = function validateMinimum (instance, schema, options, ctx) {
  if (!this.types.number(instance)) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  if (schema.exclusiveMinimum && schema.exclusiveMinimum === true) {
    if(!(instance > schema.minimum)){
      result.addError({
        name: 'minimum',
        argument: schema.minimum,
        message: "must be greater than " + schema.minimum,
      });
    }
  } else {
    if(!(instance >= schema.minimum)){
      result.addError({
        name: 'minimum',
        argument: schema.minimum,
        message: "must be greater than or equal to " + schema.minimum,
      });
    }
  }
  return result;
};

/**
 * Validates maximum and exclusiveMaximum when the type of the instance value is a number.
 * @param instance
 * @param schema
 * @return {String|null}
 */
validators.maximum = function validateMaximum (instance, schema, options, ctx) {
  if (!this.types.number(instance)) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  if (schema.exclusiveMaximum && schema.exclusiveMaximum === true) {
    if(!(instance < schema.maximum)){
      result.addError({
        name: 'maximum',
        argument: schema.maximum,
        message: "must be less than " + schema.maximum,
      });
    }
  } else {
    if(!(instance <= schema.maximum)){
      result.addError({
        name: 'maximum',
        argument: schema.maximum,
        message: "must be less than or equal to " + schema.maximum,
      });
    }
  }
  return result;
};

/**
 * Validates the number form of exclusiveMinimum when the type of the instance value is a number.
 * @param instance
 * @param schema
 * @return {String|null}
 */
validators.exclusiveMinimum = function validateExclusiveMinimum (instance, schema, options, ctx) {
  // Support the boolean form of exclusiveMinimum, which is handled by the "minimum" keyword.
  if(typeof schema.exclusiveMinimum === 'boolean') return;
  if (!this.types.number(instance)) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  var valid = instance > schema.exclusiveMinimum;
  if (!valid) {
    result.addError({
      name: 'exclusiveMinimum',
      argument: schema.exclusiveMinimum,
      message: "must be strictly greater than " + schema.exclusiveMinimum,
    });
  }
  return result;
};

/**
 * Validates the number form of exclusiveMaximum when the type of the instance value is a number.
 * @param instance
 * @param schema
 * @return {String|null}
 */
validators.exclusiveMaximum = function validateExclusiveMaximum (instance, schema, options, ctx) {
  // Support the boolean form of exclusiveMaximum, which is handled by the "maximum" keyword.
  if(typeof schema.exclusiveMaximum === 'boolean') return;
  if (!this.types.number(instance)) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  var valid = instance < schema.exclusiveMaximum;
  if (!valid) {
    result.addError({
      name: 'exclusiveMaximum',
      argument: schema.exclusiveMaximum,
      message: "must be strictly less than " + schema.exclusiveMaximum,
    });
  }
  return result;
};

/**
 * Perform validation for multipleOf and divisibleBy, which are essentially the same.
 * @param instance
 * @param schema
 * @param validationType
 * @param errorMessage
 * @returns {String|null}
 */
var validateMultipleOfOrDivisbleBy = function validateMultipleOfOrDivisbleBy (instance, schema, options, ctx, validationType, errorMessage) {
  if (!this.types.number(instance)) return;

  var validationArgument = schema[validationType];
  if (validationArgument == 0) {
    throw new SchemaError(validationType + " cannot be zero");
  }

  var result = new ValidatorResult(instance, schema, options, ctx);

  var instanceDecimals = helpers.getDecimalPlaces(instance);
  var divisorDecimals = helpers.getDecimalPlaces(validationArgument);

  var maxDecimals = Math.max(instanceDecimals , divisorDecimals);
  var multiplier = Math.pow(10, maxDecimals);

  if (Math.round(instance * multiplier) % Math.round(validationArgument * multiplier) !== 0) {
    result.addError({
      name: validationType,
      argument:  validationArgument,
      message: errorMessage + JSON.stringify(validationArgument),
    });
  }

  return result;
};

/**
 * Validates divisibleBy when the type of the instance value is a number.
 * @param instance
 * @param schema
 * @return {String|null}
 */
validators.multipleOf = function validateMultipleOf (instance, schema, options, ctx) {
  return validateMultipleOfOrDivisbleBy.call(this, instance, schema, options, ctx, "multipleOf", "is not a multiple of (divisible by) ");
};

/**
 * Validates multipleOf when the type of the instance value is a number.
 * @param instance
 * @param schema
 * @return {String|null}
 */
validators.divisibleBy = function validateDivisibleBy (instance, schema, options, ctx) {
  return validateMultipleOfOrDivisbleBy.call(this, instance, schema, options, ctx, "divisibleBy", "is not divisible by (multiple of) ");
};

/**
 * Validates whether the instance value is present.
 * @param instance
 * @param schema
 * @return {String|null}
 */
validators.required = function validateRequired (instance, schema, options, ctx) {
  var result = new ValidatorResult(instance, schema, options, ctx);
  if (instance === undefined && schema.required === true) {
    // A boolean form is implemented for reverse-compatibility with schemas written against older drafts
    result.addError({
      name: 'required',
      message: "is required",
    });
  } else if (this.types.object(instance) && Array.isArray(schema.required)) {
    schema.required.forEach(function(n){
      if(getEnumerableProperty(instance, n)===undefined){
        result.addError({
          name: 'required',
          argument: n,
          message: "requires property " + JSON.stringify(n),
        });
      }
    });
  }
  return result;
};

/**
 * Validates whether the instance value matches the regular expression, when the instance value is a string.
 * @param instance
 * @param schema
 * @return {String|null}
 */
validators.pattern = function validatePattern (instance, schema, options, ctx) {
  if (!this.types.string(instance)) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  var pattern = schema.pattern;
  try {
    var regexp = new RegExp(pattern, 'u');
  } catch(_e) {
    // In the event the stricter handling causes an error, fall back on the forgiving handling
    // DEPRECATED
    regexp = new RegExp(pattern);
  }
  if (!instance.match(regexp)) {
    result.addError({
      name: 'pattern',
      argument: schema.pattern,
      message: "does not match pattern " + JSON.stringify(schema.pattern.toString()),
    });
  }
  return result;
};

/**
 * Validates whether the instance value is of a certain defined format or a custom
 * format.
 * The following formats are supported for string types:
 *   - date-time
 *   - date
 *   - time
 *   - ip-address
 *   - ipv6
 *   - uri
 *   - color
 *   - host-name
 *   - alpha
 *   - alpha-numeric
 *   - utc-millisec
 * @param instance
 * @param schema
 * @param [options]
 * @param [ctx]
 * @return {String|null}
 */
validators.format = function validateFormat (instance, schema, options, ctx) {
  if (instance===undefined) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  if (!result.disableFormat && !helpers.isFormat(instance, schema.format, this)) {
    result.addError({
      name: 'format',
      argument: schema.format,
      message: "does not conform to the " + JSON.stringify(schema.format) + " format",
    });
  }
  return result;
};

/**
 * Validates whether the instance value is at least of a certain length, when the instance value is a string.
 * @param instance
 * @param schema
 * @return {String|null}
 */
validators.minLength = function validateMinLength (instance, schema, options, ctx) {
  if (!this.types.string(instance)) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  var hsp = instance.match(/[\uDC00-\uDFFF]/g);
  var length = instance.length - (hsp ? hsp.length : 0);
  if (!(length >= schema.minLength)) {
    result.addError({
      name: 'minLength',
      argument: schema.minLength,
      message: "does not meet minimum length of " + schema.minLength,
    });
  }
  return result;
};

/**
 * Validates whether the instance value is at most of a certain length, when the instance value is a string.
 * @param instance
 * @param schema
 * @return {String|null}
 */
validators.maxLength = function validateMaxLength (instance, schema, options, ctx) {
  if (!this.types.string(instance)) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  // TODO if this was already computed in "minLength", use that value instead of re-computing
  var hsp = instance.match(/[\uDC00-\uDFFF]/g);
  var length = instance.length - (hsp ? hsp.length : 0);
  if (!(length <= schema.maxLength)) {
    result.addError({
      name: 'maxLength',
      argument: schema.maxLength,
      message: "does not meet maximum length of " + schema.maxLength,
    });
  }
  return result;
};

/**
 * Validates whether instance contains at least a minimum number of items, when the instance is an Array.
 * @param instance
 * @param schema
 * @return {String|null}
 */
validators.minItems = function validateMinItems (instance, schema, options, ctx) {
  if (!this.types.array(instance)) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  if (!(instance.length >= schema.minItems)) {
    result.addError({
      name: 'minItems',
      argument: schema.minItems,
      message: "does not meet minimum length of " + schema.minItems,
    });
  }
  return result;
};

/**
 * Validates whether instance contains no more than a maximum number of items, when the instance is an Array.
 * @param instance
 * @param schema
 * @return {String|null}
 */
validators.maxItems = function validateMaxItems (instance, schema, options, ctx) {
  if (!this.types.array(instance)) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  if (!(instance.length <= schema.maxItems)) {
    result.addError({
      name: 'maxItems',
      argument: schema.maxItems,
      message: "does not meet maximum length of " + schema.maxItems,
    });
  }
  return result;
};

/**
 * Deep compares arrays for duplicates
 * @param v
 * @param i
 * @param a
 * @private
 * @return {boolean}
 */
function testArrays (v, i, a) {
  var j, len = a.length;
  for (j = i + 1, len; j < len; j++) {
    if (helpers.deepCompareStrict(v, a[j])) {
      return false;
    }
  }
  return true;
}

/**
 * Validates whether there are no duplicates, when the instance is an Array.
 * @param instance
 * @return {String|null}
 */
validators.uniqueItems = function validateUniqueItems (instance, schema, options, ctx) {
  if (schema.uniqueItems!==true) return;
  if (!this.types.array(instance)) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  if (!instance.every(testArrays)) {
    result.addError({
      name: 'uniqueItems',
      message: "contains duplicate item",
    });
  }
  return result;
};

/**
 * Validate for the presence of dependency properties, if the instance is an object.
 * @param instance
 * @param schema
 * @param options
 * @param ctx
 * @return {null|ValidatorResult}
 */
validators.dependencies = function validateDependencies (instance, schema, options, ctx) {
  if (!this.types.object(instance)) return;
  var result = new ValidatorResult(instance, schema, options, ctx);
  for (var property in schema.dependencies) {
    if (instance[property] === undefined) {
      continue;
    }
    var dep = schema.dependencies[property];
    var childContext = ctx.makeChild(dep, property);
    if (typeof dep == 'string') {
      dep = [dep];
    }
    if (Array.isArray(dep)) {
      dep.forEach(function (prop) {
        if (instance[prop] === undefined) {
          result.addError({
            // FIXME there's two different "dependencies" errors here with slightly different outputs
            // Can we make these the same? Or should we create different error types?
            name: 'dependencies',
            argument: childContext.propertyPath,
            message: "property " + prop + " not found, required by " + childContext.propertyPath,
          });
        }
      });
    } else {
      var res = this.validateSchema(instance, dep, options, childContext);
      if(result.instance !== res.instance) result.instance = res.instance;
      if (res && res.errors.length) {
        result.addError({
          name: 'dependencies',
          argument: childContext.propertyPath,
          message: "does not meet dependency required by " + childContext.propertyPath,
        });
        result.importErrors(res);
      }
    }
  }
  return result;
};

/**
 * Validates whether the instance value is one of the enumerated values.
 *
 * @param instance
 * @param schema
 * @return {ValidatorResult|null}
 */
validators['enum'] = function validateEnum (instance, schema, options, ctx) {
  if (instance === undefined) {
    return null;
  }
  if (!Array.isArray(schema['enum'])) {
    throw new SchemaError("enum expects an array", schema);
  }
  var result = new ValidatorResult(instance, schema, options, ctx);
  if (!schema['enum'].some(helpers.deepCompareStrict.bind(null, instance))) {
    result.addError({
      name: 'enum',
      argument: schema['enum'],
      message: "is not one of enum values: " + schema['enum'].map(String).join(','),
    });
  }
  return result;
};

/**
 * Validates whether the instance exactly matches a given value
 *
 * @param instance
 * @param schema
 * @return {ValidatorResult|null}
 */
validators['const'] = function validateEnum (instance, schema, options, ctx) {
  if (instance === undefined) {
    return null;
  }
  var result = new ValidatorResult(instance, schema, options, ctx);
  if (!helpers.deepCompareStrict(schema['const'], instance)) {
    result.addError({
      name: 'const',
      argument: schema['const'],
      message: "does not exactly match expected constant: " + schema['const'],
    });
  }
  return result;
};

/**
 * Validates whether the instance if of a prohibited type.
 * @param instance
 * @param schema
 * @param options
 * @param ctx
 * @return {null|ValidatorResult}
 */
validators.not = validators.disallow = function validateNot (instance, schema, options, ctx) {
  var self = this;
  if(instance===undefined) return null;
  var result = new ValidatorResult(instance, schema, options, ctx);
  var notTypes = schema.not || schema.disallow;
  if(!notTypes) return null;
  if(!Array.isArray(notTypes)) notTypes=[notTypes];
  notTypes.forEach(function (type) {
    if (self.testType(instance, schema, options, ctx, type)) {
      var id = type && (type.$id || type.id);
      var schemaId = id || type;
      result.addError({
        name: 'not',
        argument: schemaId,
        message: "is of prohibited type " + schemaId,
      });
    }
  });
  return result;
};

module.exports = attribute;


/***/ }),

/***/ "./node_modules/jsonschema/lib/helpers.js":
/*!************************************************!*\
  !*** ./node_modules/jsonschema/lib/helpers.js ***!
  \************************************************/
/***/ (function(module, exports) {



var ValidationError = exports.ValidationError = function ValidationError (message, instance, schema, path, name, argument) {
  if(Array.isArray(path)){
    this.path = path;
    this.property = path.reduce(function(sum, item){
      return sum + makeSuffix(item);
    }, 'instance');
  }else if(path !== undefined){
    this.property = path;
  }
  if (message) {
    this.message = message;
  }
  if (schema) {
    var id = schema.$id || schema.id;
    this.schema = id || schema;
  }
  if (instance !== undefined) {
    this.instance = instance;
  }
  this.name = name;
  this.argument = argument;
  this.stack = this.toString();
};

ValidationError.prototype.toString = function toString() {
  return this.property + ' ' + this.message;
};

var ValidatorResult = exports.ValidatorResult = function ValidatorResult(instance, schema, options, ctx) {
  this.instance = instance;
  this.schema = schema;
  this.options = options;
  this.path = ctx.path;
  this.propertyPath = ctx.propertyPath;
  this.errors = [];
  this.throwError = options && options.throwError;
  this.throwFirst = options && options.throwFirst;
  this.throwAll = options && options.throwAll;
  this.disableFormat = options && options.disableFormat === true;
};

ValidatorResult.prototype.addError = function addError(detail) {
  var err;
  if (typeof detail == 'string') {
    err = new ValidationError(detail, this.instance, this.schema, this.path);
  } else {
    if (!detail) throw new Error('Missing error detail');
    if (!detail.message) throw new Error('Missing error message');
    if (!detail.name) throw new Error('Missing validator type');
    err = new ValidationError(detail.message, this.instance, this.schema, this.path, detail.name, detail.argument);
  }

  this.errors.push(err);
  if (this.throwFirst) {
    throw new ValidatorResultError(this);
  }else if(this.throwError){
    throw err;
  }
  return err;
};

ValidatorResult.prototype.importErrors = function importErrors(res) {
  if (typeof res == 'string' || (res && res.validatorType)) {
    this.addError(res);
  } else if (res && res.errors) {
    this.errors = this.errors.concat(res.errors);
  }
};

function stringizer (v,i){
  return i+': '+v.toString()+'\n';
}
ValidatorResult.prototype.toString = function toString(res) {
  return this.errors.map(stringizer).join('');
};

Object.defineProperty(ValidatorResult.prototype, "valid", { get: function() {
  return !this.errors.length;
} });

module.exports.ValidatorResultError = ValidatorResultError;
function ValidatorResultError(result) {
  if(typeof Error.captureStackTrace === 'function'){
    Error.captureStackTrace(this, ValidatorResultError);
  }
  this.instance = result.instance;
  this.schema = result.schema;
  this.options = result.options;
  this.errors = result.errors;
}
ValidatorResultError.prototype = new Error();
ValidatorResultError.prototype.constructor = ValidatorResultError;
ValidatorResultError.prototype.name = "Validation Error";

/**
 * Describes a problem with a Schema which prevents validation of an instance
 * @name SchemaError
 * @constructor
 */
var SchemaError = exports.SchemaError = function SchemaError (msg, schema) {
  this.message = msg;
  this.schema = schema;
  Error.call(this, msg);
  if(typeof Error.captureStackTrace === 'function'){
    Error.captureStackTrace(this, SchemaError);
  }
};
SchemaError.prototype = Object.create(Error.prototype,
  {
    constructor: {value: SchemaError, enumerable: false},
    name: {value: 'SchemaError', enumerable: false},
  });

var SchemaContext = exports.SchemaContext = function SchemaContext (schema, options, path, base, schemas) {
  this.schema = schema;
  this.options = options;
  if(Array.isArray(path)){
    this.path = path;
    this.propertyPath = path.reduce(function(sum, item){
      return sum + makeSuffix(item);
    }, 'instance');
  }else{
    this.propertyPath = path;
  }
  this.base = base;
  this.schemas = schemas;
};

SchemaContext.prototype.resolve = function resolve (target) {
  return (() => resolveUrl(this.base,target))();
};

SchemaContext.prototype.makeChild = function makeChild(schema, propertyName){
  var path = (propertyName===undefined) ? this.path : this.path.concat([propertyName]);
  var id = schema.$id || schema.id;
  let base = (() => resolveUrl(this.base,id||''))();
  var ctx = new SchemaContext(schema, this.options, path, base, Object.create(this.schemas));
  if(id && !ctx.schemas[base]){
    ctx.schemas[base] = schema;
  }
  return ctx;
};

var FORMAT_REGEXPS = exports.FORMAT_REGEXPS = {
  // 7.3.1. Dates, Times, and Duration
  'date-time': /^\d{4}-(?:0[0-9]{1}|1[0-2]{1})-(3[01]|0[1-9]|[12][0-9])[tT ](2[0-4]|[01][0-9]):([0-5][0-9]):(60|[0-5][0-9])(\.\d+)?([zZ]|[+-]([0-5][0-9]):(60|[0-5][0-9]))$/,
  'date': /^\d{4}-(?:0[0-9]{1}|1[0-2]{1})-(3[01]|0[1-9]|[12][0-9])$/,
  'time': /^(2[0-4]|[01][0-9]):([0-5][0-9]):(60|[0-5][0-9])$/,
  'duration': /P(T\d+(H(\d+M(\d+S)?)?|M(\d+S)?|S)|\d+(D|M(\d+D)?|Y(\d+M(\d+D)?)?)(T\d+(H(\d+M(\d+S)?)?|M(\d+S)?|S))?|\d+W)/i,

  // 7.3.2. Email Addresses
  // TODO: fix the email production
  'email': /^(?:[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+\.)*[\w\!\#\$\%\&\'\*\+\-\/\=\?\^\`\{\|\}\~]+@(?:(?:(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-](?!\.)){0,61}[a-zA-Z0-9]?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9\-](?!$)){0,61}[a-zA-Z0-9]?)|(?:\[(?:(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\.){3}(?:[01]?\d{1,2}|2[0-4]\d|25[0-5])\]))$/,
  'idn-email': /^("(?:[!#-\[\]-\u{10FFFF}]|\\[\t -\u{10FFFF}])*"|[!#-'*+\-/-9=?A-Z\^-\u{10FFFF}](?:\.?[!#-'*+\-/-9=?A-Z\^-\u{10FFFF}])*)@([!#-'*+\-/-9=?A-Z\^-\u{10FFFF}](?:\.?[!#-'*+\-/-9=?A-Z\^-\u{10FFFF}])*|\[[!-Z\^-\u{10FFFF}]*\])$/u,

  // 7.3.3. Hostnames

  // 7.3.4. IP Addresses
  'ip-address': /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/,
  // FIXME whitespace is invalid
  'ipv6': /^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$/,

  // 7.3.5. Resource Identifiers
  // TODO: A more accurate regular expression for "uri" goes:
  // [A-Za-z][+\-.0-9A-Za-z]*:((/(/((%[0-9A-Fa-f]{2}|[!$&-.0-9;=A-Z_a-z~])+|(\[(([Vv][0-9A-Fa-f]+\.[!$&-.0-;=A-Z_a-z~]+)?|[.0-:A-Fa-f]+)\])?)(:\d*)?)?)?#(%[0-9A-Fa-f]{2}|[!$&-;=?-Z_a-z~])*|(/(/((%[0-9A-Fa-f]{2}|[!$&-.0-9;=A-Z_a-z~])+|(\[(([Vv][0-9A-Fa-f]+\.[!$&-.0-;=A-Z_a-z~]+)?|[.0-:A-Fa-f]+)\])?)(:\d*)?[/?]|[!$&-.0-;=?-Z_a-z~])|/?%[0-9A-Fa-f]{2}|[!$&-.0-;=?-Z_a-z~])(%[0-9A-Fa-f]{2}|[!$&-;=?-Z_a-z~])*(#(%[0-9A-Fa-f]{2}|[!$&-;=?-Z_a-z~])*)?|/(/((%[0-9A-Fa-f]{2}|[!$&-.0-9;=A-Z_a-z~])+(:\d*)?|(\[(([Vv][0-9A-Fa-f]+\.[!$&-.0-;=A-Z_a-z~]+)?|[.0-:A-Fa-f]+)\])?:\d*|\[(([Vv][0-9A-Fa-f]+\.[!$&-.0-;=A-Z_a-z~]+)?|[.0-:A-Fa-f]+)\])?)?)?
  'uri': /^[a-zA-Z][a-zA-Z0-9+.-]*:[^\s]*$/,
  'uri-reference': /^(((([A-Za-z][+\-.0-9A-Za-z]*(:%[0-9A-Fa-f]{2}|:[!$&-.0-;=?-Z_a-z~]|[/?])|\?)(%[0-9A-Fa-f]{2}|[!$&-;=?-Z_a-z~])*|([A-Za-z][+\-.0-9A-Za-z]*:?)?)|([A-Za-z][+\-.0-9A-Za-z]*:)?\/((%[0-9A-Fa-f]{2}|\/((%[0-9A-Fa-f]{2}|[!$&-.0-9;=A-Z_a-z~])+|(\[(([Vv][0-9A-Fa-f]+\.[!$&-.0-;=A-Z_a-z~]+)?|[.0-:A-Fa-f]+)\])?)(:\d*)?[/?]|[!$&-.0-;=?-Z_a-z~])(%[0-9A-Fa-f]{2}|[!$&-;=?-Z_a-z~])*|(\/((%[0-9A-Fa-f]{2}|[!$&-.0-9;=A-Z_a-z~])+|(\[(([Vv][0-9A-Fa-f]+\.[!$&-.0-;=A-Z_a-z~]+)?|[.0-:A-Fa-f]+)\])?)(:\d*)?)?))#(%[0-9A-Fa-f]{2}|[!$&-;=?-Z_a-z~])*|(([A-Za-z][+\-.0-9A-Za-z]*)?%[0-9A-Fa-f]{2}|[!$&-.0-9;=@_~]|[A-Za-z][+\-.0-9A-Za-z]*[!$&-*,;=@_~])(%[0-9A-Fa-f]{2}|[!$&-.0-9;=@-Z_a-z~])*((([/?](%[0-9A-Fa-f]{2}|[!$&-;=?-Z_a-z~])*)?#|[/?])(%[0-9A-Fa-f]{2}|[!$&-;=?-Z_a-z~])*)?|([A-Za-z][+\-.0-9A-Za-z]*(:%[0-9A-Fa-f]{2}|:[!$&-.0-;=?-Z_a-z~]|[/?])|\?)(%[0-9A-Fa-f]{2}|[!$&-;=?-Z_a-z~])*|([A-Za-z][+\-.0-9A-Za-z]*:)?\/((%[0-9A-Fa-f]{2}|\/((%[0-9A-Fa-f]{2}|[!$&-.0-9;=A-Z_a-z~])+|(\[(([Vv][0-9A-Fa-f]+\.[!$&-.0-;=A-Z_a-z~]+)?|[.0-:A-Fa-f]+)\])?)(:\d*)?[/?]|[!$&-.0-;=?-Z_a-z~])(%[0-9A-Fa-f]{2}|[!$&-;=?-Z_a-z~])*|\/((%[0-9A-Fa-f]{2}|[!$&-.0-9;=A-Z_a-z~])+(:\d*)?|(\[(([Vv][0-9A-Fa-f]+\.[!$&-.0-;=A-Z_a-z~]+)?|[.0-:A-Fa-f]+)\])?:\d*|\[(([Vv][0-9A-Fa-f]+\.[!$&-.0-;=A-Z_a-z~]+)?|[.0-:A-Fa-f]+)\])?)?|[A-Za-z][+\-.0-9A-Za-z]*:?)?$/,
  'iri': /^[a-zA-Z][a-zA-Z0-9+.-]*:[^\s]*$/,
  'iri-reference': /^(((([A-Za-z][+\-.0-9A-Za-z]*(:%[0-9A-Fa-f]{2}|:[!$&-.0-;=?-Z_a-z~-\u{10FFFF}]|[/?])|\?)(%[0-9A-Fa-f]{2}|[!$&-;=?-Z_a-z~-\u{10FFFF}])*|([A-Za-z][+\-.0-9A-Za-z]*:?)?)|([A-Za-z][+\-.0-9A-Za-z]*:)?\/((%[0-9A-Fa-f]{2}|\/((%[0-9A-Fa-f]{2}|[!$&-.0-9;=A-Z_a-z~-\u{10FFFF}])+|(\[(([Vv][0-9A-Fa-f]+\.[!$&-.0-;=A-Z_a-z~-\u{10FFFF}]+)?|[.0-:A-Fa-f]+)\])?)(:\d*)?[/?]|[!$&-.0-;=?-Z_a-z~-\u{10FFFF}])(%[0-9A-Fa-f]{2}|[!$&-;=?-Z_a-z~-\u{10FFFF}])*|(\/((%[0-9A-Fa-f]{2}|[!$&-.0-9;=A-Z_a-z~-\u{10FFFF}])+|(\[(([Vv][0-9A-Fa-f]+\.[!$&-.0-;=A-Z_a-z~-\u{10FFFF}]+)?|[.0-:A-Fa-f]+)\])?)(:\d*)?)?))#(%[0-9A-Fa-f]{2}|[!$&-;=?-Z_a-z~-\u{10FFFF}])*|(([A-Za-z][+\-.0-9A-Za-z]*)?%[0-9A-Fa-f]{2}|[!$&-.0-9;=@_~-\u{10FFFF}]|[A-Za-z][+\-.0-9A-Za-z]*[!$&-*,;=@_~-\u{10FFFF}])(%[0-9A-Fa-f]{2}|[!$&-.0-9;=@-Z_a-z~-\u{10FFFF}])*((([/?](%[0-9A-Fa-f]{2}|[!$&-;=?-Z_a-z~-\u{10FFFF}])*)?#|[/?])(%[0-9A-Fa-f]{2}|[!$&-;=?-Z_a-z~-\u{10FFFF}])*)?|([A-Za-z][+\-.0-9A-Za-z]*(:%[0-9A-Fa-f]{2}|:[!$&-.0-;=?-Z_a-z~-\u{10FFFF}]|[/?])|\?)(%[0-9A-Fa-f]{2}|[!$&-;=?-Z_a-z~-\u{10FFFF}])*|([A-Za-z][+\-.0-9A-Za-z]*:)?\/((%[0-9A-Fa-f]{2}|\/((%[0-9A-Fa-f]{2}|[!$&-.0-9;=A-Z_a-z~-\u{10FFFF}])+|(\[(([Vv][0-9A-Fa-f]+\.[!$&-.0-;=A-Z_a-z~-\u{10FFFF}]+)?|[.0-:A-Fa-f]+)\])?)(:\d*)?[/?]|[!$&-.0-;=?-Z_a-z~-\u{10FFFF}])(%[0-9A-Fa-f]{2}|[!$&-;=?-Z_a-z~-\u{10FFFF}])*|\/((%[0-9A-Fa-f]{2}|[!$&-.0-9;=A-Z_a-z~-\u{10FFFF}])+(:\d*)?|(\[(([Vv][0-9A-Fa-f]+\.[!$&-.0-;=A-Z_a-z~-\u{10FFFF}]+)?|[.0-:A-Fa-f]+)\])?:\d*|\[(([Vv][0-9A-Fa-f]+\.[!$&-.0-;=A-Z_a-z~-\u{10FFFF}]+)?|[.0-:A-Fa-f]+)\])?)?|[A-Za-z][+\-.0-9A-Za-z]*:?)?$/u,
  'uuid': /^[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}$/i,

  // 7.3.6. uri-template
  'uri-template': /(%[0-9a-f]{2}|[!#$&(-;=?@\[\]_a-z~]|\{[!#&+,./;=?@|]?(%[0-9a-f]{2}|[0-9_a-z])(\.?(%[0-9a-f]{2}|[0-9_a-z]))*(:[1-9]\d{0,3}|\*)?(,(%[0-9a-f]{2}|[0-9_a-z])(\.?(%[0-9a-f]{2}|[0-9_a-z]))*(:[1-9]\d{0,3}|\*)?)*\})*/iu,

  // 7.3.7. JSON Pointers
  'json-pointer': /^(\/([\x00-\x2e0-@\[-}\x7f]|~[01])*)*$/iu,
  'relative-json-pointer': /^\d+(#|(\/([\x00-\x2e0-@\[-}\x7f]|~[01])*)*)$/iu,

  // hostname regex from: http://stackoverflow.com/a/1420225/5628
  'hostname': /^(?=.{1,255}$)[0-9A-Za-z](?:(?:[0-9A-Za-z]|-){0,61}[0-9A-Za-z])?(?:\.[0-9A-Za-z](?:(?:[0-9A-Za-z]|-){0,61}[0-9A-Za-z])?)*\.?$/,
  'host-name': /^(?=.{1,255}$)[0-9A-Za-z](?:(?:[0-9A-Za-z]|-){0,61}[0-9A-Za-z])?(?:\.[0-9A-Za-z](?:(?:[0-9A-Za-z]|-){0,61}[0-9A-Za-z])?)*\.?$/,

  'utc-millisec': function (input) {
    return (typeof input === 'string') && parseFloat(input) === parseInt(input, 10) && !isNaN(input);
  },

  // 7.3.8. regex
  'regex': function (input) {
    var result = true;
    try {
      new RegExp(input);
    } catch (e) {
      result = false;
    }
    return result;
  },

  // Other definitions
  // "style" was removed from JSON Schema in draft-4 and is deprecated
  'style': /[\r\n\t ]*[^\r\n\t ][^:]*:[\r\n\t ]*[^\r\n\t ;]*[\r\n\t ]*;?/,
  // "color" was removed from JSON Schema in draft-4 and is deprecated
  'color': /^(#?([0-9A-Fa-f]{3}){1,2}\b|aqua|black|blue|fuchsia|gray|green|lime|maroon|navy|olive|orange|purple|red|silver|teal|white|yellow|(rgb\(\s*\b([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\b\s*,\s*\b([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\b\s*,\s*\b([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\b\s*\))|(rgb\(\s*(\d?\d%|100%)+\s*,\s*(\d?\d%|100%)+\s*,\s*(\d?\d%|100%)+\s*\)))$/,
  'phone': /^\+(?:[0-9] ?){6,14}[0-9]$/,
  'alpha': /^[a-zA-Z]+$/,
  'alphanumeric': /^[a-zA-Z0-9]+$/,
};

FORMAT_REGEXPS.regexp = FORMAT_REGEXPS.regex;
FORMAT_REGEXPS.pattern = FORMAT_REGEXPS.regex;
FORMAT_REGEXPS.ipv4 = FORMAT_REGEXPS['ip-address'];

exports.isFormat = function isFormat (input, format, validator) {
  if (typeof input === 'string' && FORMAT_REGEXPS[format] !== undefined) {
    if (FORMAT_REGEXPS[format] instanceof RegExp) {
      return FORMAT_REGEXPS[format].test(input);
    }
    if (typeof FORMAT_REGEXPS[format] === 'function') {
      return FORMAT_REGEXPS[format](input);
    }
  } else if (validator && validator.customFormats &&
      typeof validator.customFormats[format] === 'function') {
    return validator.customFormats[format](input);
  }
  return true;
};

var makeSuffix = exports.makeSuffix = function makeSuffix (key) {
  key = key.toString();
  // This function could be capable of outputting valid a ECMAScript string, but the
  // resulting code for testing which form to use would be tens of thousands of characters long
  // That means this will use the name form for some illegal forms
  if (!key.match(/[.\s\[\]]/) && !key.match(/^[\d]/)) {
    return '.' + key;
  }
  if (key.match(/^\d+$/)) {
    return '[' + key + ']';
  }
  return '[' + JSON.stringify(key) + ']';
};

exports.deepCompareStrict = function deepCompareStrict (a, b) {
  if (typeof a !== typeof b) {
    return false;
  }
  if (Array.isArray(a)) {
    if (!Array.isArray(b)) {
      return false;
    }
    if (a.length !== b.length) {
      return false;
    }
    return a.every(function (v, i) {
      return deepCompareStrict(a[i], b[i]);
    });
  }
  if (typeof a === 'object') {
    if (!a || !b) {
      return a === b;
    }
    var aKeys = Object.keys(a);
    var bKeys = Object.keys(b);
    if (aKeys.length !== bKeys.length) {
      return false;
    }
    return aKeys.every(function (v) {
      return deepCompareStrict(a[v], b[v]);
    });
  }
  return a === b;
};

function deepMerger (target, dst, e, i) {
  if (typeof e === 'object') {
    dst[i] = deepMerge(target[i], e);
  } else {
    if (target.indexOf(e) === -1) {
      dst.push(e);
    }
  }
}

function copyist (src, dst, key) {
  dst[key] = src[key];
}

function copyistWithDeepMerge (target, src, dst, key) {
  if (typeof src[key] !== 'object' || !src[key]) {
    dst[key] = src[key];
  }
  else {
    if (!target[key]) {
      dst[key] = src[key];
    } else {
      dst[key] = deepMerge(target[key], src[key]);
    }
  }
}

function deepMerge (target, src) {
  var array = Array.isArray(src);
  var dst = array && [] || {};

  if (array) {
    target = target || [];
    dst = dst.concat(target);
    src.forEach(deepMerger.bind(null, target, dst));
  } else {
    if (target && typeof target === 'object') {
      Object.keys(target).forEach(copyist.bind(null, target, dst));
    }
    Object.keys(src).forEach(copyistWithDeepMerge.bind(null, target, src, dst));
  }

  return dst;
}

module.exports.deepMerge = deepMerge;

/**
 * Validates instance against the provided schema
 * Implements URI+JSON Pointer encoding, e.g. "%7e"="~0"=>"~", "~1"="%2f"=>"/"
 * @param o
 * @param s The path to walk o along
 * @return any
 */
exports.objectGetPath = function objectGetPath(o, s) {
  var parts = s.split('/').slice(1);
  var k;
  while (typeof (k=parts.shift()) == 'string') {
    var n = decodeURIComponent(k.replace(/~0/,'~').replace(/~1/g,'/'));
    if (!(n in o)) return;
    o = o[n];
  }
  return o;
};

function pathEncoder (v) {
  return '/'+encodeURIComponent(v).replace(/~/g,'%7E');
}
/**
 * Accept an Array of property names and return a JSON Pointer URI fragment
 * @param Array a
 * @return {String}
 */
exports.encodePath = function encodePointer(a){
  // ~ must be encoded explicitly because hacks
  // the slash is encoded by encodeURIComponent
  return a.map(pathEncoder).join('');
};


/**
 * Calculate the number of decimal places a number uses
 * We need this to get correct results out of multipleOf and divisibleBy
 * when either figure is has decimal places, due to IEEE-754 float issues.
 * @param number
 * @returns {number}
 */
exports.getDecimalPlaces = function getDecimalPlaces(number) {

  var decimalPlaces = 0;
  if (isNaN(number)) return decimalPlaces;

  if (typeof number !== 'number') {
    number = Number(number);
  }

  var parts = number.toString().split('e');
  if (parts.length === 2) {
    if (parts[1][0] !== '-') {
      return decimalPlaces;
    } else {
      decimalPlaces = Number(parts[1].slice(1));
    }
  }

  var decimalParts = parts[0].split('.');
  if (decimalParts.length === 2) {
    decimalPlaces += decimalParts[1].length;
  }

  return decimalPlaces;
};

exports.isSchema = function isSchema(val){
  return (typeof val === 'object' && val) || (typeof val === 'boolean');
};

/**
 * Resolve target URL from a base and relative URL.
 * Similar to Node's URL Lib's legacy resolve function.
 * Code from example in deprecation note in said library.
 * @param string
 * @param string
 * @returns {string}
 */
var resolveUrl = exports.resolveUrl = function resolveUrl(from, to) {
  const resolvedUrl = new URL(to, new URL(from, 'resolve://'));
  if (resolvedUrl.protocol === 'resolve:') {
    const { pathname, search, hash } = resolvedUrl;
    return pathname + search + hash;
  }
  return resolvedUrl.toString();
}


/***/ }),

/***/ "./node_modules/jsonschema/lib/index.js":
/*!**********************************************!*\
  !*** ./node_modules/jsonschema/lib/index.js ***!
  \**********************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {



var Validator = module.exports.Validator = __webpack_require__(/*! ./validator */ "./node_modules/jsonschema/lib/validator.js");

module.exports.ValidatorResult = __webpack_require__(/*! ./helpers */ "./node_modules/jsonschema/lib/helpers.js").ValidatorResult;
module.exports.ValidatorResultError = __webpack_require__(/*! ./helpers */ "./node_modules/jsonschema/lib/helpers.js").ValidatorResultError;
module.exports.ValidationError = __webpack_require__(/*! ./helpers */ "./node_modules/jsonschema/lib/helpers.js").ValidationError;
module.exports.SchemaError = __webpack_require__(/*! ./helpers */ "./node_modules/jsonschema/lib/helpers.js").SchemaError;
module.exports.SchemaScanResult = __webpack_require__(/*! ./scan */ "./node_modules/jsonschema/lib/scan.js").SchemaScanResult;
module.exports.scan = __webpack_require__(/*! ./scan */ "./node_modules/jsonschema/lib/scan.js").scan;

module.exports.validate = function (instance, schema, options) {
  var v = new Validator();
  return v.validate(instance, schema, options);
};


/***/ }),

/***/ "./node_modules/jsonschema/lib/scan.js":
/*!*********************************************!*\
  !*** ./node_modules/jsonschema/lib/scan.js ***!
  \*********************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {



var helpers = __webpack_require__(/*! ./helpers */ "./node_modules/jsonschema/lib/helpers.js");

module.exports.SchemaScanResult = SchemaScanResult;
function SchemaScanResult(found, ref){
  this.id = found;
  this.ref = ref;
}

/**
 * Adds a schema with a certain urn to the Validator instance.
 * @param string uri
 * @param object schema
 * @return {Object}
 */
module.exports.scan = function scan(base, schema){
  function scanSchema(baseuri, schema){
    if(!schema || typeof schema!='object') return;
    // Mark all referenced schemas so we can tell later which schemas are referred to, but never defined
    if(schema.$ref){
      let resolvedUri = helpers.resolveUrl(baseuri,schema.$ref);
      ref[resolvedUri] = ref[resolvedUri] ? ref[resolvedUri]+1 : 0;
      return;
    }
    var id = schema.$id || schema.id;
    let resolvedBase = helpers.resolveUrl(baseuri,id);
    var ourBase = id ? resolvedBase : baseuri;
    if (ourBase) {
      // If there's no fragment, append an empty one
      if(ourBase.indexOf('#')<0) ourBase += '#';
      if(found[ourBase]){
        if(!helpers.deepCompareStrict(found[ourBase], schema)){
          throw new Error('Schema <'+ourBase+'> already exists with different definition');
        }
        return found[ourBase];
      }
      found[ourBase] = schema;
      // strip trailing fragment
      if(ourBase[ourBase.length-1]=='#'){
        found[ourBase.substring(0, ourBase.length-1)] = schema;
      }
    }
    scanArray(ourBase+'/items', (Array.isArray(schema.items)?schema.items:[schema.items]));
    scanArray(ourBase+'/extends', (Array.isArray(schema.extends)?schema.extends:[schema.extends]));
    scanSchema(ourBase+'/additionalItems', schema.additionalItems);
    scanObject(ourBase+'/properties', schema.properties);
    scanSchema(ourBase+'/additionalProperties', schema.additionalProperties);
    scanObject(ourBase+'/definitions', schema.definitions);
    scanObject(ourBase+'/patternProperties', schema.patternProperties);
    scanObject(ourBase+'/dependencies', schema.dependencies);
    scanArray(ourBase+'/disallow', schema.disallow);
    scanArray(ourBase+'/allOf', schema.allOf);
    scanArray(ourBase+'/anyOf', schema.anyOf);
    scanArray(ourBase+'/oneOf', schema.oneOf);
    scanSchema(ourBase+'/not', schema.not);
  }
  function scanArray(baseuri, schemas){
    if(!Array.isArray(schemas)) return;
    for(var i=0; i<schemas.length; i++){
      scanSchema(baseuri+'/'+i, schemas[i]);
    }
  }
  function scanObject(baseuri, schemas){
    if(!schemas || typeof schemas!='object') return;
    for(var p in schemas){
      scanSchema(baseuri+'/'+p, schemas[p]);
    }
  }

  var found = {};
  var ref = {};
  scanSchema(base, schema);
  return new SchemaScanResult(found, ref);
};


/***/ }),

/***/ "./node_modules/jsonschema/lib/validator.js":
/*!**************************************************!*\
  !*** ./node_modules/jsonschema/lib/validator.js ***!
  \**************************************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {



var attribute = __webpack_require__(/*! ./attribute */ "./node_modules/jsonschema/lib/attribute.js");
var helpers = __webpack_require__(/*! ./helpers */ "./node_modules/jsonschema/lib/helpers.js");
var scanSchema = (__webpack_require__(/*! ./scan */ "./node_modules/jsonschema/lib/scan.js").scan);
var ValidatorResult = helpers.ValidatorResult;
var ValidatorResultError = helpers.ValidatorResultError;
var SchemaError = helpers.SchemaError;
var SchemaContext = helpers.SchemaContext;
//var anonymousBase = 'vnd.jsonschema:///';
var anonymousBase = '/';

/**
 * Creates a new Validator object
 * @name Validator
 * @constructor
 */
var Validator = function Validator () {
  // Allow a validator instance to override global custom formats or to have their
  // own custom formats.
  this.customFormats = Object.create(Validator.prototype.customFormats);
  this.schemas = {};
  this.unresolvedRefs = [];

  // Use Object.create to make this extensible without Validator instances stepping on each other's toes.
  this.types = Object.create(types);
  this.attributes = Object.create(attribute.validators);
};

// Allow formats to be registered globally.
Validator.prototype.customFormats = {};

// Hint at the presence of a property
Validator.prototype.schemas = null;
Validator.prototype.types = null;
Validator.prototype.attributes = null;
Validator.prototype.unresolvedRefs = null;

/**
 * Adds a schema with a certain urn to the Validator instance.
 * @param schema
 * @param urn
 * @return {Object}
 */
Validator.prototype.addSchema = function addSchema (schema, base) {
  var self = this;
  if (!schema) {
    return null;
  }
  var scan = scanSchema(base||anonymousBase, schema);
  var ourUri = base || schema.$id || schema.id;
  for(var uri in scan.id){
    this.schemas[uri] = scan.id[uri];
  }
  for(var uri in scan.ref){
    // If this schema is already defined, it will be filtered out by the next step
    this.unresolvedRefs.push(uri);
  }
  // Remove newly defined schemas from unresolvedRefs
  this.unresolvedRefs = this.unresolvedRefs.filter(function(uri){
    return typeof self.schemas[uri]==='undefined';
  });
  return this.schemas[ourUri];
};

Validator.prototype.addSubSchemaArray = function addSubSchemaArray(baseuri, schemas) {
  if(!Array.isArray(schemas)) return;
  for(var i=0; i<schemas.length; i++){
    this.addSubSchema(baseuri, schemas[i]);
  }
};

Validator.prototype.addSubSchemaObject = function addSubSchemaArray(baseuri, schemas) {
  if(!schemas || typeof schemas!='object') return;
  for(var p in schemas){
    this.addSubSchema(baseuri, schemas[p]);
  }
};



/**
 * Sets all the schemas of the Validator instance.
 * @param schemas
 */
Validator.prototype.setSchemas = function setSchemas (schemas) {
  this.schemas = schemas;
};

/**
 * Returns the schema of a certain urn
 * @param urn
 */
Validator.prototype.getSchema = function getSchema (urn) {
  return this.schemas[urn];
};

/**
 * Validates instance against the provided schema
 * @param instance
 * @param schema
 * @param [options]
 * @param [ctx]
 * @return {Array}
 */
Validator.prototype.validate = function validate (instance, schema, options, ctx) {
  if((typeof schema !== 'boolean' && typeof schema !== 'object') || schema === null){
    throw new SchemaError('Expected `schema` to be an object or boolean');
  }
  if (!options) {
    options = {};
  }
  // This section indexes subschemas in the provided schema, so they don't need to be added with Validator#addSchema
  // This will work so long as the function at uri.resolve() will resolve a relative URI to a relative URI
  var id = schema.$id || schema.id;
  let base = helpers.resolveUrl(options.base,id||'');
  if(!ctx){
    ctx = new SchemaContext(schema, options, [], base, Object.create(this.schemas));
    if (!ctx.schemas[base]) {
      ctx.schemas[base] = schema;
    }
    var found = scanSchema(base, schema);
    for(var n in found.id){
      var sch = found.id[n];
      ctx.schemas[n] = sch;
    }
  }
  if(options.required && instance===undefined){
    var result = new ValidatorResult(instance, schema, options, ctx);
    result.addError('is required, but is undefined');
    return result;
  }
  var result = this.validateSchema(instance, schema, options, ctx);
  if (!result) {
    throw new Error('Result undefined');
  }else if(options.throwAll && result.errors.length){
    throw new ValidatorResultError(result);
  }
  return result;
};

/**
* @param Object schema
* @return mixed schema uri or false
*/
function shouldResolve(schema) {
  var ref = (typeof schema === 'string') ? schema : schema.$ref;
  if (typeof ref=='string') return ref;
  return false;
}

/**
 * Validates an instance against the schema (the actual work horse)
 * @param instance
 * @param schema
 * @param options
 * @param ctx
 * @private
 * @return {ValidatorResult}
 */
Validator.prototype.validateSchema = function validateSchema (instance, schema, options, ctx) {
  var result = new ValidatorResult(instance, schema, options, ctx);

  // Support for the true/false schemas
  if(typeof schema==='boolean') {
    if(schema===true){
      // `true` is always valid
      schema = {};
    }else if(schema===false){
      // `false` is always invalid
      schema = {type: []};
    }
  }else if(!schema){
    // This might be a string
    throw new Error("schema is undefined");
  }

  if (schema['extends']) {
    if (Array.isArray(schema['extends'])) {
      var schemaobj = {schema: schema, ctx: ctx};
      schema['extends'].forEach(this.schemaTraverser.bind(this, schemaobj));
      schema = schemaobj.schema;
      schemaobj.schema = null;
      schemaobj.ctx = null;
      schemaobj = null;
    } else {
      schema = helpers.deepMerge(schema, this.superResolve(schema['extends'], ctx));
    }
  }

  // If passed a string argument, load that schema URI
  var switchSchema = shouldResolve(schema);
  if (switchSchema) {
    var resolved = this.resolve(schema, switchSchema, ctx);
    var subctx = new SchemaContext(resolved.subschema, options, ctx.path, resolved.switchSchema, ctx.schemas);
    return this.validateSchema(instance, resolved.subschema, options, subctx);
  }

  var skipAttributes = options && options.skipAttributes || [];
  // Validate each schema attribute against the instance
  for (var key in schema) {
    if (!attribute.ignoreProperties[key] && skipAttributes.indexOf(key) < 0) {
      var validatorErr = null;
      var validator = this.attributes[key];
      if (validator) {
        validatorErr = validator.call(this, instance, schema, options, ctx);
      } else if (options.allowUnknownAttributes === false) {
        // This represents an error with the schema itself, not an invalid instance
        throw new SchemaError("Unsupported attribute: " + key, schema);
      }
      if (validatorErr) {
        result.importErrors(validatorErr);
      }
    }
  }

  if (typeof options.rewrite == 'function') {
    var value = options.rewrite.call(this, instance, schema, options, ctx);
    result.instance = value;
  }
  return result;
};

/**
* @private
* @param Object schema
* @param SchemaContext ctx
* @returns Object schema or resolved schema
*/
Validator.prototype.schemaTraverser = function schemaTraverser (schemaobj, s) {
  schemaobj.schema = helpers.deepMerge(schemaobj.schema, this.superResolve(s, schemaobj.ctx));
};

/**
* @private
* @param Object schema
* @param SchemaContext ctx
* @returns Object schema or resolved schema
*/
Validator.prototype.superResolve = function superResolve (schema, ctx) {
  var ref = shouldResolve(schema);
  if(ref) {
    return this.resolve(schema, ref, ctx).subschema;
  }
  return schema;
};

/**
* @private
* @param Object schema
* @param Object switchSchema
* @param SchemaContext ctx
* @return Object resolved schemas {subschema:String, switchSchema: String}
* @throws SchemaError
*/
Validator.prototype.resolve = function resolve (schema, switchSchema, ctx) {
  switchSchema = ctx.resolve(switchSchema);
  // First see if the schema exists under the provided URI
  if (ctx.schemas[switchSchema]) {
    return {subschema: ctx.schemas[switchSchema], switchSchema: switchSchema};
  }
  // Else try walking the property pointer
  let parsed = new URL(switchSchema,'thismessage::/');
  let fragment = parsed.hash;
  var document = fragment && fragment.length && switchSchema.substr(0, switchSchema.length - fragment.length);
  if (!document || !ctx.schemas[document]) {
    throw new SchemaError("no such schema <" + switchSchema + ">", schema);
  }
  var subschema = helpers.objectGetPath(ctx.schemas[document], fragment.substr(1));
  if(subschema===undefined){
    throw new SchemaError("no such schema " + fragment + " located in <" + document + ">", schema);
  }
  return {subschema: subschema, switchSchema: switchSchema};
};

/**
 * Tests whether the instance if of a certain type.
 * @private
 * @param instance
 * @param schema
 * @param options
 * @param ctx
 * @param type
 * @return {boolean}
 */
Validator.prototype.testType = function validateType (instance, schema, options, ctx, type) {
  if(type===undefined){
    return;
  }else if(type===null){
    throw new SchemaError('Unexpected null in "type" keyword');
  }
  if (typeof this.types[type] == 'function') {
    return this.types[type].call(this, instance);
  }
  if (type && typeof type == 'object') {
    var res = this.validateSchema(instance, type, options, ctx);
    return res === undefined || !(res && res.errors.length);
  }
  // Undefined or properties not on the list are acceptable, same as not being defined
  return true;
};

var types = Validator.prototype.types = {};
types.string = function testString (instance) {
  return typeof instance == 'string';
};
types.number = function testNumber (instance) {
  // isFinite returns false for NaN, Infinity, and -Infinity
  return typeof instance == 'number' && isFinite(instance);
};
types.integer = function testInteger (instance) {
  return (typeof instance == 'number') && instance % 1 === 0;
};
types.boolean = function testBoolean (instance) {
  return typeof instance == 'boolean';
};
types.array = function testArray (instance) {
  return Array.isArray(instance);
};
types['null'] = function testNull (instance) {
  return instance === null;
};
types.date = function testDate (instance) {
  return instance instanceof Date;
};
types.any = function testAny (instance) {
  return true;
};
types.object = function testObject (instance) {
  // TODO: fix this - see #15
  return instance && (typeof instance === 'object') && !(Array.isArray(instance)) && !(instance instanceof Date);
};

module.exports = Validator;


/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-color-overlay.ts":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-color-overlay.ts ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   backgroundColorOverlayPropTypeUtil: function() { return /* binding */ backgroundColorOverlayPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");


const backgroundColorOverlayPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_0__.createPropUtils)('background-color-overlay', _utils__WEBPACK_IMPORTED_MODULE_1__.unknownChildrenSchema);

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-gradient-overlay.ts":
/*!*****************************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-gradient-overlay.ts ***!
  \*****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   backgroundGradientOverlayPropTypeUtil: function() { return /* binding */ backgroundGradientOverlayPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");


const backgroundGradientOverlayPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_0__.createPropUtils)('background-gradient-overlay', _utils__WEBPACK_IMPORTED_MODULE_1__.unknownChildrenSchema);

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-image-overlay.ts":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-image-overlay.ts ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   backgroundImageOverlayPropTypeUtil: function() { return /* binding */ backgroundImageOverlayPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");


const backgroundImageOverlayPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_0__.createPropUtils)('background-image-overlay', _utils__WEBPACK_IMPORTED_MODULE_1__.unknownChildrenSchema);

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-image-position-offset.ts":
/*!**********************************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-image-position-offset.ts ***!
  \**********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   backgroundImagePositionOffsetPropTypeUtil: function() { return /* binding */ backgroundImagePositionOffsetPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");


const backgroundImagePositionOffsetPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_0__.createPropUtils)('background-image-position-offset', _utils__WEBPACK_IMPORTED_MODULE_1__.unknownChildrenSchema);

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-image-size-scale.ts":
/*!*****************************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-image-size-scale.ts ***!
  \*****************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   backgroundImageSizeScalePropTypeUtil: function() { return /* binding */ backgroundImageSizeScalePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");


const backgroundImageSizeScalePropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_0__.createPropUtils)('background-image-size-scale', _utils__WEBPACK_IMPORTED_MODULE_1__.unknownChildrenSchema);

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-overlay.ts":
/*!********************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-overlay.ts ***!
  \********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   backgroundOverlayItem: function() { return /* binding */ backgroundOverlayItem; },
/* harmony export */   backgroundOverlayPropTypeUtil: function() { return /* binding */ backgroundOverlayPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _background_color_overlay__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./background-color-overlay */ "./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-color-overlay.ts");
/* harmony import */ var _background_gradient_overlay__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./background-gradient-overlay */ "./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-gradient-overlay.ts");
/* harmony import */ var _background_image_overlay__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./background-image-overlay */ "./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-image-overlay.ts");





const backgroundOverlayItem = _background_color_overlay__WEBPACK_IMPORTED_MODULE_2__.backgroundColorOverlayPropTypeUtil.schema.or(_background_gradient_overlay__WEBPACK_IMPORTED_MODULE_3__.backgroundGradientOverlayPropTypeUtil.schema).or(_background_image_overlay__WEBPACK_IMPORTED_MODULE_4__.backgroundImageOverlayPropTypeUtil.schema);
const backgroundOverlayPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('background-overlay', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.array(backgroundOverlayItem));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background.ts":
/*!************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background.ts ***!
  \************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   backgroundPropTypeUtil: function() { return /* binding */ backgroundPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const backgroundPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('background', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  color: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  clip: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'background-overlay': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/boolean.ts":
/*!***********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/boolean.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   booleanPropTypeUtil: function() { return /* binding */ booleanPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");


const booleanPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('boolean', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.boolean().nullable());

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/border-radius.ts":
/*!*****************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/border-radius.ts ***!
  \*****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   borderRadiusPropTypeUtil: function() { return /* binding */ borderRadiusPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const borderRadiusPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('border-radius-v2', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  'start-start': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'start-end': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'end-start': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'end-end': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/border-width.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/border-width.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   borderWidthPropTypeUtil: function() { return /* binding */ borderWidthPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const borderWidthPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('border-width-v2', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  'block-start': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'block-end': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'inline-start': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'inline-end': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/box-shadow.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/box-shadow.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   boxShadowPropTypeUtil: function() { return /* binding */ boxShadowPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _shadow__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./shadow */ "./packages/packages/libs/editor-props/src/prop-types/shadow.ts");



const boxShadowPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('box-shadow', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.array(_shadow__WEBPACK_IMPORTED_MODULE_2__.shadowPropTypeUtil.schema));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/classes.ts":
/*!***********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/classes.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CLASSES_PROP_KEY: function() { return /* binding */ CLASSES_PROP_KEY; },
/* harmony export */   classesPropTypeUtil: function() { return /* binding */ classesPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");


const CLASSES_PROP_KEY = 'classes';
const classesPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)(CLASSES_PROP_KEY, _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.array(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().regex(/^[a-z][a-z-_0-9]*$/i)));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/color-stop.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/color-stop.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   colorStopPropTypeUtil: function() { return /* binding */ colorStopPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const colorStopPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('color-stop', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  color: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  offset: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/color.ts":
/*!*********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/color.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   colorPropTypeUtil: function() { return /* binding */ colorPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");


const colorPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('color', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string());

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/date-range.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/date-range.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   dateRangePropTypeUtil: function() { return /* binding */ dateRangePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const dateRangePropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('date-range', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  min: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  max: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/date-string.ts":
/*!***************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/date-string.ts ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   dateStringPropTypeUtil: function() { return /* binding */ dateStringPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");


const dateStringPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('date-string', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string());

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/date-time.ts":
/*!*************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/date-time.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   DateTimePropTypeUtil: function() { return /* binding */ DateTimePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const DateTimePropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('date-time', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  date: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  time: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/dimensions.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/dimensions.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   dimensionsPropTypeUtil: function() { return /* binding */ dimensionsPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const dimensionsPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('dimensions', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  'block-start': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'block-end': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'inline-start': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'inline-end': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/email.ts":
/*!*********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/email.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   emailPropTypeUtil: function() { return /* binding */ emailPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const emailPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('email', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  to: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  subject: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  message: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  from: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'meta-data': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'send-as': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'from-name': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'reply-to': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  cc: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  bcc: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/emails.ts":
/*!**********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/emails.ts ***!
  \**********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   emailsPropTypeUtil: function() { return /* binding */ emailsPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const emailsPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('emails', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  to: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  subject: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  message: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  from: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'meta-data': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'send-as': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'from-name': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'reply-to': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  cc: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  bcc: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/backdrop-filter.ts":
/*!*************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/backdrop-filter.ts ***!
  \*************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   backdropFilterPropTypeUtil: function() { return /* binding */ backdropFilterPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _filter__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./filter */ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter.ts");



const backdropFilterPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('backdrop-filter', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.array(_filter__WEBPACK_IMPORTED_MODULE_2__.cssFilterFunctionPropUtil.schema));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/drop-shadow-filter.ts":
/*!****************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/drop-shadow-filter.ts ***!
  \****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   dropShadowFilterPropTypeUtil: function() { return /* binding */ dropShadowFilterPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const dropShadowFilterPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('drop-shadow', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.object({
  xAxis: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  yAxis: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  blur: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  color: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter-functions/blur-filter.ts":
/*!**************************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter-functions/blur-filter.ts ***!
  \**************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   blurFilterPropTypeUtil: function() { return /* binding */ blurFilterPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const blurFilterPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('blur', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  size: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter-functions/color-tone-filter.ts":
/*!********************************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter-functions/color-tone-filter.ts ***!
  \********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   colorToneFilterPropTypeUtil: function() { return /* binding */ colorToneFilterPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const colorToneFilterPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('color-tone', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  size: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter-functions/hue-rotate-filter.ts":
/*!********************************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter-functions/hue-rotate-filter.ts ***!
  \********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   hueRotateFilterPropTypeUtil: function() { return /* binding */ hueRotateFilterPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const hueRotateFilterPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('hue-rotate', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  size: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter-functions/intensity-filter.ts":
/*!*******************************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter-functions/intensity-filter.ts ***!
  \*******************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   intensityFilterPropTypeUtil: function() { return /* binding */ intensityFilterPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const intensityFilterPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('intensity', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  size: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter.ts":
/*!****************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter.ts ***!
  \****************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   cssFilterFunctionPropUtil: function() { return /* binding */ cssFilterFunctionPropUtil; },
/* harmony export */   filterPropTypeUtil: function() { return /* binding */ filterPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _string__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../string */ "./packages/packages/libs/editor-props/src/prop-types/string.ts");
/* harmony import */ var _drop_shadow_filter__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./drop-shadow-filter */ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/drop-shadow-filter.ts");
/* harmony import */ var _filter_functions_blur_filter__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./filter-functions/blur-filter */ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter-functions/blur-filter.ts");
/* harmony import */ var _filter_functions_color_tone_filter__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./filter-functions/color-tone-filter */ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter-functions/color-tone-filter.ts");
/* harmony import */ var _filter_functions_hue_rotate_filter__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./filter-functions/hue-rotate-filter */ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter-functions/hue-rotate-filter.ts");
/* harmony import */ var _filter_functions_intensity_filter__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./filter-functions/intensity-filter */ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter-functions/intensity-filter.ts");








const cssFilterFunctionPropUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('css-filter-func', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.object({
  func: _string__WEBPACK_IMPORTED_MODULE_2__.stringPropTypeUtil.schema,
  args: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.union([_filter_functions_blur_filter__WEBPACK_IMPORTED_MODULE_4__.blurFilterPropTypeUtil.schema, _filter_functions_intensity_filter__WEBPACK_IMPORTED_MODULE_7__.intensityFilterPropTypeUtil.schema, _filter_functions_color_tone_filter__WEBPACK_IMPORTED_MODULE_5__.colorToneFilterPropTypeUtil.schema, _filter_functions_hue_rotate_filter__WEBPACK_IMPORTED_MODULE_6__.hueRotateFilterPropTypeUtil.schema, _drop_shadow_filter__WEBPACK_IMPORTED_MODULE_3__.dropShadowFilterPropTypeUtil.schema])
}));
const filterPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('filter', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.array(cssFilterFunctionPropUtil.schema));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/flex.ts":
/*!********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/flex.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   flexPropTypeUtil: function() { return /* binding */ flexPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const flexPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('flex', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  flexGrow: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  flexShrink: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  flexBasis: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/font-family.ts":
/*!***************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/font-family.ts ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   fontFamilyPropTypeUtil: function() { return /* binding */ fontFamilyPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");


const baseUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('font-family', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().nullable());
const fontFamilyPropTypeUtil = Object.assign(baseUtil, {
  getEnqueueFontFamily: value => {
    const trimmed = value.trim();
    if (trimmed.startsWith('"') && trimmed.endsWith('"') || trimmed.startsWith("'") && trimmed.endsWith("'")) {
      return trimmed.slice(1, -1).trim();
    }
    return trimmed;
  }
});

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/gradient-color-stop.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/gradient-color-stop.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   gradientColorStopPropTypeUtil: function() { return /* binding */ gradientColorStopPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _color_stop__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./color-stop */ "./packages/packages/libs/editor-props/src/prop-types/color-stop.ts");



const gradientColorStopPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('gradient-color-stop', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.array(_color_stop__WEBPACK_IMPORTED_MODULE_2__.colorStopPropTypeUtil.schema));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/grid-track-size.ts":
/*!*******************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/grid-track-size.ts ***!
  \*******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   gridTrackSizePropTypeUtil: function() { return /* binding */ gridTrackSizePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");


const gridTrackSizePropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('grid-track-size', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  unit: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.literal('fr'),
  size: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.union([_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number(), _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.literal('')])
}).or(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  unit: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.literal('custom'),
  size: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string()
})));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/html-v2.ts":
/*!***********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/html-v2.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   htmlV2PropTypeUtil: function() { return /* binding */ htmlV2PropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");


const childElementSchema = _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.lazy(() => _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.object({
  id: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string(),
  type: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string(),
  content: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().optional(),
  children: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.array(childElementSchema).optional()
}));
const htmlV2ValueSchema = _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.object({
  content: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().nullable(),
  children: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.array(childElementSchema)
});
const htmlV2PropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('html-v2', htmlV2ValueSchema);

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/html-v3.ts":
/*!***********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/html-v3.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   htmlV3PropTypeUtil: function() { return /* binding */ htmlV3PropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _string__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./string */ "./packages/packages/libs/editor-props/src/prop-types/string.ts");



const htmlV3ValueSchema = _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.object({
  content: _string__WEBPACK_IMPORTED_MODULE_2__.stringPropTypeUtil.schema.nullable(),
  children: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.array(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.unknown())
});
const htmlV3PropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('html-v3', htmlV3ValueSchema);

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/html.ts":
/*!********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/html.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   htmlPropTypeUtil: function() { return /* binding */ htmlPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");


const htmlPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('html', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().nullable());

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/image-attachment-id.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/image-attachment-id.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   imageAttachmentIdPropType: function() { return /* binding */ imageAttachmentIdPropType; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");


const imageAttachmentIdPropType = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('image-attachment-id', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number());

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/image-src.ts":
/*!*************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/image-src.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   imageSrcPropTypeUtil: function() { return /* binding */ imageSrcPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const imageSrcPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('image-src', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  id: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  url: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.null(),
  alt: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema.optional().default(null)
}).or(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  id: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema.optional().default(null),
  url: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  alt: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema.optional().default(null)
})));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/image.ts":
/*!*********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/image.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   imagePropTypeUtil: function() { return /* binding */ imagePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const imagePropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('image', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  src: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  size: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/index.ts":
/*!*********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/index.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CLASSES_PROP_KEY: function() { return /* reexport safe */ _classes__WEBPACK_IMPORTED_MODULE_3__.CLASSES_PROP_KEY; },
/* harmony export */   DateTimePropTypeUtil: function() { return /* reexport safe */ _date_time__WEBPACK_IMPORTED_MODULE_40__.DateTimePropTypeUtil; },
/* harmony export */   backdropFilterPropTypeUtil: function() { return /* reexport safe */ _filter_prop_types_backdrop_filter__WEBPACK_IMPORTED_MODULE_56__.backdropFilterPropTypeUtil; },
/* harmony export */   backgroundColorOverlayPropTypeUtil: function() { return /* reexport safe */ _background_prop_types_background_color_overlay__WEBPACK_IMPORTED_MODULE_30__.backgroundColorOverlayPropTypeUtil; },
/* harmony export */   backgroundGradientOverlayPropTypeUtil: function() { return /* reexport safe */ _background_prop_types_background_gradient_overlay__WEBPACK_IMPORTED_MODULE_32__.backgroundGradientOverlayPropTypeUtil; },
/* harmony export */   backgroundImageOverlayPropTypeUtil: function() { return /* reexport safe */ _background_prop_types_background_image_overlay__WEBPACK_IMPORTED_MODULE_31__.backgroundImageOverlayPropTypeUtil; },
/* harmony export */   backgroundImagePositionOffsetPropTypeUtil: function() { return /* reexport safe */ _background_prop_types_background_image_position_offset__WEBPACK_IMPORTED_MODULE_33__.backgroundImagePositionOffsetPropTypeUtil; },
/* harmony export */   backgroundImageSizeScalePropTypeUtil: function() { return /* reexport safe */ _background_prop_types_background_image_size_scale__WEBPACK_IMPORTED_MODULE_34__.backgroundImageSizeScalePropTypeUtil; },
/* harmony export */   backgroundOverlayItem: function() { return /* reexport safe */ _background_prop_types_background_overlay__WEBPACK_IMPORTED_MODULE_29__.backgroundOverlayItem; },
/* harmony export */   backgroundOverlayPropTypeUtil: function() { return /* reexport safe */ _background_prop_types_background_overlay__WEBPACK_IMPORTED_MODULE_29__.backgroundOverlayPropTypeUtil; },
/* harmony export */   backgroundPropTypeUtil: function() { return /* reexport safe */ _background_prop_types_background__WEBPACK_IMPORTED_MODULE_28__.backgroundPropTypeUtil; },
/* harmony export */   blurFilterPropTypeUtil: function() { return /* reexport safe */ _filter_prop_types_filter_functions_blur_filter__WEBPACK_IMPORTED_MODULE_58__.blurFilterPropTypeUtil; },
/* harmony export */   booleanPropTypeUtil: function() { return /* reexport safe */ _boolean__WEBPACK_IMPORTED_MODULE_35__.booleanPropTypeUtil; },
/* harmony export */   borderRadiusPropTypeUtil: function() { return /* reexport safe */ _border_radius__WEBPACK_IMPORTED_MODULE_1__.borderRadiusPropTypeUtil; },
/* harmony export */   borderWidthPropTypeUtil: function() { return /* reexport safe */ _border_width__WEBPACK_IMPORTED_MODULE_2__.borderWidthPropTypeUtil; },
/* harmony export */   boxShadowPropTypeUtil: function() { return /* reexport safe */ _box_shadow__WEBPACK_IMPORTED_MODULE_0__.boxShadowPropTypeUtil; },
/* harmony export */   classesPropTypeUtil: function() { return /* reexport safe */ _classes__WEBPACK_IMPORTED_MODULE_3__.classesPropTypeUtil; },
/* harmony export */   colorPropTypeUtil: function() { return /* reexport safe */ _color__WEBPACK_IMPORTED_MODULE_4__.colorPropTypeUtil; },
/* harmony export */   colorStopPropTypeUtil: function() { return /* reexport safe */ _color_stop__WEBPACK_IMPORTED_MODULE_36__.colorStopPropTypeUtil; },
/* harmony export */   colorToneFilterPropTypeUtil: function() { return /* reexport safe */ _filter_prop_types_filter_functions_color_tone_filter__WEBPACK_IMPORTED_MODULE_60__.colorToneFilterPropTypeUtil; },
/* harmony export */   cssFilterFunctionPropUtil: function() { return /* reexport safe */ _filter_prop_types_filter__WEBPACK_IMPORTED_MODULE_47__.cssFilterFunctionPropUtil; },
/* harmony export */   dateRangePropTypeUtil: function() { return /* reexport safe */ _date_range__WEBPACK_IMPORTED_MODULE_62__.dateRangePropTypeUtil; },
/* harmony export */   dateStringPropTypeUtil: function() { return /* reexport safe */ _date_string__WEBPACK_IMPORTED_MODULE_63__.dateStringPropTypeUtil; },
/* harmony export */   dimensionsPropTypeUtil: function() { return /* reexport safe */ _dimensions__WEBPACK_IMPORTED_MODULE_13__.dimensionsPropTypeUtil; },
/* harmony export */   dropShadowFilterPropTypeUtil: function() { return /* reexport safe */ _filter_prop_types_drop_shadow_filter__WEBPACK_IMPORTED_MODULE_57__.dropShadowFilterPropTypeUtil; },
/* harmony export */   emailPropTypeUtil: function() { return /* reexport safe */ _email__WEBPACK_IMPORTED_MODULE_25__.emailPropTypeUtil; },
/* harmony export */   emailsPropTypeUtil: function() { return /* reexport safe */ _emails__WEBPACK_IMPORTED_MODULE_26__.emailsPropTypeUtil; },
/* harmony export */   filterPropTypeUtil: function() { return /* reexport safe */ _filter_prop_types_filter__WEBPACK_IMPORTED_MODULE_47__.filterPropTypeUtil; },
/* harmony export */   flexPropTypeUtil: function() { return /* reexport safe */ _flex__WEBPACK_IMPORTED_MODULE_5__.flexPropTypeUtil; },
/* harmony export */   fontFamilyPropTypeUtil: function() { return /* reexport safe */ _font_family__WEBPACK_IMPORTED_MODULE_6__.fontFamilyPropTypeUtil; },
/* harmony export */   gradientColorStopPropTypeUtil: function() { return /* reexport safe */ _gradient_color_stop__WEBPACK_IMPORTED_MODULE_37__.gradientColorStopPropTypeUtil; },
/* harmony export */   gridTrackSizePropTypeUtil: function() { return /* reexport safe */ _grid_track_size__WEBPACK_IMPORTED_MODULE_17__.gridTrackSizePropTypeUtil; },
/* harmony export */   htmlPropTypeUtil: function() { return /* reexport safe */ _html__WEBPACK_IMPORTED_MODULE_44__.htmlPropTypeUtil; },
/* harmony export */   htmlV2PropTypeUtil: function() { return /* reexport safe */ _html_v2__WEBPACK_IMPORTED_MODULE_45__.htmlV2PropTypeUtil; },
/* harmony export */   htmlV3PropTypeUtil: function() { return /* reexport safe */ _html_v3__WEBPACK_IMPORTED_MODULE_46__.htmlV3PropTypeUtil; },
/* harmony export */   hueRotateFilterPropTypeUtil: function() { return /* reexport safe */ _filter_prop_types_filter_functions_hue_rotate_filter__WEBPACK_IMPORTED_MODULE_61__.hueRotateFilterPropTypeUtil; },
/* harmony export */   imageAttachmentIdPropType: function() { return /* reexport safe */ _image_attachment_id__WEBPACK_IMPORTED_MODULE_8__.imageAttachmentIdPropType; },
/* harmony export */   imagePropTypeUtil: function() { return /* reexport safe */ _image__WEBPACK_IMPORTED_MODULE_7__.imagePropTypeUtil; },
/* harmony export */   imageSrcPropTypeUtil: function() { return /* reexport safe */ _image_src__WEBPACK_IMPORTED_MODULE_9__.imageSrcPropTypeUtil; },
/* harmony export */   intensityFilterPropTypeUtil: function() { return /* reexport safe */ _filter_prop_types_filter_functions_intensity_filter__WEBPACK_IMPORTED_MODULE_59__.intensityFilterPropTypeUtil; },
/* harmony export */   keyValuePropTypeUtil: function() { return /* reexport safe */ _key_value__WEBPACK_IMPORTED_MODULE_38__.keyValuePropTypeUtil; },
/* harmony export */   layoutDirectionPropTypeUtil: function() { return /* reexport safe */ _layout_direction__WEBPACK_IMPORTED_MODULE_23__.layoutDirectionPropTypeUtil; },
/* harmony export */   linkPropTypeUtil: function() { return /* reexport safe */ _link__WEBPACK_IMPORTED_MODULE_24__.linkPropTypeUtil; },
/* harmony export */   moveTransformPropTypeUtil: function() { return /* reexport safe */ _transform_prop_types_transform_functions_move_transform__WEBPACK_IMPORTED_MODULE_50__.moveTransformPropTypeUtil; },
/* harmony export */   numberPropTypeUtil: function() { return /* reexport safe */ _number__WEBPACK_IMPORTED_MODULE_14__.numberPropTypeUtil; },
/* harmony export */   numberRangePropTypeUtil: function() { return /* reexport safe */ _number_range__WEBPACK_IMPORTED_MODULE_39__.numberRangePropTypeUtil; },
/* harmony export */   perspectiveOriginPropTypeUtil: function() { return /* reexport safe */ _transform_prop_types_perspective_origin__WEBPACK_IMPORTED_MODULE_55__.perspectiveOriginPropTypeUtil; },
/* harmony export */   positionPropTypeUtil: function() { return /* reexport safe */ _position__WEBPACK_IMPORTED_MODULE_41__.positionPropTypeUtil; },
/* harmony export */   queryFilterArrayPropTypeUtil: function() { return /* reexport safe */ _query_filter__WEBPACK_IMPORTED_MODULE_43__.queryFilterArrayPropTypeUtil; },
/* harmony export */   queryFilterPropTypeUtil: function() { return /* reexport safe */ _query_filter__WEBPACK_IMPORTED_MODULE_43__.queryFilterPropTypeUtil; },
/* harmony export */   queryPropTypeUtil: function() { return /* reexport safe */ _query__WEBPACK_IMPORTED_MODULE_42__.queryPropTypeUtil; },
/* harmony export */   rotateTransformPropTypeUtil: function() { return /* reexport safe */ _transform_prop_types_transform_functions_rotate_transform__WEBPACK_IMPORTED_MODULE_52__.rotateTransformPropTypeUtil; },
/* harmony export */   scaleTransformPropTypeUtil: function() { return /* reexport safe */ _transform_prop_types_transform_functions_scale_transform__WEBPACK_IMPORTED_MODULE_51__.scaleTransformPropTypeUtil; },
/* harmony export */   selectionSizePropTypeUtil: function() { return /* reexport safe */ _selection_size__WEBPACK_IMPORTED_MODULE_27__.selectionSizePropTypeUtil; },
/* harmony export */   shadowPropTypeUtil: function() { return /* reexport safe */ _shadow__WEBPACK_IMPORTED_MODULE_15__.shadowPropTypeUtil; },
/* harmony export */   sizePropTypeUtil: function() { return /* reexport safe */ _size__WEBPACK_IMPORTED_MODULE_16__.sizePropTypeUtil; },
/* harmony export */   skewTransformPropTypeUtil: function() { return /* reexport safe */ _transform_prop_types_transform_functions_skew_transform__WEBPACK_IMPORTED_MODULE_53__.skewTransformPropTypeUtil; },
/* harmony export */   spanPropTypeUtil: function() { return /* reexport safe */ _span__WEBPACK_IMPORTED_MODULE_18__.spanPropTypeUtil; },
/* harmony export */   stringArrayPropTypeUtil: function() { return /* reexport safe */ _string_array__WEBPACK_IMPORTED_MODULE_20__.stringArrayPropTypeUtil; },
/* harmony export */   stringPropTypeUtil: function() { return /* reexport safe */ _string__WEBPACK_IMPORTED_MODULE_19__.stringPropTypeUtil; },
/* harmony export */   strokePropTypeUtil: function() { return /* reexport safe */ _stroke__WEBPACK_IMPORTED_MODULE_21__.strokePropTypeUtil; },
/* harmony export */   svgSrcPropTypeUtil: function() { return /* reexport safe */ _svg_src__WEBPACK_IMPORTED_MODULE_10__.svgSrcPropTypeUtil; },
/* harmony export */   timeRangePropTypeUtil: function() { return /* reexport safe */ _time_range__WEBPACK_IMPORTED_MODULE_64__.timeRangePropTypeUtil; },
/* harmony export */   timeStringPropTypeUtil: function() { return /* reexport safe */ _time_string__WEBPACK_IMPORTED_MODULE_65__.timeStringPropTypeUtil; },
/* harmony export */   transformFunctionsPropTypeUtil: function() { return /* reexport safe */ _transform_prop_types_transform_functions__WEBPACK_IMPORTED_MODULE_49__.transformFunctionsPropTypeUtil; },
/* harmony export */   transformOriginPropTypeUtil: function() { return /* reexport safe */ _transform_prop_types_transform_origin__WEBPACK_IMPORTED_MODULE_54__.transformOriginPropTypeUtil; },
/* harmony export */   transformPropTypeUtil: function() { return /* reexport safe */ _transform_prop_types_transform__WEBPACK_IMPORTED_MODULE_48__.transformPropTypeUtil; },
/* harmony export */   urlPropTypeUtil: function() { return /* reexport safe */ _url__WEBPACK_IMPORTED_MODULE_22__.urlPropTypeUtil; },
/* harmony export */   videoAttachmentIdPropType: function() { return /* reexport safe */ _video_attachment_id__WEBPACK_IMPORTED_MODULE_11__.videoAttachmentIdPropType; },
/* harmony export */   videoSrcPropTypeUtil: function() { return /* reexport safe */ _video_src__WEBPACK_IMPORTED_MODULE_12__.videoSrcPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _box_shadow__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./box-shadow */ "./packages/packages/libs/editor-props/src/prop-types/box-shadow.ts");
/* harmony import */ var _border_radius__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./border-radius */ "./packages/packages/libs/editor-props/src/prop-types/border-radius.ts");
/* harmony import */ var _border_width__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./border-width */ "./packages/packages/libs/editor-props/src/prop-types/border-width.ts");
/* harmony import */ var _classes__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./classes */ "./packages/packages/libs/editor-props/src/prop-types/classes.ts");
/* harmony import */ var _color__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./color */ "./packages/packages/libs/editor-props/src/prop-types/color.ts");
/* harmony import */ var _flex__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./flex */ "./packages/packages/libs/editor-props/src/prop-types/flex.ts");
/* harmony import */ var _font_family__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./font-family */ "./packages/packages/libs/editor-props/src/prop-types/font-family.ts");
/* harmony import */ var _image__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./image */ "./packages/packages/libs/editor-props/src/prop-types/image.ts");
/* harmony import */ var _image_attachment_id__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./image-attachment-id */ "./packages/packages/libs/editor-props/src/prop-types/image-attachment-id.ts");
/* harmony import */ var _image_src__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./image-src */ "./packages/packages/libs/editor-props/src/prop-types/image-src.ts");
/* harmony import */ var _svg_src__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./svg-src */ "./packages/packages/libs/editor-props/src/prop-types/svg-src.ts");
/* harmony import */ var _video_attachment_id__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./video-attachment-id */ "./packages/packages/libs/editor-props/src/prop-types/video-attachment-id.ts");
/* harmony import */ var _video_src__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./video-src */ "./packages/packages/libs/editor-props/src/prop-types/video-src.ts");
/* harmony import */ var _dimensions__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(/*! ./dimensions */ "./packages/packages/libs/editor-props/src/prop-types/dimensions.ts");
/* harmony import */ var _number__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(/*! ./number */ "./packages/packages/libs/editor-props/src/prop-types/number.ts");
/* harmony import */ var _shadow__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(/*! ./shadow */ "./packages/packages/libs/editor-props/src/prop-types/shadow.ts");
/* harmony import */ var _size__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(/*! ./size */ "./packages/packages/libs/editor-props/src/prop-types/size.ts");
/* harmony import */ var _grid_track_size__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(/*! ./grid-track-size */ "./packages/packages/libs/editor-props/src/prop-types/grid-track-size.ts");
/* harmony import */ var _span__WEBPACK_IMPORTED_MODULE_18__ = __webpack_require__(/*! ./span */ "./packages/packages/libs/editor-props/src/prop-types/span.ts");
/* harmony import */ var _string__WEBPACK_IMPORTED_MODULE_19__ = __webpack_require__(/*! ./string */ "./packages/packages/libs/editor-props/src/prop-types/string.ts");
/* harmony import */ var _string_array__WEBPACK_IMPORTED_MODULE_20__ = __webpack_require__(/*! ./string-array */ "./packages/packages/libs/editor-props/src/prop-types/string-array.ts");
/* harmony import */ var _stroke__WEBPACK_IMPORTED_MODULE_21__ = __webpack_require__(/*! ./stroke */ "./packages/packages/libs/editor-props/src/prop-types/stroke.ts");
/* harmony import */ var _url__WEBPACK_IMPORTED_MODULE_22__ = __webpack_require__(/*! ./url */ "./packages/packages/libs/editor-props/src/prop-types/url.ts");
/* harmony import */ var _layout_direction__WEBPACK_IMPORTED_MODULE_23__ = __webpack_require__(/*! ./layout-direction */ "./packages/packages/libs/editor-props/src/prop-types/layout-direction.ts");
/* harmony import */ var _link__WEBPACK_IMPORTED_MODULE_24__ = __webpack_require__(/*! ./link */ "./packages/packages/libs/editor-props/src/prop-types/link.ts");
/* harmony import */ var _email__WEBPACK_IMPORTED_MODULE_25__ = __webpack_require__(/*! ./email */ "./packages/packages/libs/editor-props/src/prop-types/email.ts");
/* harmony import */ var _emails__WEBPACK_IMPORTED_MODULE_26__ = __webpack_require__(/*! ./emails */ "./packages/packages/libs/editor-props/src/prop-types/emails.ts");
/* harmony import */ var _selection_size__WEBPACK_IMPORTED_MODULE_27__ = __webpack_require__(/*! ./selection-size */ "./packages/packages/libs/editor-props/src/prop-types/selection-size.ts");
/* harmony import */ var _background_prop_types_background__WEBPACK_IMPORTED_MODULE_28__ = __webpack_require__(/*! ./background-prop-types/background */ "./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background.ts");
/* harmony import */ var _background_prop_types_background_overlay__WEBPACK_IMPORTED_MODULE_29__ = __webpack_require__(/*! ./background-prop-types/background-overlay */ "./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-overlay.ts");
/* harmony import */ var _background_prop_types_background_color_overlay__WEBPACK_IMPORTED_MODULE_30__ = __webpack_require__(/*! ./background-prop-types/background-color-overlay */ "./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-color-overlay.ts");
/* harmony import */ var _background_prop_types_background_image_overlay__WEBPACK_IMPORTED_MODULE_31__ = __webpack_require__(/*! ./background-prop-types/background-image-overlay */ "./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-image-overlay.ts");
/* harmony import */ var _background_prop_types_background_gradient_overlay__WEBPACK_IMPORTED_MODULE_32__ = __webpack_require__(/*! ./background-prop-types/background-gradient-overlay */ "./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-gradient-overlay.ts");
/* harmony import */ var _background_prop_types_background_image_position_offset__WEBPACK_IMPORTED_MODULE_33__ = __webpack_require__(/*! ./background-prop-types/background-image-position-offset */ "./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-image-position-offset.ts");
/* harmony import */ var _background_prop_types_background_image_size_scale__WEBPACK_IMPORTED_MODULE_34__ = __webpack_require__(/*! ./background-prop-types/background-image-size-scale */ "./packages/packages/libs/editor-props/src/prop-types/background-prop-types/background-image-size-scale.ts");
/* harmony import */ var _boolean__WEBPACK_IMPORTED_MODULE_35__ = __webpack_require__(/*! ./boolean */ "./packages/packages/libs/editor-props/src/prop-types/boolean.ts");
/* harmony import */ var _color_stop__WEBPACK_IMPORTED_MODULE_36__ = __webpack_require__(/*! ./color-stop */ "./packages/packages/libs/editor-props/src/prop-types/color-stop.ts");
/* harmony import */ var _gradient_color_stop__WEBPACK_IMPORTED_MODULE_37__ = __webpack_require__(/*! ./gradient-color-stop */ "./packages/packages/libs/editor-props/src/prop-types/gradient-color-stop.ts");
/* harmony import */ var _key_value__WEBPACK_IMPORTED_MODULE_38__ = __webpack_require__(/*! ./key-value */ "./packages/packages/libs/editor-props/src/prop-types/key-value.ts");
/* harmony import */ var _number_range__WEBPACK_IMPORTED_MODULE_39__ = __webpack_require__(/*! ./number-range */ "./packages/packages/libs/editor-props/src/prop-types/number-range.ts");
/* harmony import */ var _date_time__WEBPACK_IMPORTED_MODULE_40__ = __webpack_require__(/*! ./date-time */ "./packages/packages/libs/editor-props/src/prop-types/date-time.ts");
/* harmony import */ var _position__WEBPACK_IMPORTED_MODULE_41__ = __webpack_require__(/*! ./position */ "./packages/packages/libs/editor-props/src/prop-types/position.ts");
/* harmony import */ var _query__WEBPACK_IMPORTED_MODULE_42__ = __webpack_require__(/*! ./query */ "./packages/packages/libs/editor-props/src/prop-types/query.ts");
/* harmony import */ var _query_filter__WEBPACK_IMPORTED_MODULE_43__ = __webpack_require__(/*! ./query-filter */ "./packages/packages/libs/editor-props/src/prop-types/query-filter.ts");
/* harmony import */ var _html__WEBPACK_IMPORTED_MODULE_44__ = __webpack_require__(/*! ./html */ "./packages/packages/libs/editor-props/src/prop-types/html.ts");
/* harmony import */ var _html_v2__WEBPACK_IMPORTED_MODULE_45__ = __webpack_require__(/*! ./html-v2 */ "./packages/packages/libs/editor-props/src/prop-types/html-v2.ts");
/* harmony import */ var _html_v3__WEBPACK_IMPORTED_MODULE_46__ = __webpack_require__(/*! ./html-v3 */ "./packages/packages/libs/editor-props/src/prop-types/html-v3.ts");
/* harmony import */ var _filter_prop_types_filter__WEBPACK_IMPORTED_MODULE_47__ = __webpack_require__(/*! ./filter-prop-types/filter */ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter.ts");
/* harmony import */ var _transform_prop_types_transform__WEBPACK_IMPORTED_MODULE_48__ = __webpack_require__(/*! ./transform-prop-types/transform */ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform.ts");
/* harmony import */ var _transform_prop_types_transform_functions__WEBPACK_IMPORTED_MODULE_49__ = __webpack_require__(/*! ./transform-prop-types/transform-functions */ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions.ts");
/* harmony import */ var _transform_prop_types_transform_functions_move_transform__WEBPACK_IMPORTED_MODULE_50__ = __webpack_require__(/*! ./transform-prop-types/transform-functions/move-transform */ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions/move-transform.ts");
/* harmony import */ var _transform_prop_types_transform_functions_scale_transform__WEBPACK_IMPORTED_MODULE_51__ = __webpack_require__(/*! ./transform-prop-types/transform-functions/scale-transform */ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions/scale-transform.ts");
/* harmony import */ var _transform_prop_types_transform_functions_rotate_transform__WEBPACK_IMPORTED_MODULE_52__ = __webpack_require__(/*! ./transform-prop-types/transform-functions/rotate-transform */ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions/rotate-transform.ts");
/* harmony import */ var _transform_prop_types_transform_functions_skew_transform__WEBPACK_IMPORTED_MODULE_53__ = __webpack_require__(/*! ./transform-prop-types/transform-functions/skew-transform */ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions/skew-transform.ts");
/* harmony import */ var _transform_prop_types_transform_origin__WEBPACK_IMPORTED_MODULE_54__ = __webpack_require__(/*! ./transform-prop-types/transform-origin */ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-origin.ts");
/* harmony import */ var _transform_prop_types_perspective_origin__WEBPACK_IMPORTED_MODULE_55__ = __webpack_require__(/*! ./transform-prop-types/perspective-origin */ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/perspective-origin.ts");
/* harmony import */ var _filter_prop_types_backdrop_filter__WEBPACK_IMPORTED_MODULE_56__ = __webpack_require__(/*! ./filter-prop-types/backdrop-filter */ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/backdrop-filter.ts");
/* harmony import */ var _filter_prop_types_drop_shadow_filter__WEBPACK_IMPORTED_MODULE_57__ = __webpack_require__(/*! ./filter-prop-types/drop-shadow-filter */ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/drop-shadow-filter.ts");
/* harmony import */ var _filter_prop_types_filter_functions_blur_filter__WEBPACK_IMPORTED_MODULE_58__ = __webpack_require__(/*! ./filter-prop-types/filter-functions/blur-filter */ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter-functions/blur-filter.ts");
/* harmony import */ var _filter_prop_types_filter_functions_intensity_filter__WEBPACK_IMPORTED_MODULE_59__ = __webpack_require__(/*! ./filter-prop-types/filter-functions/intensity-filter */ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter-functions/intensity-filter.ts");
/* harmony import */ var _filter_prop_types_filter_functions_color_tone_filter__WEBPACK_IMPORTED_MODULE_60__ = __webpack_require__(/*! ./filter-prop-types/filter-functions/color-tone-filter */ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter-functions/color-tone-filter.ts");
/* harmony import */ var _filter_prop_types_filter_functions_hue_rotate_filter__WEBPACK_IMPORTED_MODULE_61__ = __webpack_require__(/*! ./filter-prop-types/filter-functions/hue-rotate-filter */ "./packages/packages/libs/editor-props/src/prop-types/filter-prop-types/filter-functions/hue-rotate-filter.ts");
/* harmony import */ var _date_range__WEBPACK_IMPORTED_MODULE_62__ = __webpack_require__(/*! ./date-range */ "./packages/packages/libs/editor-props/src/prop-types/date-range.ts");
/* harmony import */ var _date_string__WEBPACK_IMPORTED_MODULE_63__ = __webpack_require__(/*! ./date-string */ "./packages/packages/libs/editor-props/src/prop-types/date-string.ts");
/* harmony import */ var _time_range__WEBPACK_IMPORTED_MODULE_64__ = __webpack_require__(/*! ./time-range */ "./packages/packages/libs/editor-props/src/prop-types/time-range.ts");
/* harmony import */ var _time_string__WEBPACK_IMPORTED_MODULE_65__ = __webpack_require__(/*! ./time-string */ "./packages/packages/libs/editor-props/src/prop-types/time-string.ts");




































































/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/key-value.ts":
/*!*************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/key-value.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   keyValuePropTypeUtil: function() { return /* binding */ keyValuePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const keyValuePropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('key-value', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  key: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  value: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/layout-direction.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/layout-direction.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   layoutDirectionPropTypeUtil: function() { return /* binding */ layoutDirectionPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");


const layoutDirectionPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('layout-direction', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.object({
  row: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.any(),
  column: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.any()
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/link.ts":
/*!********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/link.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   linkPropTypeUtil: function() { return /* binding */ linkPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const linkPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('link', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  destination: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  isTargetBlank: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  tag: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/number-range.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/number-range.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   numberRangePropTypeUtil: function() { return /* binding */ numberRangePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const numberRangePropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('number-range', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  min: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  max: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/number.ts":
/*!**********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/number.ts ***!
  \**********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   numberPropTypeUtil: function() { return /* binding */ numberPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");


const numberPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('number', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number().nullable());

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/position.ts":
/*!************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/position.ts ***!
  \************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   positionPropTypeUtil: function() { return /* binding */ positionPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const positionPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('object-position', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  x: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  y: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/query-filter.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/query-filter.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   queryFilterArrayPropTypeUtil: function() { return /* binding */ queryFilterArrayPropTypeUtil; },
/* harmony export */   queryFilterPropTypeUtil: function() { return /* binding */ queryFilterPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const queryFilterPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('query-filter', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  key: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  values: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  taxonomies: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));
const queryFilterArrayPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createArrayPropUtils)(queryFilterPropTypeUtil.key, queryFilterPropTypeUtil.schema);

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/query.ts":
/*!*********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/query.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   queryPropTypeUtil: function() { return /* binding */ queryPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const queryPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('query', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  id: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  label: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/selection-size.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/selection-size.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   selectionSizePropTypeUtil: function() { return /* binding */ selectionSizePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _key_value__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./key-value */ "./packages/packages/libs/editor-props/src/prop-types/key-value.ts");
/* harmony import */ var _string__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./string */ "./packages/packages/libs/editor-props/src/prop-types/string.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");





const selectionSizePropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('selection-size', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  selection: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.union([_key_value__WEBPACK_IMPORTED_MODULE_2__.keyValuePropTypeUtil.schema, _string__WEBPACK_IMPORTED_MODULE_3__.stringPropTypeUtil.schema]),
  size: _utils__WEBPACK_IMPORTED_MODULE_4__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/shadow.ts":
/*!**********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/shadow.ts ***!
  \**********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   shadowPropTypeUtil: function() { return /* binding */ shadowPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const shadowPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('shadow', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  position: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  hOffset: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  vOffset: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  blur: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  spread: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  color: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/size.ts":
/*!********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/size.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   sizePropTypeUtil: function() { return /* binding */ sizePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");


const sizeNumberOrEmpty = _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.union([_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number(), _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.literal('')]);

// NOTE: The schema differs from the PHP schema, check size-prop-type.php for the actual schema.
const sizePropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('size', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  unit: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.enum(['px', 'em', 'rem', '%', 'vw', 'vh', 'ch', 'fr']),
  size: sizeNumberOrEmpty
}).or(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  unit: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.enum(['deg', 'rad', 'grad', 'turn']),
  size: sizeNumberOrEmpty
})).or(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  unit: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.enum(['s', 'ms']),
  size: sizeNumberOrEmpty
})).or(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  unit: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.literal('auto'),
  size: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.literal('')
})).or(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  unit: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.literal('custom'),
  size: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string()
})));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/span.ts":
/*!********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/span.ts ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   spanPropTypeUtil: function() { return /* binding */ spanPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");


const spanPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('span', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().nullable());

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/string-array.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/string-array.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   stringArrayPropTypeUtil: function() { return /* binding */ stringArrayPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _string__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./string */ "./packages/packages/libs/editor-props/src/prop-types/string.ts");


const stringArrayPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_0__.createArrayPropUtils)(_string__WEBPACK_IMPORTED_MODULE_1__.stringPropTypeUtil.key, _string__WEBPACK_IMPORTED_MODULE_1__.stringPropTypeUtil.schema);

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/string.ts":
/*!**********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/string.ts ***!
  \**********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   stringPropTypeUtil: function() { return /* binding */ stringPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");


const stringPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('string', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().nullable());

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/stroke.ts":
/*!**********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/stroke.ts ***!
  \**********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   strokePropTypeUtil: function() { return /* binding */ strokePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const strokePropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('stroke', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  color: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  width: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/svg-src.ts":
/*!***********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/svg-src.ts ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   svgSrcPropTypeUtil: function() { return /* binding */ svgSrcPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const svgSrcValueSchema = _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  id: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  url: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.null()
}).or(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  id: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.null(),
  url: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
})).or(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  id: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  url: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));
const svgSrcPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('svg-src', svgSrcValueSchema);

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/time-range.ts":
/*!**************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/time-range.ts ***!
  \**************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   timeRangePropTypeUtil: function() { return /* binding */ timeRangePropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const timeRangePropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('time-range', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  min: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  max: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/time-string.ts":
/*!***************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/time-string.ts ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   timeStringPropTypeUtil: function() { return /* binding */ timeStringPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");


const timeStringPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('time-string', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string());

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/perspective-origin.ts":
/*!*******************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/perspective-origin.ts ***!
  \*******************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   perspectiveOriginPropTypeUtil: function() { return /* binding */ perspectiveOriginPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const perspectiveOriginPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('perspective-origin', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  x: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  y: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions.ts":
/*!********************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions.ts ***!
  \********************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   transformFunctionsPropTypeUtil: function() { return /* binding */ transformFunctionsPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _transform_functions_move_transform__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./transform-functions/move-transform */ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions/move-transform.ts");
/* harmony import */ var _transform_functions_rotate_transform__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./transform-functions/rotate-transform */ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions/rotate-transform.ts");
/* harmony import */ var _transform_functions_scale_transform__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./transform-functions/scale-transform */ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions/scale-transform.ts");
/* harmony import */ var _transform_functions_skew_transform__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./transform-functions/skew-transform */ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions/skew-transform.ts");






const filterTypes = _transform_functions_move_transform__WEBPACK_IMPORTED_MODULE_2__.moveTransformPropTypeUtil.schema.or(_transform_functions_scale_transform__WEBPACK_IMPORTED_MODULE_4__.scaleTransformPropTypeUtil.schema).or(_transform_functions_rotate_transform__WEBPACK_IMPORTED_MODULE_3__.rotateTransformPropTypeUtil.schema).or(_transform_functions_skew_transform__WEBPACK_IMPORTED_MODULE_5__.skewTransformPropTypeUtil.schema);
const transformFunctionsPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('transform-functions', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.array(filterTypes));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions/move-transform.ts":
/*!***********************************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions/move-transform.ts ***!
  \***********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   moveTransformPropTypeUtil: function() { return /* binding */ moveTransformPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../types */ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/types.ts");




const moveTransformPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)(_types__WEBPACK_IMPORTED_MODULE_3__.TransformFunctionKeys.move, _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  x: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  y: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  z: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions/rotate-transform.ts":
/*!*************************************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions/rotate-transform.ts ***!
  \*************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   rotateTransformPropTypeUtil: function() { return /* binding */ rotateTransformPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../types */ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/types.ts");




const rotateTransformPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)(_types__WEBPACK_IMPORTED_MODULE_3__.TransformFunctionKeys.rotate, _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  x: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  y: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  z: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions/scale-transform.ts":
/*!************************************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions/scale-transform.ts ***!
  \************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   scaleTransformPropTypeUtil: function() { return /* binding */ scaleTransformPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _number__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../number */ "./packages/packages/libs/editor-props/src/prop-types/number.ts");
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../types */ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/types.ts");




const scaleTransformPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)(_types__WEBPACK_IMPORTED_MODULE_3__.TransformFunctionKeys.scale, _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  x: _number__WEBPACK_IMPORTED_MODULE_2__.numberPropTypeUtil.schema.nullable(),
  y: _number__WEBPACK_IMPORTED_MODULE_2__.numberPropTypeUtil.schema.nullable(),
  z: _number__WEBPACK_IMPORTED_MODULE_2__.numberPropTypeUtil.schema.nullable()
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions/skew-transform.ts":
/*!***********************************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-functions/skew-transform.ts ***!
  \***********************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   skewTransformPropTypeUtil: function() { return /* binding */ skewTransformPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../../utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../types */ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/types.ts");




const skewTransformPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)(_types__WEBPACK_IMPORTED_MODULE_3__.TransformFunctionKeys.skew, _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  x: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  y: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-origin.ts":
/*!*****************************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform-origin.ts ***!
  \*****************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   transformOriginPropTypeUtil: function() { return /* binding */ transformOriginPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const transformOriginPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('transform-origin', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  x: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  y: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  z: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform.ts":
/*!**********************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/transform.ts ***!
  \**********************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   transformPropTypeUtil: function() { return /* binding */ transformPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const transformPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('transform', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  'transform-functions': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'transform-origin': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  perspective: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  'perspective-origin': _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
}));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/types.ts":
/*!******************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/transform-prop-types/types.ts ***!
  \******************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   TransformFunctionKeys: function() { return /* binding */ TransformFunctionKeys; }
/* harmony export */ });
const TransformFunctionKeys = {
  move: 'transform-move',
  scale: 'transform-scale',
  rotate: 'transform-rotate',
  skew: 'transform-skew'
};

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/url.ts":
/*!*******************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/url.ts ***!
  \*******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   urlPropTypeUtil: function() { return /* binding */ urlPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");


const urlPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('url', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string().nullable());

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/utils.ts":
/*!*********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/utils.ts ***!
  \*********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   unknownChildrenSchema: function() { return /* binding */ unknownChildrenSchema; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);

const unknownChildrenSchema = _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.any().nullable();

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/video-attachment-id.ts":
/*!***********************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/video-attachment-id.ts ***!
  \***********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   videoAttachmentIdPropType: function() { return /* binding */ videoAttachmentIdPropType; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");


const videoAttachmentIdPropType = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('video-attachment-id', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.number());

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/prop-types/video-src.ts":
/*!*************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/prop-types/video-src.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   videoSrcPropTypeUtil: function() { return /* binding */ videoSrcPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils */ "./packages/packages/libs/editor-props/src/prop-types/utils.ts");



const videoSrcPropTypeUtil = (0,_utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_1__.createPropUtils)('video-src', _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  id: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema,
  url: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.null()
}).or(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
  id: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.null(),
  url: _utils__WEBPACK_IMPORTED_MODULE_2__.unknownChildrenSchema
})));

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/types.ts":
/*!**********************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/types.ts ***!
  \**********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);


/***/ }),

/***/ "./packages/packages/libs/editor-props/src/utils/adjust-llm-prop-value-schema.ts":
/*!***************************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/utils/adjust-llm-prop-value-schema.ts ***!
  \***************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   adjustLlmPropValueSchema: function() { return /* binding */ adjustLlmPropValueSchema; }
/* harmony export */ });
/* harmony import */ var _prop_types__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../prop-types */ "./packages/packages/libs/editor-props/src/prop-types/index.ts");

const ensureNotNull = (v, fallback) => v === null ? fallback : v;
const defaultOptions = {
  transformers: {}
};
const adjustLlmPropValueSchema = (value, {
  transformers = {},
  forceKey = undefined
} = defaultOptions) => {
  const clone = structuredClone(value);
  if (typeof clone !== 'object' || clone === null) {
    return null;
  }
  // Check for transformable types
  if (Array.isArray(clone)) {
    return clone.map(item => adjustLlmPropValueSchema(item, {
      forceKey,
      transformers
    }));
  }
  const transformablePropValue = clone;
  if ('$intention' in transformablePropValue) {
    delete transformablePropValue.$intention;
  }
  if (forceKey) {
    transformablePropValue.$$type = forceKey;
  }

  // fix by type - Size is the only case where we have a non-valid structure
  switch (transformablePropValue.$$type) {
    case 'size':
      {
        const {
          value: rawSizePropValue
        } = transformablePropValue;
        const unit = typeof rawSizePropValue.unit === 'string' ? rawSizePropValue.unit : ensureNotNull(_prop_types__WEBPACK_IMPORTED_MODULE_0__.stringPropTypeUtil.extract(rawSizePropValue.unit), 'px');
        const size = typeof rawSizePropValue.size === 'string' || typeof rawSizePropValue.size === 'number' ? rawSizePropValue.size : ensureNotNull(_prop_types__WEBPACK_IMPORTED_MODULE_0__.stringPropTypeUtil.extract(rawSizePropValue.size), _prop_types__WEBPACK_IMPORTED_MODULE_0__.numberPropTypeUtil.extract(rawSizePropValue.size));
        return {
          $$type: 'size',
          value: {
            unit,
            size
          }
        };
      }
    case 'html-v3':
      {
        const {
          value: rawHtmlV3PropValue
        } = transformablePropValue;
        return {
          $$type: 'html-v3',
          value: {
            ...rawHtmlV3PropValue,
            children: Array.isArray(rawHtmlV3PropValue.children) ? rawHtmlV3PropValue.children : []
          }
        };
      }
    default:
      const transformer = transformers?.[transformablePropValue.$$type];
      if (transformer) {
        return transformer(transformablePropValue.value);
      }
  }
  if (typeof transformablePropValue.value === 'object') {
    if (Array.isArray(transformablePropValue.value)) {
      transformablePropValue.value = adjustLlmPropValueSchema(transformablePropValue.value, {
        transformers
      });
    } else {
      const {
        value: objectValue
      } = transformablePropValue;
      const clonedObject = clone;
      clonedObject.value = {}; // Record< string, PropValue >;
      Object.entries(objectValue).forEach(([key, childProp]) => {
        clonedObject.value[key] = adjustLlmPropValueSchema(childProp, {
          transformers
        });
      });
    }
  }
  return clone;
};

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts":
/*!****************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts ***!
  \****************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   createArrayPropUtils: function() { return /* binding */ createArrayPropUtils; },
/* harmony export */   createPropUtils: function() { return /* binding */ createPropUtils; },
/* harmony export */   getPropSchemaFromCache: function() { return /* binding */ getPropSchemaFromCache; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);

const SCHEMA_CACHE = new Map();
/**
 * Usage example:
 *
 * ```ts
 * const elementsPropUtils = createPropUtils( 'elements', z.array( z.string() ) );
 *
 * elementsPropUtils.isValid( element.props?.children );
 * elementsPropUtils.create( [ 'a', 'b' ] );
 * elementsPropUtils.create( ( prev = [] ) => [ ...prev, 'c' ], { base: element.props?.children } );
 * elementsPropUtils.create( ( prev = [] ) => [ ...prev, 'c' ], { disabled: true } );
 * elementsPropUtils.extract( element.props?.children );
 *
 * ```
 */

function getPropSchemaFromCache(key) {
  return SCHEMA_CACHE.get(key);
}
function createPropUtils(key, valueSchema) {
  const schema = _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.strictObject({
    $$type: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.literal(key),
    value: valueSchema,
    disabled: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.boolean().optional()
  });
  function isValid(prop) {
    return schema.safeParse(prop).success;
  }
  function create(value, createOptions) {
    const fn = typeof value === 'function' ? value : () => value;
    const {
      base,
      disabled
    } = createOptions || {};
    if (!base) {
      return {
        $$type: key,
        value: fn(),
        ...(disabled && {
          disabled
        })
      };
    }
    if (!isValid(base)) {
      throw new Error(`Cannot create prop based on invalid value: ${JSON.stringify(base)}`);
    }
    return {
      $$type: key,
      value: fn(base.value),
      ...(disabled && {
        disabled
      })
    };
  }
  function extract(prop) {
    if (!isValid(prop)) {
      return null;
    }
    return prop.value;
  }
  const propUtil = {
    extract,
    isValid,
    create,
    schema,
    key: key
  };
  SCHEMA_CACHE.set(key, propUtil);
  return propUtil;
}
function createArrayPropUtils(key, valueSchema, overrideKey) {
  return createPropUtils(overrideKey || `${key}-array`, _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.array(valueSchema));
}

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/utils/filter-empty-values.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/utils/filter-empty-values.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   filterEmptyValues: function() { return /* binding */ filterEmptyValues; },
/* harmony export */   isEmpty: function() { return /* binding */ isEmpty; }
/* harmony export */ });
/* harmony import */ var _utils_is_transformable__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../utils/is-transformable */ "./packages/packages/libs/editor-props/src/utils/is-transformable.ts");

const filterEmptyValues = value => {
  if (isEmpty(value)) {
    return null;
  }
  if (Array.isArray(value)) {
    return value.map(filterEmptyValues).filter(item => !isEmpty(item));
  }
  if (typeof value === 'object') {
    return Object.fromEntries(Object.entries(value).map(([key, val]) => [key, filterEmptyValues(val)]).filter(([, val]) => !isEmpty(val)));
  }
  return value;
};
const isEmpty = value => {
  if (value && (0,_utils_is_transformable__WEBPACK_IMPORTED_MODULE_0__.isTransformable)(value)) {
    return isEmpty(value.value);
  }
  return isNullish(value) || isNullishArray(value) || isNullishObject(value);
};
const isNullish = value => value === null || value === undefined || value === '';
const isNullishArray = value => Array.isArray(value) && value.every(isEmpty);
const isNullishObject = value => {
  return typeof value === 'object' && isNullishArray(Object.values(value));
};

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/utils/is-overridable.ts":
/*!*************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/utils/is-overridable.ts ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   isOverridable: function() { return /* binding */ isOverridable; },
/* harmony export */   rewrapOverridableValue: function() { return /* binding */ rewrapOverridableValue; }
/* harmony export */ });
/* harmony import */ var _is_transformable__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./is-transformable */ "./packages/packages/libs/editor-props/src/utils/is-transformable.ts");

function isOverridable(value) {
  return (0,_is_transformable__WEBPACK_IMPORTED_MODULE_0__.isTransformable)(value) && value.$$type === 'overridable';
}
function rewrapOverridableValue(existing, newInner) {
  return {
    ...existing,
    value: {
      ...existing.value,
      origin_value: newInner
    }
  };
}

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/utils/is-transformable.ts":
/*!***************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/utils/is-transformable.ts ***!
  \***************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   isTransformable: function() { return /* binding */ isTransformable; }
/* harmony export */ });
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @elementor/schema */ "@elementor/schema");
/* harmony import */ var _elementor_schema__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_elementor_schema__WEBPACK_IMPORTED_MODULE_0__);

const transformableSchema = _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.object({
  $$type: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.string(),
  value: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.any(),
  disabled: _elementor_schema__WEBPACK_IMPORTED_MODULE_0__.z.boolean().optional()
});
const isTransformable = value => {
  return transformableSchema.safeParse(value).success;
};

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/utils/llm-schema-to-props.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/utils/llm-schema-to-props.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   jsonSchemaToPropType: function() { return /* binding */ jsonSchemaToPropType; },
/* harmony export */   jsonSchemaToPropsSchema: function() { return /* binding */ jsonSchemaToPropsSchema; }
/* harmony export */ });
function jsonSchemaToPropType(schema, key = schema.key) {
  const meta = {};
  if (schema.description) {
    meta.description = schema.description;
  }

  // Handle union types (anyOf)
  if (schema.anyOf && Array.isArray(schema.anyOf)) {
    return convertJsonSchemaToUnionPropType(schema, meta);
  }

  // Handle object types
  if (schema.type === 'object' && schema.properties) {
    return convertJsonSchemaToObjectPropType(schema, meta, key);
  }

  // Handle array types
  if (schema.type === 'array' && schema.items) {
    return convertJsonSchemaToArrayPropType(schema, meta, key);
  }

  // Handle plain types (string, number, boolean)
  return convertJsonSchemaToPlainPropType(schema, meta, key);
}
function convertJsonSchemaToPlainPropType(schema, meta, key = schema.key) {
  const settings = {};

  // Determine the key based on type
  let propKey = key || 'string';
  if (schema.type === 'number') {
    propKey = 'number';
  } else if (schema.type === 'boolean') {
    propKey = 'boolean';
  } else if (schema.type === 'string') {
    propKey = 'string';
  }

  // Handle enum values
  if (Array.isArray(schema.enum)) {
    settings.enum = schema.enum;
  }
  return {
    kind: 'plain',
    key: propKey,
    settings,
    meta
  };
}

/**
 * Converts a JSON Schema anyOf to a union PropType
 * @param schema
 * @param meta
 */
function convertJsonSchemaToUnionPropType(schema, meta) {
  const propTypes = {};
  if (!schema.anyOf || !Array.isArray(schema.anyOf)) {
    throw new Error('Invalid anyOf schema');
  }

  // Process each variant in the anyOf array
  for (const variantSchema of schema.anyOf) {
    // Each variant should be an object with $$type and value properties
    if (variantSchema.type === 'object' && variantSchema.properties && variantSchema.properties.$$type && variantSchema.properties.value) {
      const typeProperty = variantSchema.properties.$$type;

      // Extract the type key from the enum
      let typeKey;
      if (typeProperty.enum && Array.isArray(typeProperty.enum) && typeProperty.enum.length > 0) {
        typeKey = typeProperty.enum[0];
      } else {
        continue;
      }

      // Convert the value schema to a PropType
      const valuePropType = convertJsonSchemaToPropType(variantSchema.properties.value);
      propTypes[typeKey] = valuePropType;
    }
  }
  return {
    kind: 'union',
    prop_types: propTypes,
    settings: {},
    meta
  };
}
function convertJsonSchemaToObjectPropType(schema, meta, key = schema.key) {
  const shape = {};
  if (!schema.properties) {
    return {
      kind: 'object',
      key,
      shape: {},
      settings: {},
      meta
    };
  }
  const requiredFields = Array.isArray(schema.required) ? schema.required : [];

  // Convert each property
  for (const [propKey, propSchema] of Object.entries(schema.properties)) {
    const subPropType = convertJsonSchemaToPropType(propSchema, key);

    // Mark as required if it's in the required array
    if (requiredFields.includes(propKey)) {
      subPropType.settings = {
        ...subPropType.settings,
        required: true
      };
    }
    shape[propKey] = subPropType;
  }
  return {
    kind: 'object',
    key: key || 'object',
    shape,
    settings: {},
    meta
  };
}
function convertJsonSchemaToArrayPropType(schema, meta, key = schema.key) {
  if (!schema.items) {
    throw new Error('Array schema must have items property');
  }
  const itemPropType = convertJsonSchemaToPropType(schema.items);
  return {
    kind: 'array',
    key: key || 'array',
    item_prop_type: itemPropType,
    settings: {},
    meta
  };
}
function convertJsonSchemaToPropType(schema, key) {
  return jsonSchemaToPropType(schema, key);
}

/**
 * Converts a complete JSON Schema object back to a PropsSchema
 *
 * @param jsonSchema The JSON Schema to convert
 */
function jsonSchemaToPropsSchema(jsonSchema) {
  const propsSchema = {};
  if (jsonSchema.type !== 'object' || !jsonSchema.properties) {
    throw new Error('Root schema must be an object with properties');
  }
  for (const [key, propSchema] of Object.entries(jsonSchema.properties)) {
    propsSchema[key] = convertJsonSchemaToPropType(propSchema, key);
  }
  return propsSchema;
}

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/utils/merge-props.ts":
/*!**********************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/utils/merge-props.ts ***!
  \**********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   mergeProps: function() { return /* binding */ mergeProps; }
/* harmony export */ });
function mergeProps(current, updates) {
  // edge case, the server returns an array instead of an object when empty props because of PHP array / object conversion
  let props = {};
  if (!Array.isArray(current)) {
    props = structuredClone(current);
  }
  Object.entries(updates).forEach(([key, value]) => {
    if (value === null || value === undefined) {
      // eslint-disable-next-line @typescript-eslint/no-dynamic-delete
      delete props[key];
    } else {
      props[key] = value;
    }
  });
  return props;
}

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/utils/parse-html-children.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/utils/parse-html-children.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   parseHtmlChildren: function() { return /* binding */ parseHtmlChildren; }
/* harmony export */ });
const INLINE_ELEMENTS = new Set(['span', 'b', 'strong', 'i', 'em', 'u', 'a', 'del', 'sup', 'sub', 's']);
function generateElementId() {
  const timestamp = Date.now().toString(36);
  const randomPart = Math.random().toString(36).substring(2, 9);
  return `e-${timestamp}-${randomPart}`;
}
function traverseChildren(node) {
  const result = [];
  for (const child of Array.from(node.children)) {
    const tagName = child.tagName.toLowerCase();
    if (!INLINE_ELEMENTS.has(tagName)) {
      result.push(...traverseChildren(child));
      continue;
    }
    let id = child.getAttribute('id');
    if (!id) {
      id = generateElementId();
      child.setAttribute('id', id);
    }
    const childElement = {
      id,
      type: tagName
    };
    const textContent = child.textContent?.trim();
    if (textContent) {
      childElement.content = textContent;
    }
    const nestedChildren = traverseChildren(child);
    if (nestedChildren.length > 0) {
      childElement.children = nestedChildren;
    }
    result.push(childElement);
  }
  return result;
}
function parseHtmlChildren(html) {
  if (!html) {
    return {
      content: html,
      children: []
    };
  }
  const parser = new DOMParser();
  const doc = parser.parseFromString(`<body>${html}</body>`, 'text/html');
  const parserError = doc.querySelector('parsererror');
  if (parserError) {
    // eslint-disable-next-line no-console
    console.warn('HTML parsing error, returning original content:', parserError.textContent);
    return {
      content: html,
      children: []
    };
  }
  const body = doc.body;
  const children = traverseChildren(body);
  return {
    content: body.innerHTML,
    children
  };
}

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/utils/prop-dependency-utils.ts":
/*!********************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/utils/prop-dependency-utils.ts ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   evaluateTerm: function() { return /* binding */ evaluateTerm; },
/* harmony export */   extractValue: function() { return /* binding */ extractValue; },
/* harmony export */   isDependency: function() { return /* binding */ isDependency; },
/* harmony export */   isDependencyMet: function() { return /* binding */ isDependencyMet; }
/* harmony export */ });
/* harmony import */ var _is_overridable__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./is-overridable */ "./packages/packages/libs/editor-props/src/utils/is-overridable.ts");
/* harmony import */ var _is_transformable__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./is-transformable */ "./packages/packages/libs/editor-props/src/utils/is-transformable.ts");


function isDependencyMet(dependency, values) {
  if (!dependency?.terms.length) {
    return {
      isMet: true
    };
  }
  const {
    relation,
    terms
  } = dependency;
  const method = getRelationMethod(relation);
  const failingDependencies = [];
  const isMet = terms[method](term => {
    const isNestedDependency = isDependency(term);
    const result = isNestedDependency ? isDependencyMet(term, values).isMet : evaluateTerm(term, extractValue(term.path, values, term.nestedPath)?.value);
    if (!result) {
      failingDependencies.push(term);
    }
    return result;
  });
  return {
    isMet,
    failingDependencies
  };
}
function evaluateTerm(term, actualValue) {
  const {
    value: valueToCompare,
    operator
  } = term;
  switch (operator) {
    case 'eq':
    case 'ne':
      return actualValue === valueToCompare === ('eq' === operator);
    case 'gt':
    case 'lte':
      if (!isNumber(actualValue) || !isNumber(valueToCompare)) {
        return false;
      }
      return Number(actualValue) > Number(valueToCompare) === ('gt' === operator);
    case 'lt':
    case 'gte':
      if (!isNumber(actualValue) || !isNumber(valueToCompare)) {
        return false;
      }
      return Number(actualValue) < Number(valueToCompare) === ('lt' === operator);
    case 'in':
    case 'nin':
      if (!Array.isArray(valueToCompare)) {
        return false;
      }
      return valueToCompare.includes(actualValue) === ('in' === operator);
    case 'contains':
    case 'ncontains':
      if (('string' !== typeof actualValue || 'string' !== typeof valueToCompare) && !Array.isArray(actualValue)) {
        return false;
      }
      const transformedValue = Array.isArray(actualValue) ? actualValue.map(item => (0,_is_transformable__WEBPACK_IMPORTED_MODULE_1__.isTransformable)(item) ? item.value : item) : actualValue;
      return 'contains' === operator === transformedValue.includes(valueToCompare);
    case 'exists':
    case 'not_exist':
      const evaluation = !!actualValue || 0 === actualValue || false === actualValue;
      return 'exists' === operator === evaluation;
    default:
      return true;
  }
}
function isNumber(value) {
  return typeof value === 'number' && !isNaN(value);
}
function getRelationMethod(relation) {
  switch (relation) {
    case 'or':
      return 'some';
    case 'and':
      return 'every';
    default:
      throw new Error(`Relation not supported ${relation}`);
  }
}
function extractValue(path, elementValues, nestedPath = [], options = {}) {
  const {
    unwrapOverridableLeaf = true
  } = options;
  const extractedValue = path.reduce((acc, key, index) => {
    const value = acc?.[key];
    if (index === path.length - 1) {
      return value;
    }
    if ((0,_is_overridable__WEBPACK_IMPORTED_MODULE_0__.isOverridable)(value)) {
      const inner = value.value.origin_value;
      return (0,_is_transformable__WEBPACK_IMPORTED_MODULE_1__.isTransformable)(inner) ? inner.value ?? null : inner;
    }
    if ((0,_is_transformable__WEBPACK_IMPORTED_MODULE_1__.isTransformable)(value)) {
      return value.value ?? null;
    }
    return value;
  }, elementValues);
  let resolved = extractedValue;
  if (unwrapOverridableLeaf && resolved && (0,_is_overridable__WEBPACK_IMPORTED_MODULE_0__.isOverridable)(resolved)) {
    resolved = resolved.value.origin_value ?? null;
  }
  if (!nestedPath?.length) {
    return resolved;
  }
  const nestedValue = nestedPath.reduce((acc, key) => acc?.[key], resolved?.value);
  return {
    $$type: 'unknown',
    value: nestedValue
  };
}
function isDependency(term) {
  return 'terms' in term;
}

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/utils/props-to-llm-schema.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/utils/props-to-llm-schema.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   configurableKeys: function() { return /* binding */ configurableKeys; },
/* harmony export */   enrichWithIntention: function() { return /* binding */ enrichWithIntention; },
/* harmony export */   isPropKeyConfigurable: function() { return /* binding */ isPropKeyConfigurable; },
/* harmony export */   nonConfigurablePropKeys: function() { return /* binding */ nonConfigurablePropKeys; },
/* harmony export */   propTypeToJsonSchema: function() { return /* binding */ propTypeToJsonSchema; },
/* harmony export */   removeIntention: function() { return /* binding */ removeIntention; },
/* harmony export */   setDynamicTagNamesResolver: function() { return /* binding */ setDynamicTagNamesResolver; }
/* harmony export */ });
const DYNAMIC_PROP_TYPE_KEY = 'dynamic';
const OVERRIDABLE_PROP_TYPE_KEY = 'overridable';
// Host (editor-canvas) injects a resolver that maps a prop's accepted categories to the names of the
// dynamic tags allowed for it. Keeping it injectable preserves this lib's purity: without a host the
// `name` field stays an open string instead of an enum.
let dynamicTagNamesResolver = null;
function setDynamicTagNamesResolver(resolver) {
  dynamicTagNamesResolver = resolver;
}

// A dynamic value replaces the value of the exact node it is attached to, which may be a nested
// field (e.g. an image's `src`) rather than the property root. It is advertised once per branch, at
// the outermost prop type that supports it, and suppressed for descendants of that node to avoid
// offering the same dynamic option twice on a single branch.
function propTypeToJsonSchema(propType, suppressDynamic = false) {
  const description = propType.meta?.description;
  const schema = {};
  if (description) {
    schema.description = description;
  }

  // Add example from initial_value if it exists
  if (propType.initial_value !== null && propType.initial_value !== undefined) {
    schema.examples = [propType.initial_value];
  }

  // Handle different kinds of prop types
  switch (propType.kind) {
    case 'union':
      return convertUnionPropType(propType, schema, suppressDynamic);
    case 'object':
      return convertObjectPropType(propType, schema, suppressDynamic);
    case 'array':
      return convertArrayPropType(propType, schema, suppressDynamic);
    default:
      return convertPlainPropType(propType, schema);
  }
}
function convertPlainPropType(propType, baseSchema) {
  const schema = {
    ...baseSchema
  };

  // This could happen when data is malformed due to a bug, added this as a safeguard.
  if (!Object.hasOwn(propType, 'kind')) {
    throw new Error(`PropType kind is undefined for propType with key: ${propType.key ?? '[unknown key]'}`);
  }
  const enumValues = propType.settings?.enum || [];
  switch (propType.kind) {
    case 'string':
    case 'number':
    case 'boolean':
      return {
        ...schema,
        type: 'object',
        properties: {
          $$type: {
            type: 'string',
            const: propType.key ?? propType.kind
          },
          value: {
            type: propType.kind,
            ...(enumValues.length > 0 ? {
              enum: enumValues
            } : {})
          }
        },
        required: ['$$type', 'value']
      };
    // @ts-expect-error: 'unknown' is a possible value at runtime - treat as "any"
    case 'unknown':
      return {};
    default:
      return {
        ...schema,
        type: 'object',
        $$type: propType.kind,
        value: {
          type: propType.kind
        }
      };
  }
}

/**
 * Converts a union prop type to JSON Schema (anyOf).
 *
 * @param propType        The union prop type to convert
 * @param baseSchema      Base schema to extend
 * @param suppressDynamic When true, an ancestor already offered the dynamic option for this branch
 */
function convertUnionPropType(propType, baseSchema, suppressDynamic) {
  const schema = structuredClone(baseSchema);
  const propTypes = propType.prop_types || {};
  const offersDynamic = !suppressDynamic && Boolean(propTypes[DYNAMIC_PROP_TYPE_KEY]);
  const suppressNestedDynamic = suppressDynamic || offersDynamic;
  const schemas = [];

  // Convert each prop type in the union
  for (const [typeKey, subPropType] of Object.entries(propTypes)) {
    if (typeKey === OVERRIDABLE_PROP_TYPE_KEY) {
      continue;
    }
    if (typeKey === DYNAMIC_PROP_TYPE_KEY) {
      if (offersDynamic) {
        schemas.push(convertDynamicPropType(subPropType));
      }
      continue;
    }
    schemas.push(propTypeToJsonSchema(subPropType, suppressNestedDynamic));
  }
  if (schemas.length > 0) {
    schema.anyOf = schemas;
  }
  const propTypeDescription = propType.meta?.description;
  if (propTypeDescription) {
    schema.description = propTypeDescription;
  }
  return schema;
}

// Emits a compact representation of the `dynamic` union member. It is offered as one option of THIS
// node's value (e.g. a property root, or a nested field such as an image's `src`): put the dynamic
// object exactly here, in place of the sibling static variant. Only `name` is required from the LLM
// (constrained to the tags allowed here); `group` is filled by the host resolver, and `settings` are
// described per-tag in the dynamic-tags resource, so the full tag catalog is never inlined.
function convertDynamicPropType(propType) {
  const categories = Array.isArray(propType.settings?.categories) ? propType.settings.categories : [];
  const allowedTagNames = dynamicTagNamesResolver?.(categories) ?? [];
  return {
    type: 'object',
    description: 'Bind THIS value to a dynamic tag instead of a static value (this may be a nested field, ' + 'e.g. an image\'s "src"). Look up the chosen tag in the "elementor://dynamic-tags" resource ' + 'and populate "settings" exactly as its schema requires.',
    properties: {
      $$type: {
        type: 'string',
        const: DYNAMIC_PROP_TYPE_KEY
      },
      value: {
        type: 'object',
        properties: {
          name: {
            type: 'string',
            description: 'Dynamic tag name from "elementor://dynamic-tags".',
            ...(allowedTagNames.length ? {
              enum: allowedTagNames
            } : {})
          },
          settings: {
            type: 'object',
            description: "Tag settings matching the chosen tag's schema in the resource."
          }
        },
        required: ['name']
      }
    },
    required: ['$$type', 'value']
  };
}
function convertObjectPropType(propType, baseSchema, suppressDynamic) {
  const schema = structuredClone(baseSchema);
  schema.type = 'object';
  const internalStructure = {
    properties: {
      $$type: {
        type: 'string',
        const: propType.key
      },
      value: {
        type: 'object',
        properties: {},
        additionalProperties: false
      }
    }
  };
  const required = ['$$type', 'value'];
  const valueRequired = [];
  const shape = propType.shape || {};

  // Convert each property in the object shape
  for (const [key, subPropType] of Object.entries(shape)) {
    const propSchema = propTypeToJsonSchema(subPropType, suppressDynamic);

    // Check if this property is required
    if (subPropType.settings?.required === true) {
      valueRequired.push(key);
    }
    if (internalStructure.properties.value.properties) {
      internalStructure.properties.value.properties[key] = propSchema;
    }
  }
  schema.required = required;
  if (valueRequired.length > 0) {
    internalStructure.properties.value.required = valueRequired;
  }
  return {
    ...schema,
    ...internalStructure
  };
}
function convertArrayPropType(propType, baseSchema, suppressDynamic) {
  const schema = structuredClone(baseSchema);
  schema.type = 'object';
  let items;
  const itemPropType = propType.item_prop_type;
  if (itemPropType) {
    items = propTypeToJsonSchema(itemPropType, suppressDynamic);
  }
  schema.properties = {
    $$type: {
      type: 'string',
      const: propType.key
    },
    value: {
      type: 'array',
      ...(items ? {
        items
      } : {})
    }
  };
  return schema;
}
const nonConfigurablePropKeys = ['_cssid', 'classes', 'attributes'];
function isPropKeyConfigurable(propKey, propType) {
  if (!nonConfigurablePropKeys.includes(propKey)) {
    return true;
  }
  return !!(!Array.isArray(propType?.meta) && propType?.meta?.llm_configurable);
}
function configurableKeys(schema) {
  return Object.keys(schema).filter(key => isPropKeyConfigurable(key, schema[key]));
}
function enrichWithIntention(jsonSchema, text = 'Describe the desired outcome') {
  const result = structuredClone(jsonSchema);
  if (!result.properties) {
    return jsonSchema;
  }
  result.properties.$intention = {
    type: 'string',
    description: text
  };
  result.required = [...(result.required || []), '$intention'];
  return result;
}
function removeIntention(jsonSchema) {
  const result = structuredClone(jsonSchema);
  if (!result.properties) {
    return jsonSchema;
  }
  delete result.properties.$intention;
  if (result.required) {
    result.required = result.required.filter(req => req !== '$intention');
  }
  return result;
}

/***/ }),

/***/ "./packages/packages/libs/editor-props/src/utils/validate-prop-value.ts":
/*!******************************************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/utils/validate-prop-value.ts ***!
  \******************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   validatePropValue: function() { return /* binding */ validatePropValue; },
/* harmony export */   validatePropValueDetailed: function() { return /* binding */ validatePropValueDetailed; }
/* harmony export */ });
/* harmony import */ var jsonschema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! jsonschema */ "./node_modules/jsonschema/lib/index.js");
/* harmony import */ var _props_to_llm_schema__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./props-to-llm-schema */ "./packages/packages/libs/editor-props/src/utils/props-to-llm-schema.ts");



/**
 * Detailed error information with nested anyOf variant errors
 */

/**
 * Recursively processes validation errors to provide detailed information about anyOf failures
 *
 * @param error The validation error to process
 */
function processValidationError(error) {
  const detailed = {
    path: error.path,
    message: error.message,
    schema: error.schema,
    instance: error.instance,
    name: error.name
  };

  // If this is an anyOf error, re-validate against each variant to get nested errors
  if (error.name === 'anyOf' && error.schema && typeof error.schema === 'object' && 'anyOf' in error.schema) {
    const anyOfSchema = error.schema;
    const variants = (anyOfSchema.anyOf || []).map((variantSchema, idx) => {
      // Re-validate the instance against this specific variant
      const variantResult = (0,jsonschema__WEBPACK_IMPORTED_MODULE_0__.validate)(error.instance, variantSchema);

      // Get discriminator from schema if available
      let discriminator = `variant-${idx}`;
      if (variantSchema && typeof variantSchema === 'object' && 'properties' in variantSchema && variantSchema.properties && typeof variantSchema.properties === 'object' && '$$type' in variantSchema.properties) {
        const typeProperty = variantSchema.properties.$$type;
        if (typeProperty && typeof typeProperty === 'object' && 'const' in typeProperty && typeof typeProperty.const === 'string') {
          discriminator = typeProperty.const;
        }
      }
      return {
        discriminator,
        errors: variantResult.errors.map(processValidationError)
      };
    });
    detailed.variants = variants;
  }
  return detailed;
}

/**
 * Formats detailed errors into a human-readable string
 * @param errors
 * @param indent
 */
function formatDetailedErrors(errors, indent = '') {
  const lines = [];
  for (const error of errors) {
    const pathStr = error.path.length > 0 ? error.path.join('.') : 'root';
    lines.push(`${indent}Error at ${pathStr}: ${error.message}`);
    if (error.variants && error.variants.length > 0) {
      lines.push(`${indent}  Tried ${error.variants.length} variant(s):`);
      for (const variant of error.variants) {
        lines.push(`${indent}    - ${variant.discriminator}:`);
        if (variant.errors.length === 0) {
          lines.push(`${indent}        (no errors - this variant matched!)`);
        } else {
          for (const nestedError of variant.errors) {
            const nestedPathStr = nestedError.path.length > 0 ? nestedError.path.join('.') : 'root';
            lines.push(`${indent}        ${nestedPathStr}: ${nestedError.message}`);

            // Recursively format nested variant errors
            if (nestedError.variants && nestedError.variants.length > 0) {
              lines.push(formatDetailedErrors([nestedError], `${indent}        `));
            }
          }
        }
      }
    }
  }
  return lines.join('\n');
}
const validatePropValue = (schema, value) => {
  const jsonSchema = (0,_props_to_llm_schema__WEBPACK_IMPORTED_MODULE_1__.propTypeToJsonSchema)(schema);
  if (value === null) {
    return {
      valid: true,
      errors: [],
      errorMessages: [],
      jsonSchema: JSON.stringify((0,_props_to_llm_schema__WEBPACK_IMPORTED_MODULE_1__.propTypeToJsonSchema)(schema))
    };
  }
  const result = (0,jsonschema__WEBPACK_IMPORTED_MODULE_0__.validate)(value, jsonSchema);
  const detailedErrors = result.errors.map(processValidationError);
  return {
    valid: result.valid,
    errors: result.errors,
    errorMessages: formatDetailedErrors(detailedErrors),
    jsonSchema: JSON.stringify(jsonSchema)
  };
};

/**
 * Validates a prop value with detailed error reporting for anyOf failures
 *
 * This function provides enhanced error messages that show exactly which nested
 * properties failed validation in anyOf schemas, making debugging much easier.
 *
 * @param schema The PropType schema to validate against
 * @param value  The value to validate
 * @return Validation result with detailed error information
 */
const validatePropValueDetailed = (schema, value) => {
  const jsonSchema = (0,_props_to_llm_schema__WEBPACK_IMPORTED_MODULE_1__.propTypeToJsonSchema)(schema);
  const result = (0,jsonschema__WEBPACK_IMPORTED_MODULE_0__.validate)(value, jsonSchema);

  // Process all errors to add detailed anyOf information
  const detailedErrors = result.errors.map(processValidationError);
  return {
    valid: result.valid,
    errors: detailedErrors,
    errorMessages: detailedErrors.map(err => err.message),
    formattedErrors: formatDetailedErrors(detailedErrors),
    jsonSchema: JSON.stringify(jsonSchema)
  };
};

/***/ }),

/***/ "@elementor/schema":
/*!*****************************************!*\
  !*** external ["elementorV2","schema"] ***!
  \*****************************************/
/***/ (function(module) {

module.exports = window["elementorV2"]["schema"];

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
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
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
/*!**********************************************************!*\
  !*** ./packages/packages/libs/editor-props/src/index.ts ***!
  \**********************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CLASSES_PROP_KEY: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.CLASSES_PROP_KEY; },
/* harmony export */   DateTimePropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.DateTimePropTypeUtil; },
/* harmony export */   Schema: function() { return /* binding */ Schema; },
/* harmony export */   backdropFilterPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.backdropFilterPropTypeUtil; },
/* harmony export */   backgroundColorOverlayPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.backgroundColorOverlayPropTypeUtil; },
/* harmony export */   backgroundGradientOverlayPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.backgroundGradientOverlayPropTypeUtil; },
/* harmony export */   backgroundImageOverlayPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.backgroundImageOverlayPropTypeUtil; },
/* harmony export */   backgroundImagePositionOffsetPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.backgroundImagePositionOffsetPropTypeUtil; },
/* harmony export */   backgroundImageSizeScalePropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.backgroundImageSizeScalePropTypeUtil; },
/* harmony export */   backgroundOverlayItem: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.backgroundOverlayItem; },
/* harmony export */   backgroundOverlayPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.backgroundOverlayPropTypeUtil; },
/* harmony export */   backgroundPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.backgroundPropTypeUtil; },
/* harmony export */   blurFilterPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.blurFilterPropTypeUtil; },
/* harmony export */   booleanPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.booleanPropTypeUtil; },
/* harmony export */   borderRadiusPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.borderRadiusPropTypeUtil; },
/* harmony export */   borderWidthPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.borderWidthPropTypeUtil; },
/* harmony export */   boxShadowPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.boxShadowPropTypeUtil; },
/* harmony export */   classesPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.classesPropTypeUtil; },
/* harmony export */   colorPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.colorPropTypeUtil; },
/* harmony export */   colorStopPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.colorStopPropTypeUtil; },
/* harmony export */   colorToneFilterPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.colorToneFilterPropTypeUtil; },
/* harmony export */   createArrayPropUtils: function() { return /* reexport safe */ _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_6__.createArrayPropUtils; },
/* harmony export */   createPropUtils: function() { return /* reexport safe */ _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_6__.createPropUtils; },
/* harmony export */   cssFilterFunctionPropUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.cssFilterFunctionPropUtil; },
/* harmony export */   dateRangePropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.dateRangePropTypeUtil; },
/* harmony export */   dateStringPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.dateStringPropTypeUtil; },
/* harmony export */   dimensionsPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.dimensionsPropTypeUtil; },
/* harmony export */   dropShadowFilterPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.dropShadowFilterPropTypeUtil; },
/* harmony export */   emailPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.emailPropTypeUtil; },
/* harmony export */   emailsPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.emailsPropTypeUtil; },
/* harmony export */   evaluateTerm: function() { return /* reexport safe */ _utils_prop_dependency_utils__WEBPACK_IMPORTED_MODULE_11__.evaluateTerm; },
/* harmony export */   extractValue: function() { return /* reexport safe */ _utils_prop_dependency_utils__WEBPACK_IMPORTED_MODULE_11__.extractValue; },
/* harmony export */   filterEmptyValues: function() { return /* reexport safe */ _utils_filter_empty_values__WEBPACK_IMPORTED_MODULE_7__.filterEmptyValues; },
/* harmony export */   filterPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.filterPropTypeUtil; },
/* harmony export */   flexPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.flexPropTypeUtil; },
/* harmony export */   fontFamilyPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.fontFamilyPropTypeUtil; },
/* harmony export */   getPropSchemaFromCache: function() { return /* reexport safe */ _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_6__.getPropSchemaFromCache; },
/* harmony export */   gradientColorStopPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.gradientColorStopPropTypeUtil; },
/* harmony export */   gridTrackSizePropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.gridTrackSizePropTypeUtil; },
/* harmony export */   htmlPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.htmlPropTypeUtil; },
/* harmony export */   htmlV2PropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.htmlV2PropTypeUtil; },
/* harmony export */   htmlV3PropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.htmlV3PropTypeUtil; },
/* harmony export */   hueRotateFilterPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.hueRotateFilterPropTypeUtil; },
/* harmony export */   imageAttachmentIdPropType: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.imageAttachmentIdPropType; },
/* harmony export */   imagePropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.imagePropTypeUtil; },
/* harmony export */   imageSrcPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.imageSrcPropTypeUtil; },
/* harmony export */   intensityFilterPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.intensityFilterPropTypeUtil; },
/* harmony export */   isDependency: function() { return /* reexport safe */ _utils_prop_dependency_utils__WEBPACK_IMPORTED_MODULE_11__.isDependency; },
/* harmony export */   isDependencyMet: function() { return /* reexport safe */ _utils_prop_dependency_utils__WEBPACK_IMPORTED_MODULE_11__.isDependencyMet; },
/* harmony export */   isEmpty: function() { return /* reexport safe */ _utils_filter_empty_values__WEBPACK_IMPORTED_MODULE_7__.isEmpty; },
/* harmony export */   isOverridable: function() { return /* reexport safe */ _utils_is_overridable__WEBPACK_IMPORTED_MODULE_8__.isOverridable; },
/* harmony export */   isTransformable: function() { return /* reexport safe */ _utils_is_transformable__WEBPACK_IMPORTED_MODULE_9__.isTransformable; },
/* harmony export */   keyValuePropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.keyValuePropTypeUtil; },
/* harmony export */   layoutDirectionPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.layoutDirectionPropTypeUtil; },
/* harmony export */   linkPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.linkPropTypeUtil; },
/* harmony export */   mergeProps: function() { return /* reexport safe */ _utils_merge_props__WEBPACK_IMPORTED_MODULE_10__.mergeProps; },
/* harmony export */   moveTransformPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.moveTransformPropTypeUtil; },
/* harmony export */   numberPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.numberPropTypeUtil; },
/* harmony export */   numberRangePropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.numberRangePropTypeUtil; },
/* harmony export */   parseHtmlChildren: function() { return /* reexport safe */ _utils_parse_html_children__WEBPACK_IMPORTED_MODULE_12__.parseHtmlChildren; },
/* harmony export */   perspectiveOriginPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.perspectiveOriginPropTypeUtil; },
/* harmony export */   positionPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.positionPropTypeUtil; },
/* harmony export */   queryFilterArrayPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.queryFilterArrayPropTypeUtil; },
/* harmony export */   queryFilterPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.queryFilterPropTypeUtil; },
/* harmony export */   queryPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.queryPropTypeUtil; },
/* harmony export */   rewrapOverridableValue: function() { return /* reexport safe */ _utils_is_overridable__WEBPACK_IMPORTED_MODULE_8__.rewrapOverridableValue; },
/* harmony export */   rotateTransformPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.rotateTransformPropTypeUtil; },
/* harmony export */   scaleTransformPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.scaleTransformPropTypeUtil; },
/* harmony export */   selectionSizePropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.selectionSizePropTypeUtil; },
/* harmony export */   shadowPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.shadowPropTypeUtil; },
/* harmony export */   sizePropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.sizePropTypeUtil; },
/* harmony export */   skewTransformPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.skewTransformPropTypeUtil; },
/* harmony export */   spanPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.spanPropTypeUtil; },
/* harmony export */   stringArrayPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.stringArrayPropTypeUtil; },
/* harmony export */   stringPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.stringPropTypeUtil; },
/* harmony export */   strokePropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.strokePropTypeUtil; },
/* harmony export */   svgSrcPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.svgSrcPropTypeUtil; },
/* harmony export */   timeRangePropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.timeRangePropTypeUtil; },
/* harmony export */   timeStringPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.timeStringPropTypeUtil; },
/* harmony export */   transformFunctionsPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.transformFunctionsPropTypeUtil; },
/* harmony export */   transformOriginPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.transformOriginPropTypeUtil; },
/* harmony export */   transformPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.transformPropTypeUtil; },
/* harmony export */   urlPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.urlPropTypeUtil; },
/* harmony export */   videoAttachmentIdPropType: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.videoAttachmentIdPropType; },
/* harmony export */   videoSrcPropTypeUtil: function() { return /* reexport safe */ _prop_types__WEBPACK_IMPORTED_MODULE_5__.videoSrcPropTypeUtil; }
/* harmony export */ });
/* harmony import */ var _utils_adjust_llm_prop_value_schema__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils/adjust-llm-prop-value-schema */ "./packages/packages/libs/editor-props/src/utils/adjust-llm-prop-value-schema.ts");
/* harmony import */ var _utils_llm_schema_to_props__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils/llm-schema-to-props */ "./packages/packages/libs/editor-props/src/utils/llm-schema-to-props.ts");
/* harmony import */ var _utils_props_to_llm_schema__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./utils/props-to-llm-schema */ "./packages/packages/libs/editor-props/src/utils/props-to-llm-schema.ts");
/* harmony import */ var _utils_validate_prop_value__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./utils/validate-prop-value */ "./packages/packages/libs/editor-props/src/utils/validate-prop-value.ts");
/* harmony import */ var _types__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./types */ "./packages/packages/libs/editor-props/src/types.ts");
/* harmony import */ var _prop_types__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./prop-types */ "./packages/packages/libs/editor-props/src/prop-types/index.ts");
/* harmony import */ var _utils_create_prop_utils__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./utils/create-prop-utils */ "./packages/packages/libs/editor-props/src/utils/create-prop-utils.ts");
/* harmony import */ var _utils_filter_empty_values__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./utils/filter-empty-values */ "./packages/packages/libs/editor-props/src/utils/filter-empty-values.ts");
/* harmony import */ var _utils_is_overridable__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./utils/is-overridable */ "./packages/packages/libs/editor-props/src/utils/is-overridable.ts");
/* harmony import */ var _utils_is_transformable__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./utils/is-transformable */ "./packages/packages/libs/editor-props/src/utils/is-transformable.ts");
/* harmony import */ var _utils_merge_props__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./utils/merge-props */ "./packages/packages/libs/editor-props/src/utils/merge-props.ts");
/* harmony import */ var _utils_prop_dependency_utils__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./utils/prop-dependency-utils */ "./packages/packages/libs/editor-props/src/utils/prop-dependency-utils.ts");
/* harmony import */ var _utils_parse_html_children__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./utils/parse-html-children */ "./packages/packages/libs/editor-props/src/utils/parse-html-children.ts");




// types

// prop types


// utils







const Schema = {
  jsonSchemaToPropType: _utils_llm_schema_to_props__WEBPACK_IMPORTED_MODULE_1__.jsonSchemaToPropType,
  propTypeToJsonSchema: _utils_props_to_llm_schema__WEBPACK_IMPORTED_MODULE_2__.propTypeToJsonSchema,
  adjustLlmPropValueSchema: _utils_adjust_llm_prop_value_schema__WEBPACK_IMPORTED_MODULE_0__.adjustLlmPropValueSchema,
  isPropKeyConfigurable: _utils_props_to_llm_schema__WEBPACK_IMPORTED_MODULE_2__.isPropKeyConfigurable,
  nonConfigurablePropKeys: _utils_props_to_llm_schema__WEBPACK_IMPORTED_MODULE_2__.nonConfigurablePropKeys,
  configurableKeys: _utils_props_to_llm_schema__WEBPACK_IMPORTED_MODULE_2__.configurableKeys,
  validatePropValue: _utils_validate_prop_value__WEBPACK_IMPORTED_MODULE_3__.validatePropValue,
  enrichWithIntention: _utils_props_to_llm_schema__WEBPACK_IMPORTED_MODULE_2__.enrichWithIntention,
  removeIntention: _utils_props_to_llm_schema__WEBPACK_IMPORTED_MODULE_2__.removeIntention,
  setDynamicTagNamesResolver: _utils_props_to_llm_schema__WEBPACK_IMPORTED_MODULE_2__.setDynamicTagNamesResolver
};
}();
(window.elementorV2 = window.elementorV2 || {}).editorProps = __webpack_exports__;
/******/ })()
;
window.elementorV2.editorProps?.init?.();
//# sourceMappingURL=editor-props.js.map
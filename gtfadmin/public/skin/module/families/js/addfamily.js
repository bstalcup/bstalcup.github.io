$.formUtils.addValidator({
	name : 'FirstNameValid',
	validatorFunction : function(value, $el, config, language, $form) {
		//var usernameRegex = '/^([a-zA-Z&]+)$/';
		return value.match(/^([a-zA-Z&\s]+)$/);
	},
	errorMessage : 'The answer you gave must contain only characters and &',
	errorMessageKey: 'badEvenNumber'
}); 

$.formUtils.addValidator({
	name : 'LastNameValid',
	validatorFunction : function(value, $el, config, language, $form) {
		return value.match(/^([a-zA-Z-\s]+)$/);
	},
	errorMessage : 'The answer you gave must contain only characters and -',
	errorMessageKey: 'badEvenNumber'
});

$.formUtils.addValidator({
	name : 'PhoneNumberValid',
	validatorFunction : function(value, $el, config, language, $form) {
		return value.match(/^([0-9-()\s]+)$/);
	},
	errorMessage : 'The answer you gave must contain only numbers and - or ( )',
	errorMessageKey: 'badEvenNumber'
}); 

$.validate({
  form : '.validate-me'
});
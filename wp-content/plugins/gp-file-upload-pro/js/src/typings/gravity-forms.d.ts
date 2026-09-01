interface GravityFormsField {
	inputs: any
	choices: any
	id: string
	type: string
	inputType: string
}

interface GFFile {
	uploaded_filename: string
	temp_filename: string
	/* Gravity Forms includes the Plupload file ID in the uploaded file meta. */
	id?: string
}

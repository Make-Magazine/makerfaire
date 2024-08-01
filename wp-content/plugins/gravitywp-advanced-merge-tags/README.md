# gravitywp-advanced-merge-tags

## Basic Merge Tags<br>
### {gwp_current_timestamp} -> returns the current timestamp

## Merge Tags with attributes
### gwp_parent_slug

-> Returns the slug of the parent page/post. A depth paramater can be added like this:

{gwp_parent_slug depth="1"}

-> Returns the slug of the parent of the parent page.

{gwp_parent_slug depth="top"}

-> Returns the highest parent page slug.

### gwp_date_created / gwp_date_updated
This mergetag returns a modified date time relative to the date time when the entry was created in the desired format. 

Paramaters:
- modify (optional): For available date time modifications, see https://www.php.net/manual/en/datetime.formats.relative.php.
- format (optional, default ='Y-m-d H:i:s'): For an explanation of available format options, see https://www.php.net/manual/en/datetime.format.php. Regular character can be escaped using '\'. For example: 'd/m/Y\ \a\t\ H:i:s'.
- timezone (optional, default is Wordpress timezone setting): For available timezones, see https://www.php.net/manual/en/timezones.php.

Examples:

{gwp_date_created modify="+1 day" format="d-m-Y H:i:s"}
-> When the entry is created on 2021-03-30 12:21:23 this returns '31-03-2021 12:21:23'

{gwp_date_created modify="-1 day" format="d-m-Y"}
-> When the entry is created on 2021-03-30 12:21:23 this returns '29-03-2021'

{gwp_date_updated modify="first day of next month" format="d-m-Y"}
-> When the entry is updated on 2021-03-30 12:21:23 this returns '01-04-2021'

{gwp_date_updated format="d/m/Y\ \a\t\ H:i:s"}
-> When the entry is updated on 2021-03-30 12:21:23 this returns '30-03-2021 at 12:21:23'

### gwp_date_field
This mergetag returns a modified date time relative to the date time of a field-value with a date/time string in the desired format. 
Parameters:
- id (required): ID of the field containing a date time value.
- modify (optional): For available date time modifications, see https://www.php.net/manual/en/datetime.formats.relative.php.
- field_format (optional for date fields, required for other fieldtypes): For an explanation of available format options, see https://www.php.net/manual/en/datetime.format.php. Regular character can be escaped using '\'. For example: 'd/m/Y\ \a\t\ H:i:s'.
- format (optional, default ='Y-m-d H:i:s').
- field_timezone (optional, default is WordPress timezone setting).
- timezone (optional, default is WordPress timezone setting): For available timezones, see https://www.php.net/manual/en/timezones.php.

Example:
{gwp_date_field id=5 modify="first day of next month" format="d-m-Y"} where field 5 is a date field.

{gwp_date_field id=1 field_format="Y-m-d \a\t\ H:i:s" field_timezone='UTC' format="d/m/Y\ \a\t\ H:i:s" timezone='Europe/Amsterdam'} where field 1 is a textfield


## gwp_generate_token
This mergetag generates a token with the option to check if the token is unique. 
Parameters:
- charset (optional): By default charset contains all alphanumeric characters, upper and lowercase. You can pass in your own custom character set.
- length (optional): The character length of the token. Default length is 16. Minimum length is 8. Comma or dotted seperated numbers are converted to integers.
- unique (optional): Checks if the generated token is unique. False by default.

Example: 
{gwp_generate_token charset="ABCDEFG1234567890" length="20" unique="true"}

## Merge Tag modifiers with attributes
#### gwp_get_matched_entry_value
This modifier can be used to retrieve values from entries from another form, based on a shared value.

Examples:
{Textfield:1:gwp_get_matched_entry_value form_id="2" match_id='1' return_id=2}

-> Searches in Form 2 for entries with the same value as Textfield in Field 1. Returns the value in Field 2 from the newest entry.

{Textfield:1:gwp_get_matched_entry_value form_id="2" match_id='1' return_id=2 sort_order=asc}

-> with different sort_order: returns the oldest entry value.

{Textfield:1:gwp_get_matched_entry_value form_id="2" match_id='1' return_id=2 sort_order=asc offset=1}

-> with offset=1: returns the second oldest entry value.<br>

#### gwp_count_matched_entries
This modifier can be used to count entries from another form, based on a shared value with optional extra filters.

Supported operators: 'is', 'isnot', 'contains', 'greater_than', 'less_than', 'greater_than_or_is', 'less_than_or_is'.

Example:
{Textfield:1:gwp_count_matched_entries form_id="2" match_id='1' filter1="3" operator1="isnot" value1=complete filter2="created_by" operator2="is" value2=1}

-> Matches all entries in Form 2 where Field 1 has the same value as Textfield:1. Entries where Field 3 value is not 'complete' AND where created_by is not User 1 are filtered out. The total count of matching entries is returned.


## Privacy Policy
GravityWP - Advanced Merge Tags uses [Appsero](https://appsero.com) SDK to collect some telemetry data upon user's confirmation. This helps us to troubleshoot problems faster & make product improvements.

Appsero SDK **does not gather any data by default.** The SDK only starts gathering basic telemetry data **when a user allows it via the admin notice**. We collect the data to ensure a great user experience for all our users.

Integrating Appsero SDK **DOES NOT IMMEDIATELY** start gathering data, **without confirmation from users in any case.**

Learn more about how [Appsero collects and uses this data](https://appsero.com/privacy-policy/).

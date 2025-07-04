# Multi-Value Dependencies Implementation Summary

## Changes Made to QuestionnaireImportExportController.php

### 1. Template Generation (`downloadTemplate`)
- **Header update**: Changed column R header from "Depends On Value" to "Depends On Values (| separated for multiple values)"
- **Example update**: Updated example data to show multiple values: "Sangat Baik|Baik"

### 2. Import Functionality (`import`)
- **Variable rename**: Changed `$dependsOnValue` to `$dependsOnValues` for clarity
- **Multi-value parsing**: Added support for pipe-separated values using `explode('|', $dependsOnValues)`
- **Loop processing**: Added foreach loop to process each dependency value individually
- **Option mapping**: Enhanced option text-to-ID mapping for each dependency value
- **Storage format**: Store as comma-separated string using `implode(',', $dependsValueIds)`
- **Enhanced logging**: Added detailed logging for debugging dependency processing
- **Error handling**: Improved error handling for unmatched dependency values

### 3. Export Functionality (`export`)
- **Variable rename**: Changed `$dependsValue` to `$dependsValues` for clarity
- **Multi-value extraction**: Added support for comma-separated values from database using `explode(',', $question->depends_value)`
- **ID-to-text mapping**: Enhanced option ID-to-text mapping for each dependency value
- **Export format**: Join multiple values with pipe separator using `implode('|', $dependsValueTexts)`
- **Enhanced logging**: Added detailed logging for debugging export process
- **Header update**: Updated export headers to reflect multi-value support

### 4. Technical Details

#### Database Storage Format:
- **Single dependency**: `"123"` (single option ID)
- **Multiple dependencies**: `"123,124,125"` (comma-separated option IDs)

#### Export/Import File Format:
- **Single dependency**: `"Sangat Baik"` (single option text)
- **Multiple dependencies**: `"Sangat Baik|Baik|Cukup"` (pipe-separated option texts)

#### Processing Flow:
1. **Import**: Text values → Option IDs → Comma-separated string → Database
2. **Export**: Database → Comma-separated string → Option IDs → Text values → Pipe-separated string

### 5. Compatibility
- **Backward compatible**: Single dependency values still work as before
- **Forward compatible**: Multi-value dependencies are fully supported
- **Error handling**: Graceful handling of malformed or missing dependency values

### 6. Testing Recommendations
1. Test export of existing questionnaires with single dependencies
2. Test export of questionnaires with multiple dependencies
3. Test import of files with single dependency values
4. Test import of files with multiple dependency values
5. Verify database storage format matches expected comma-separated format
6. Verify dependency logic works correctly in questionnaire responses

### 7. Example Usage

#### Template Example:
```
Depends On Question: "Bagaimana pengalaman Anda?"
Depends On Values: "Sangat Baik|Baik|Cukup"
```

#### Database Result:
```
depends_on: 15 (question ID)
depends_value: "123,124,125" (comma-separated option IDs)
```

This implementation fully supports multi-value dependencies while maintaining backward compatibility with existing single-value dependencies.

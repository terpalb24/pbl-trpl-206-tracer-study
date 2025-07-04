## Multi-Value Dependencies - Import/Export Test

### Test Cases:

1. **Single dependency value**: "Sangat Baik"
2. **Multiple dependency values**: "Sangat Baik|Baik|Cukup"
3. **Mixed dependencies**: Some questions with single values, some with multiple

### Export Format:
- Single: "Sangat Baik"
- Multiple: "Sangat Baik|Baik|Cukup"

### Import Format:
- Single: "Sangat Baik"
- Multiple: "Sangat Baik|Baik|Cukup"

### Database Storage:
- Single: "123" (option ID)
- Multiple: "123,124,125" (comma-separated option IDs)

### Changes Made:

1. **Template Generation** (`downloadTemplate`):
   - Updated header from "Depends On Value" to "Depends On Values (| separated for multiple values)"
   - Updated example to show multiple values: "Sangat Baik|Baik"

2. **Import Function** (`import`):
   - Changed variable from `$dependsOnValue` to `$dependsOnValues`
   - Added pipe-separated value parsing: `explode('|', $dependsOnValues)`
   - Added loop to process each dependency value
   - Store as comma-separated string: `implode(',', $dependsValueIds)`

3. **Export Function** (`export`):
   - Changed variable from `$dependsValue` to `$dependsValues`
   - Added support for comma-separated dependency values from database
   - Convert back to pipe-separated format for export: `implode('|', $dependsValueTexts)`

4. **Header Updates**:
   - Updated column headers to reflect multi-value support
   - Extended range from Q1 to R1 for proper header reading

### Testing:
1. Export existing questionnaire with dependencies
2. Verify multi-value format in exported file
3. Import modified file with multi-value dependencies
4. Verify proper parsing and storage in database

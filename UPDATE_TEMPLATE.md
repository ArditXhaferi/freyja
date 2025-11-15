# How to Update template.docx with Placeholders

## Quick Guide

1. **Open template.docx** in Microsoft Word or LibreOffice Writer
2. **Find sections** where you want dynamic content to appear
3. **Type placeholders** in this exact format: `${placeholder_name}`
4. **Save** the document

## Step-by-Step Instructions

### For Microsoft Word:

1. Open `template.docx` in Microsoft Word
2. Navigate to the section where you want to insert dynamic content
3. Type the placeholder exactly as: `${business_name}` (don't add spaces)
4. Repeat for other placeholders where needed
5. Save the document (Ctrl+S / Cmd+S)

### For LibreOffice Writer:

1. Open `template.docx` in LibreOffice Writer
2. Navigate to the section where you want to insert dynamic content
3. Type the placeholder exactly as: `${business_name}`
4. Repeat for other placeholders where needed
5. Save the document (Ctrl+S / Cmd+S)

## Example Template Structure

Here's an example of how your template.docx might look:

```
BUSINESS PLAN

Company Information
===================

Company Name: ${business_name}
Company Type: ${company_type}
Industry: ${industry}
Business ID: ${business_id}

Contact Information
===================

Address: ${address}
ZIP Code: ${zip_code}
Postal District: ${postal_district}
Website: ${internet_address}
Contact: ${company_contact_info}

Business Overview
=================

Business Idea:
${business_idea}

Target Market:
${target_market_groups}

Products and Services:
${products_services_general}

Vision and Long-term Goals:
${vision_long_term}

Generated on: ${generation_date}
```

## Verify Placeholders

After updating your template, run the verification script:

```bash
php verify-template-placeholders.php
```

This will:
- ✅ Show all placeholders found in your template
- ⚠️  Show missing placeholders (available but not in template)
- ⚠️  Show unexpected placeholders (in template but not recognized)

## Common Placeholders

Most commonly used placeholders:

- `${business_name}` - Business name
- `${business_idea}` - Business idea summary
- `${target_market_groups}` - Target customers
- `${company_planned_name}` - Planned company name
- `${industry}` - Industry sector
- `${address}` - Business address
- `${generation_date}` - Date PDF was generated

## Important Notes

1. **Format is critical**: Use `${placeholder_name}` exactly (dollar sign, curly braces)
2. **No spaces**: Don't add spaces inside `${}` 
3. **Case sensitive**: Use exactly as listed in TEMPLATE_PLACEHOLDERS.md
4. **Single word blocks**: Keep each placeholder as a single text block (don't split across lines)

## Full List of Placeholders

See `TEMPLATE_PLACEHOLDERS.md` for the complete list of all available placeholders.


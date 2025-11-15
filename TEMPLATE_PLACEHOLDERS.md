# Business Plan Template Placeholders

This document lists all available placeholders that can be used in `template.docx`.

## How to Use Placeholders in template.docx

1. Open `template.docx` in Microsoft Word or LibreOffice Writer
2. Where you want to insert dynamic content, type placeholders in this exact format: `${placeholder_name}`
3. Make sure each placeholder is a single word/text block (not split across lines)
4. The placeholders will be automatically replaced with user data when generating the PDF

## Available Placeholders

### User Information
- `${name}` - User's full name
- `${generation_date}` - Date when PDF was generated (e.g., "January 15, 2025")

### Business Basic Information
- `${business_name}` - Business name
- `${company_planned_name}` - Planned company name
- `${company_type}` - Type of company
- `${industry}` - Industry sector
- `${business_id}` - Business ID number
- `${year_of_establishment}` - Year business was established
- `${number_of_employees}` - Number of employees

### Contact Information
- `${address}` - Business address
- `${zip_code}` - ZIP/Postal code
- `${postal_district}` - Postal district
- `${internet_address}` - Website URL
- `${company_contact_info}` - Company contact information

### Business Details
- `${business_idea}` - Business idea summary
- `${competence_skills}` - Competence and skills
- `${company_owners_holdings}` - Company owners and their holdings

### Products & Services
- `${products_services_general}` - General description of products/services
- `${products_services_detailed}` - Detailed products/services information

### Market & Competition
- `${target_market_groups}` - Target market groups
- `${competitors}` - Competitor information
- `${competitive_situation}` - Competitive situation analysis

### Operations
- `${sales_marketing}` - Sales and marketing strategy
- `${production_logistics}` - Production and logistics
- `${distribution_network}` - Distribution network

### Strategic Information
- `${swot_analysis}` - SWOT analysis
- `${vision_long_term}` - Long-term vision
- `${industry_future_prospects}` - Industry future prospects

### Risk & Legal
- `${operating_environment_risks}` - Operating environment and risks
- `${third_parties_partners}` - Third parties and partners
- `${permits_notices}` - Permits and notices
- `${insurance_contracts}` - Insurance contracts
- `${intellectual_property_rights}` - Intellectual property rights
- `${support_network}` - Support network

### Additional Information
- `${my_business_comprehensive}` - Comprehensive business description
- `${country_of_origin}` - Country of origin

## Example Template Content

```
BUSINESS PLAN

Company Name: ${business_name}
Company Type: ${company_type}
Industry: ${industry}

Date Generated: ${generation_date}

BUSINESS IDEA
${business_idea}

TARGET MARKET
${target_market_groups}

COMPETITORS
${competitors}

PRODUCTS AND SERVICES
${products_services_general}

VISION
${vision_long_term}

CONTACT INFORMATION
Address: ${address}
ZIP Code: ${zip_code}
Website: ${internet_address}
```

## Important Notes

1. **Placeholder Format**: Always use `${placeholder_name}` format (with dollar sign and curly braces)
2. **Case Sensitive**: Placeholders are case-sensitive. Use exactly as listed above
3. **Empty Values**: If a placeholder has no value, it will be replaced with an empty string
4. **Special Characters**: Placeholders with spaces or special characters use underscores (e.g., `company_planned_name`)
5. **Arrays/Lists**: Array values will be automatically converted to comma-separated strings

## Testing Placeholders

After updating your template.docx:
1. Ensure the file is saved in the project root directory
2. Test the PDF generation from the application
3. Check the Laravel logs for any placeholder replacement errors
4. Verify all placeholders are replaced correctly in the generated PDF


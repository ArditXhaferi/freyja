<?php

/**
 * Helper script to verify placeholders in template.docx
 * 
 * Usage: php verify-template-placeholders.php
 */

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;

$templatePath = __DIR__ . '/template.docx';

if (!file_exists($templatePath)) {
    echo "❌ Error: template.docx not found at: {$templatePath}\n";
    exit(1);
}

echo "📄 Checking template: {$templatePath}\n\n";

try {
    // Load the template using TemplateProcessor to find placeholders
    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);
    
    // Get all variables from the template
    $variables = $templateProcessor->getVariables();
    
    $placeholderPattern = '/\$\{([^}]+)\}/';
    $foundPlaceholders = [];
    
    // Extract placeholders from variables list
    foreach ($variables as $variable) {
        // PHPWord returns variable names without ${}, so we add them
        $placeholder = '${' . $variable . '}';
        $foundPlaceholders[] = $placeholder;
    }
    
    // Also try to find placeholders directly in the document XML
    // This is a fallback in case TemplateProcessor doesn't catch all
    $zip = new \ZipArchive();
    if ($zip->open($templatePath) === true) {
        $documentXml = $zip->getFromName('word/document.xml');
        if ($documentXml !== false && preg_match_all($placeholderPattern, $documentXml, $matches)) {
            foreach ($matches[0] as $placeholder) {
                if (!in_array($placeholder, $foundPlaceholders)) {
                    $foundPlaceholders[] = $placeholder;
                }
            }
        }
        $zip->close();
    }
    
    // Expected placeholders from the controller
    $expectedPlaceholders = [
        '${name}',
        '${business_name}',
        '${company_planned_name}',
        '${company_type}',
        '${industry}',
        '${address}',
        '${zip_code}',
        '${postal_district}',
        '${internet_address}',
        '${business_id}',
        '${year_of_establishment}',
        '${number_of_employees}',
        '${company_contact_info}',
        '${company_owners_holdings}',
        '${business_idea}',
        '${competence_skills}',
        '${swot_analysis}',
        '${products_services_general}',
        '${products_services_detailed}',
        '${sales_marketing}',
        '${production_logistics}',
        '${distribution_network}',
        '${target_market_groups}',
        '${competitors}',
        '${competitive_situation}',
        '${third_parties_partners}',
        '${operating_environment_risks}',
        '${vision_long_term}',
        '${industry_future_prospects}',
        '${permits_notices}',
        '${insurance_contracts}',
        '${intellectual_property_rights}',
        '${support_network}',
        '${my_business_comprehensive}',
        '${country_of_origin}',
        '${generation_date}',
    ];
    
    if (empty($foundPlaceholders)) {
        echo "⚠️  No placeholders found in the template!\n";
        echo "\n📝 You need to add placeholders to your template.docx file.\n";
        echo "   Format: \${placeholder_name}\n";
        echo "   Example: \${business_name}\n";
        echo "\n📖 See TEMPLATE_PLACEHOLDERS.md for a complete list.\n";
    } else {
        echo "✅ Found " . count($foundPlaceholders) . " placeholder(s) in template:\n\n";
        foreach ($foundPlaceholders as $placeholder) {
            $status = in_array($placeholder, $expectedPlaceholders) ? '✅' : '⚠️';
            echo "   {$status} {$placeholder}\n";
        }
        
        echo "\n📋 Missing placeholders (available but not in template):\n\n";
        $missing = array_diff($expectedPlaceholders, $foundPlaceholders);
        if (empty($missing)) {
            echo "   ✨ All expected placeholders are present!\n";
        } else {
            foreach ($missing as $placeholder) {
                echo "   ⚠️  {$placeholder}\n";
            }
        }
        
        echo "\n⚠️  Unexpected placeholders (in template but not recognized):\n\n";
        $unexpected = array_diff($foundPlaceholders, $expectedPlaceholders);
        if (empty($unexpected)) {
            echo "   ✨ No unexpected placeholders found!\n";
        } else {
            foreach ($unexpected as $placeholder) {
                echo "   ⚠️  {$placeholder}\n";
            }
        }
    }
    
    echo "\n";
    
} catch (\Exception $e) {
    echo "❌ Error reading template: " . $e->getMessage() . "\n";
    exit(1);
}


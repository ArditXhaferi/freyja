<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;

class BusinessPlanController extends Controller
{
    /**
     * Get the current user's business plan data
     */
    public function show(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Return all business plan fields including contextual information
        return response()->json([
            // Contextual fields (for personalized roadmap)
            'country_of_origin' => $user->country_of_origin,
            'is_eu_resident' => $user->is_eu_resident,
            'is_newcomer_to_finland' => $user->is_newcomer_to_finland,
            'has_residence_permit' => $user->has_residence_permit,
            'residence_permit_type' => $user->residence_permit_type,
            'years_in_finland' => $user->years_in_finland,
            'has_business_experience' => $user->has_business_experience,
            'language' => $user->language,
            // Advisor fields
            'specialization' => $user->specialization,
            'title' => $user->title,
            'bio' => $user->bio,
            'languages' => $user->languages,
            // Business plan fields
            'business_name' => $user->business_name,
            'company_contact_info' => $user->company_contact_info,
            'industry' => $user->industry,
            'company_planned_name' => $user->company_planned_name,
            'company_type' => $user->company_type,
            'address' => $user->address,
            'zip_code' => $user->zip_code,
            'postal_district' => $user->postal_district,
            'year_of_establishment' => $user->year_of_establishment,
            'number_of_employees' => $user->number_of_employees,
            'internet_address' => $user->internet_address,
            'business_id' => $user->business_id,
            'company_owners_holdings' => $user->company_owners_holdings,
            'business_idea' => $user->business_idea,
            'competence_skills' => $user->competence_skills,
            'swot_analysis' => $user->swot_analysis,
            'products_services_general' => $user->products_services_general,
            'products_services_detailed' => $user->products_services_detailed,
            'sales_marketing' => $user->sales_marketing,
            'production_logistics' => $user->production_logistics,
            'distribution_network' => $user->distribution_network,
            'target_market_groups' => $user->target_market_groups,
            'competitors' => $user->competitors,
            'competitive_situation' => $user->competitive_situation,
            'third_parties_partners' => $user->third_parties_partners,
            'operating_environment_risks' => $user->operating_environment_risks,
            'vision_long_term' => $user->vision_long_term,
            'industry_future_prospects' => $user->industry_future_prospects,
            'permits_notices' => $user->permits_notices,
            'insurance_contracts' => $user->insurance_contracts,
            'intellectual_property_rights' => $user->intellectual_property_rights,
            'support_network' => $user->support_network,
            'my_business_comprehensive' => $user->my_business_comprehensive,
        ], 200);
    }

    /**
     * Update the user's business plan data (accepts partial updates)
     */
    public function update(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Log incoming request data for debugging
        Log::info('BusinessPlanController::update - Incoming request data', [
            'all' => $request->all(),
            'has_residence_permit' => $request->input('has_residence_permit'),
            'residence_permit_type' => $request->input('residence_permit_type'),
            'has_residence_permit_type' => gettype($request->input('has_residence_permit')),
        ]);

        // Normalize boolean values from request (handle strings like "true", "false", "1", "0")
        $normalizedData = $request->all();
        $booleanFields = ['is_eu_resident', 'is_newcomer_to_finland', 'has_residence_permit', 'has_business_experience'];
        foreach ($booleanFields as $field) {
            if (isset($normalizedData[$field])) {
                $value = $normalizedData[$field];
                if (is_string($value)) {
                    $normalizedData[$field] = in_array(strtolower($value), ['true', '1', 'yes', 'on']) ? true : 
                                             (in_array(strtolower($value), ['false', '0', 'no', 'off']) ? false : null);
                } elseif ($value === null) {
                    // Keep null as null
                    $normalizedData[$field] = null;
                }
            }
        }
        
        // Merge normalized data back into request
        $request->merge($normalizedData);

        // Validate all possible business plan fields (all optional for partial updates)
        $validated = $request->validate([
            // Contextual fields
            'country_of_origin' => 'sometimes|nullable|string|max:255',
            'is_eu_resident' => 'sometimes|nullable|boolean',
            'is_newcomer_to_finland' => 'sometimes|nullable|boolean',
            'has_residence_permit' => 'sometimes|nullable|boolean',
            'residence_permit_type' => 'sometimes|nullable|string|max:255',
            'years_in_finland' => 'sometimes|nullable|integer|min:0',
            'has_business_experience' => 'sometimes|nullable|boolean',
            'language' => 'sometimes|nullable|string|max:10',
            // Advisor fields
            'specialization' => 'sometimes|nullable|string|max:255',
            'title' => 'sometimes|nullable|string|max:255',
            'bio' => 'sometimes|nullable|string',
            'languages' => 'sometimes|nullable|array',
            // Business plan fields
            'business_name' => 'sometimes|string|max:255',
            'company_contact_info' => 'sometimes|string',
            'industry' => 'sometimes|string|max:255',
            'company_planned_name' => 'sometimes|string|max:255',
            'company_type' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:255',
            'zip_code' => 'sometimes|string|max:20',
            'postal_district' => 'sometimes|string|max:255',
            'year_of_establishment' => 'sometimes|nullable|integer|min:1900|max:' . date('Y'),
            'number_of_employees' => 'sometimes|integer|min:0',
            'internet_address' => 'sometimes|string|max:255',
            'business_id' => 'sometimes|string|max:255',
            'company_owners_holdings' => 'sometimes|string',
            'business_idea' => 'sometimes|string',
            'competence_skills' => 'sometimes|string',
            'swot_analysis' => 'sometimes|array',
            'products_services_general' => 'sometimes|string',
            'products_services_detailed' => 'sometimes|array',
            'sales_marketing' => 'sometimes|string',
            'production_logistics' => 'sometimes|string',
            'distribution_network' => 'sometimes|string',
            'target_market_groups' => 'sometimes|string',
            'competitors' => 'sometimes|string',
            'competitive_situation' => 'sometimes|string',
            'third_parties_partners' => 'sometimes|string',
            'operating_environment_risks' => 'sometimes|string',
            'vision_long_term' => 'sometimes|string',
            'industry_future_prospects' => 'sometimes|string',
            'permits_notices' => 'sometimes|string',
            'insurance_contracts' => 'sometimes|string',
            'intellectual_property_rights' => 'sometimes|string',
            'support_network' => 'sometimes|string',
            'my_business_comprehensive' => 'sometimes|string',
        ]);

        // Log validated data for debugging
        Log::info('BusinessPlanController::update - Validated data', [
            'validated' => $validated,
            'has_residence_permit' => $validated['has_residence_permit'] ?? 'NOT IN VALIDATED',
            'residence_permit_type' => $validated['residence_permit_type'] ?? 'NOT IN VALIDATED',
        ]);

        // Update only the fields that were provided
        $user->fill($validated);
        $user->save();

        // Log saved data for debugging
        Log::info('BusinessPlanController::update - Saved user data', [
            'has_residence_permit' => $user->has_residence_permit,
            'residence_permit_type' => $user->residence_permit_type,
        ]);

        return response()->json([
            'message' => 'Business plan updated successfully',
            'business_plan' => [
                // Contextual fields
                'country_of_origin' => $user->country_of_origin,
                'is_eu_resident' => $user->is_eu_resident,
                'is_newcomer_to_finland' => $user->is_newcomer_to_finland,
                'has_residence_permit' => $user->has_residence_permit,
                'residence_permit_type' => $user->residence_permit_type,
                'years_in_finland' => $user->years_in_finland,
                'has_business_experience' => $user->has_business_experience,
                'language' => $user->language,
                // Advisor fields
                'specialization' => $user->specialization,
                'title' => $user->title,
                'bio' => $user->bio,
                'languages' => $user->languages,
                // Business plan fields
                'business_name' => $user->business_name,
                'company_contact_info' => $user->company_contact_info,
                'industry' => $user->industry,
                'company_planned_name' => $user->company_planned_name,
                'company_type' => $user->company_type,
                'address' => $user->address,
                'zip_code' => $user->zip_code,
                'postal_district' => $user->postal_district,
                'year_of_establishment' => $user->year_of_establishment,
                'number_of_employees' => $user->number_of_employees,
                'internet_address' => $user->internet_address,
                'business_id' => $user->business_id,
                'company_owners_holdings' => $user->company_owners_holdings,
                'business_idea' => $user->business_idea,
                'competence_skills' => $user->competence_skills,
                'swot_analysis' => $user->swot_analysis,
                'products_services_general' => $user->products_services_general,
                'products_services_detailed' => $user->products_services_detailed,
                'sales_marketing' => $user->sales_marketing,
                'production_logistics' => $user->production_logistics,
                'distribution_network' => $user->distribution_network,
                'target_market_groups' => $user->target_market_groups,
                'competitors' => $user->competitors,
                'competitive_situation' => $user->competitive_situation,
                'third_parties_partners' => $user->third_parties_partners,
                'operating_environment_risks' => $user->operating_environment_risks,
                'vision_long_term' => $user->vision_long_term,
                'industry_future_prospects' => $user->industry_future_prospects,
                'permits_notices' => $user->permits_notices,
                'insurance_contracts' => $user->insurance_contracts,
                'intellectual_property_rights' => $user->intellectual_property_rights,
                'support_network' => $user->support_network,
                'my_business_comprehensive' => $user->my_business_comprehensive,
            ]
        ], 200);
    }

    /**
     * Generate business plan PDF from template.docx
     */
    public function generatePdf(): JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $templatePath = base_path('template.docx');
            
            if (!file_exists($templatePath)) {
                return response()->json(['error' => 'Template file not found'], 404);
            }

            // Create template processor
            $templateProcessor = new TemplateProcessor($templatePath);

            // Prepare data for template (map user fields to template placeholders)
            $data = $this->prepareTemplateData($user);

            // Replace placeholders in template
            // PHPWord setValue expects just the variable name without ${} wrapper
            // The template should have placeholders like: ${variable}
            $replacedCount = 0;
            foreach ($data as $key => $value) {
                // Remove ${} wrapper if present from our key format
                $placeholder = preg_replace('/^\$\{|\}$/', '', $key);
                try {
                    // Replace with empty string if value is empty/null
                    $replacementValue = $value ?? '';
                    $templateProcessor->setValue($placeholder, $replacementValue);
                    $replacedCount++;
                } catch (\Exception $e) {
                    // Placeholder might not exist in template, log but continue
                    Log::debug("Placeholder '{$placeholder}' not found in template or error replacing: " . $e->getMessage());
                }
            }
            
            Log::info("Business plan PDF generation", [
                'user_id' => $user->id,
                'placeholders_replaced' => $replacedCount,
                'total_placeholders' => count($data),
            ]);

            // Save filled template to temporary file
            $tempDocxPath = storage_path('app/temp/business-plan-' . $user->id . '-' . time() . '.docx');
            $tempDir = dirname($tempDocxPath);
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            $templateProcessor->saveAs($tempDocxPath);

            // Convert DOCX to PDF
            $pdfPath = $this->convertDocxToPdf($tempDocxPath);

            // Clean up temporary DOCX file
            if (file_exists($tempDocxPath)) {
                unlink($tempDocxPath);
            }

            // Return PDF file
            // Sanitize filename to avoid special characters
            $businessName = $user->business_name ?? 'my-business';
            $businessName = preg_replace('/[^a-zA-Z0-9_-]/', '-', $businessName);
            $businessName = preg_replace('/-+/', '-', $businessName); // Replace multiple dashes with single
            $filename = 'business-plan-' . $businessName . '-' . date('Y-m-d') . '.pdf';
            
            return response()->download($pdfPath, $filename, [
                'Content-Type' => 'application/pdf',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            Log::error('Error generating business plan PDF: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => $user->id ?? null,
            ]);

            return response()->json([
                'error' => 'Failed to generate business plan PDF',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prepare user data for template placeholders
     */
    private function prepareTemplateData($user): array
    {
        // Helper function to format array/object to string
        $formatValue = function ($value) {
            if (is_null($value)) {
                return '';
            }
            if (is_array($value)) {
                return implode(', ', array_filter($value));
            }
            if (is_bool($value)) {
                return $value ? 'Yes' : 'No';
            }
            return (string) $value;
        };

        return [
            '${name}' => $formatValue($user->name),
            '${business_name}' => $formatValue($user->business_name),
            '${company_planned_name}' => $formatValue($user->company_planned_name),
            '${company_type}' => $formatValue($user->company_type),
            '${industry}' => $formatValue($user->industry),
            '${address}' => $formatValue($user->address),
            '${zip_code}' => $formatValue($user->zip_code),
            '${postal_district}' => $formatValue($user->postal_district),
            '${internet_address}' => $formatValue($user->internet_address),
            '${business_id}' => $formatValue($user->business_id),
            '${year_of_establishment}' => $formatValue($user->year_of_establishment),
            '${number_of_employees}' => $formatValue($user->number_of_employees),
            '${company_contact_info}' => $formatValue($user->company_contact_info),
            '${company_owners_holdings}' => $formatValue($user->company_owners_holdings),
            '${business_idea}' => $formatValue($user->business_idea),
            '${competence_skills}' => $formatValue($user->competence_skills),
            '${swot_analysis}' => $formatValue($user->swot_analysis),
            '${products_services_general}' => $formatValue($user->products_services_general),
            '${products_services_detailed}' => $formatValue($user->products_services_detailed),
            '${sales_marketing}' => $formatValue($user->sales_marketing),
            '${production_logistics}' => $formatValue($user->production_logistics),
            '${distribution_network}' => $formatValue($user->distribution_network),
            '${target_market_groups}' => $formatValue($user->target_market_groups),
            '${competitors}' => $formatValue($user->competitors),
            '${competitive_situation}' => $formatValue($user->competitive_situation),
            '${third_parties_partners}' => $formatValue($user->third_parties_partners),
            '${operating_environment_risks}' => $formatValue($user->operating_environment_risks),
            '${vision_long_term}' => $formatValue($user->vision_long_term),
            '${industry_future_prospects}' => $formatValue($user->industry_future_prospects),
            '${permits_notices}' => $formatValue($user->permits_notices),
            '${insurance_contracts}' => $formatValue($user->insurance_contracts),
            '${intellectual_property_rights}' => $formatValue($user->intellectual_property_rights),
            '${support_network}' => $formatValue($user->support_network),
            '${my_business_comprehensive}' => $formatValue($user->my_business_comprehensive),
            '${country_of_origin}' => $formatValue($user->country_of_origin),
            '${generation_date}' => date('F j, Y'),
        ];
    }

    /**
     * Convert DOCX file to PDF
     */
    private function convertDocxToPdf(string $docxPath): string
    {
        // Load the DOCX file
        $phpWord = IOFactory::load($docxPath);

        // Set PDF options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        // Ensure temp directory exists
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Convert DOCX to HTML
        $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');
        $tempHtmlPath = $tempDir . '/business-plan-' . time() . '.html';
        $htmlWriter->save($tempHtmlPath);
        
        $htmlContent = file_get_contents($tempHtmlPath);
        unlink($tempHtmlPath);

        // Create PDF from HTML
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Save PDF to temporary file
        $pdfPath = $tempDir . '/business-plan-' . time() . '.pdf';
        $pdfContent = $dompdf->output();
        file_put_contents($pdfPath, $pdfContent);

        return $pdfPath;
    }
}

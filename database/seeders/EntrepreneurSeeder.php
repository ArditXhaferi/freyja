<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EntrepreneurSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entrepreneur = [
            // Basic user information
            'name' => 'Mikael Virtanen',
            'email' => 'mikael@innovatech.fi',
            'password' => Hash::make('password'),
            'role' => 'entrepreneur',
            'email_verified_at' => now(),
            'onboarding_completed_at' => now(),

            // Personal details
            'title' => 'Founder & CEO',
            'bio' => 'Serial entrepreneur with 10+ years of experience in technology startups. Passionate about innovation and building scalable solutions that make a difference.',
            'specialization' => 'SaaS & Enterprise Software',
            'language' => 'fi',
            'languages' => ['fi', 'en', 'sv'],
            'country_of_origin' => 'Finland',
            'is_eu_resident' => true,
            'is_newcomer_to_finland' => false,
            'has_residence_permit' => true,
            'residence_permit_type' => 'Finnish Citizen',
            'years_in_finland' => 30,
            'has_business_experience' => true,

            // Company basic information
            'business_name' => 'InnovaTech Solutions Oy',
            'company_planned_name' => 'InnovaTech Solutions Oy',
            'company_type' => 'Oy',
            'business_id' => 'FI12345678',
            'industry' => 'Information Technology',
            'year_of_establishment' => 2022,
            'number_of_employees' => 15,
            'internet_address' => 'www.innovatech.fi',
            'company_contact_info' => 'Phone: +358 50 123 4567, Email: info@innovatech.fi, Website: www.innovatech.fi',

            // Address information
            'address' => 'Tekniikantie 21',
            'zip_code' => '02150',
            'postal_district' => 'Espoo',

            // Business idea and concept
            'business_idea' => 'We develop AI-powered enterprise software solutions that help mid-sized companies automate their business processes, improve operational efficiency, and reduce costs by up to 40%. Our flagship product is an intelligent workflow automation platform that integrates seamlessly with existing enterprise systems.',

            // Competence and skills
            'competence_skills' => 'Team possesses strong technical expertise in AI/ML, cloud computing, and enterprise software development. Key team members have 15+ years combined experience at major tech companies (Microsoft, IBM, Nokia). Strong business development and sales experience in B2B enterprise market. Proven track record of raising funding and building high-performing teams.',

            // SWOT Analysis
            'swot_analysis' => [
                'strengths' => [
                    'Highly skilled technical team with deep expertise',
                    'Strong market validation with 10+ enterprise customers',
                    'Proven technology platform with competitive advantages',
                    'Excellent network of advisors and investors',
                ],
                'weaknesses' => [
                    'Limited marketing resources compared to large competitors',
                    'Still building brand recognition in the market',
                    'Dependency on key technical personnel',
                ],
                'opportunities' => [
                    'Growing demand for AI automation solutions',
                    'Enterprise digital transformation trend',
                    'Expansion opportunities to European and US markets',
                    'Partnership opportunities with major system integrators',
                ],
                'threats' => [
                    'Competition from large established players (Microsoft, Salesforce)',
                    'Economic downturn affecting enterprise IT spending',
                    'Rapid technological changes requiring continuous innovation',
                ],
            ],

            // Products and services
            'products_services_general' => 'AI-powered workflow automation platform, enterprise integration services, custom AI solutions, business process consulting, training and support services',

            'products_services_detailed' => [
                [
                    'name' => 'InnovaWorkflow Platform',
                    'description' => 'AI-powered workflow automation platform for enterprises',
                    'target_customer' => 'Mid-sized companies (50-500 employees)',
                    'pricing_model' => 'Subscription-based SaaS (€99-999/month per user)',
                    'differentiation' => 'Advanced AI capabilities with easy integration',
                ],
                [
                    'name' => 'Enterprise Integration Services',
                    'description' => 'Custom integration services for existing enterprise systems',
                    'target_customer' => 'Large enterprises',
                    'pricing_model' => 'Project-based (€50k-500k per project)',
                    'differentiation' => 'Deep expertise in enterprise architecture',
                ],
                [
                    'name' => 'Custom AI Solutions',
                    'description' => 'Bespoke AI solutions tailored to specific business needs',
                    'target_customer' => 'Companies with unique automation needs',
                    'pricing_model' => 'Custom pricing',
                    'differentiation' => 'Rapid prototyping and deployment',
                ],
            ],

            // Sales and marketing
            'sales_marketing' => 'Multi-channel approach: Direct sales team targeting enterprise accounts, partner channel through system integrators, content marketing and thought leadership (blogs, webinars, industry conferences), referral program with existing customers. Focus on inbound marketing with SEO, paid advertising on LinkedIn and Google, and participation in key industry events. Strong emphasis on case studies and customer success stories.',

            // Production and logistics
            'production_logistics' => 'Software development follows agile methodology with 2-week sprints. Cloud-based infrastructure hosted on AWS with multi-region deployment for redundancy. Continuous integration/continuous deployment (CI/CD) pipeline for rapid releases. Customer support provided 24/7 through online portal, email, and phone. Development team based in Espoo, Finland, with remote collaboration tools.',

            // Distribution network
            'distribution_network' => 'Primary distribution through direct online sales via company website and sales team. Strategic partnerships with system integrators (Accenture, Capgemini) for enterprise deployments. Integration with major cloud marketplaces (AWS Marketplace, Azure Marketplace). Reseller program for smaller consulting firms. Direct customer onboarding and implementation services.',

            // Target market
            'target_market_groups' => 'Primary: Mid-sized Finnish companies (50-500 employees) in manufacturing, logistics, and professional services seeking digital transformation. Secondary: Larger enterprises (500+ employees) requiring custom solutions. Tertiary: International expansion to Nordic countries (Sweden, Norway, Denmark) and eventually Central Europe. Focus on industries with complex manual processes that can benefit from automation.',

            // Competitors
            'competitors' => 'Direct competitors: Microsoft Power Automate, UiPath, Zapier (for SMB market). Indirect competitors: Traditional ERP providers (SAP, Oracle) and custom development firms. Competitive advantages: Better AI capabilities, easier integration, more affordable pricing for mid-market, stronger local presence and support in Finland.',

            // Competitive situation
            'competitive_situation' => 'Market is competitive but growing rapidly. Large players dominate enterprise segment but have weak mid-market presence. Our focus on mid-market with strong AI capabilities and local support provides differentiation. We compete on: (1) Superior AI technology, (2) Ease of integration, (3) Competitive pricing, (4) Local expertise and support, (5) Faster implementation time. Market position: Challenger with strong technology foundation and growing customer base.',

            // Third parties and partners
            'third_parties_partners' => 'Key partnerships: AWS (cloud infrastructure), Microsoft (Azure integration), Stripe (payment processing), Twilio (communication APIs). Strategic partners: System integrators (Accenture Finland, Capgemini), consulting firms (Deloitte Digital), technology resellers. Advisory board includes former executives from Microsoft and IBM. Investor network includes Finnish and Nordic VCs.',

            // Operating environment and risks
            'operating_environment_risks' => 'Key risks: (1) Economic downturn reducing enterprise IT spending, (2) Increased competition from large tech companies, (3) Key personnel leaving, (4) Technology obsolescence, (5) Data security and GDPR compliance requirements, (6) Customer concentration risk. Mitigation strategies: Diversified customer base, strong IP protection, comprehensive security measures, employee retention programs, continuous technology innovation, compliance certifications (ISO 27001, SOC 2).',

            // Vision and long-term goals
            'vision_long_term' => 'To become the leading AI automation platform in the Nordic region by 2027, expanding to serve 500+ enterprise customers across Finland, Sweden, Norway, and Denmark. Long-term vision includes expansion to Central Europe and potential entry into the US market. Goal to achieve €50M annual recurring revenue (ARR) by 2027. Build a strong brand known for innovation, reliability, and exceptional customer service. Eventually explore strategic acquisition opportunities or IPO potential.',

            // Industry future prospects
            'industry_future_prospects' => 'AI automation market is expected to grow at 25% CAGR over next 5 years, driven by: digital transformation initiatives, labor shortages, cost reduction pressures, and AI technology maturation. Enterprise adoption of AI automation is accelerating. Market opportunity in Nordic region alone is estimated at €2B by 2027. Favorable government policies supporting innovation and digitalization in Finland. Strong talent pool in AI/ML in Finland supports growth.',

            // Permits and notices
            'permits_notices' => 'Company is registered with Finnish Trade Register (Yritys- ja yhteisötietojärjestelmä). VAT registered. All necessary business licenses obtained. Compliance with Finnish data protection laws and GDPR. Regular tax filings and compliance with accounting standards. Employee contracts compliant with Finnish labor law. Software licenses and third-party agreements in place.',

            // Insurance contracts
            'insurance_contracts' => 'Comprehensive business insurance package including: General liability insurance (€2M coverage), Professional liability/Errors & Omissions insurance (€5M coverage), Cyber liability insurance (€1M coverage), Key person insurance, Business interruption insurance. All policies reviewed annually and updated as company grows. Strong focus on cybersecurity insurance given nature of business.',

            // Intellectual property rights
            'intellectual_property_rights' => 'Core platform technology protected by multiple patent applications (3 pending in Finland, 2 in EU). Proprietary AI algorithms are trade secrets. All code and IP developed in-house. Comprehensive employee IP assignment agreements in place. Trademark applications for brand name and logos filed in Finland and EU. Open source components carefully managed and compliant with licenses. Regular IP audits conducted.',

            // Support network
            'support_network' => 'Strong support network: Advisory board with 4 experienced entrepreneurs and executives, active participation in Finnish startup ecosystem (Slush, Arctic Startup), membership in Finnish Software Industry Association, mentorship from experienced entrepreneurs, investor network providing strategic guidance, partnerships with universities (Aalto University, University of Helsinki) for research collaboration, government support through Business Finland programs.',

            // Comprehensive business overview
            'my_business_comprehensive' => 'InnovaTech Solutions is a fast-growing AI automation software company based in Espoo, Finland. Founded in 2022 by experienced entrepreneurs, we develop cutting-edge AI-powered workflow automation solutions for mid-sized enterprises. Our flagship platform helps companies automate complex business processes, resulting in significant cost savings and operational efficiency improvements.

Our team of 15 highly skilled professionals combines deep technical expertise in AI/ML with strong business acumen. We have successfully served 10+ enterprise customers and are experiencing strong growth with increasing market validation.

Our competitive advantages lie in superior AI technology, ease of integration with existing systems, competitive pricing for the mid-market, and strong local presence in Finland. We operate in a rapidly growing market with excellent future prospects, supported by digital transformation trends and favorable regulatory environment.

We have built strong partnerships with major cloud providers, system integrators, and technology platforms. Our comprehensive risk management approach, strong IP protection, and robust support network position us well for continued growth and expansion across the Nordic region.',
        ];

        User::updateOrCreate(
            ['email' => $entrepreneur['email']],
            $entrepreneur
        );
    }
}

# ElevenLabs First Message Template

## Copy this into your ElevenLabs Agent Dashboard → First Message field:

```
Hi! I'm Eppu the Bear, your AI startup coach! 🐻

Nice to meet you, {{user_name}}!

I see you're working on {{business_name}}. That's a great {{industry}} venture!

Ready to build your startup roadmap? Let's get started!

Let's talk about your {{business_idea}} business!
```

**Note:** Variables will be replaced with actual values if available, or left as-is if not provided. Make sure to pass all variables when initializing conversations.

## Shorter Version:

```
Hi! I'm Eppu the Bear, your AI startup coach! 🐻

Hi {{user_name}}! I see you're working on {{business_name}}.

Ready to build your startup roadmap? Let's do this!

What kind of {{business_idea}} business are you planning?
```

## Required Dynamic Variable Placeholders

In your ElevenLabs Agent configuration, you need to define these placeholders:

1. Go to your Agent settings
2. Find "Dynamic Variables" → "dynamic_variable_placeholders" section
3. Add these placeholders (all are **Required**):

### Variable Placeholders (Required):
- `user_name` (string) - User's name
- `business_name` (string) - Business name or planned company name
- `industry` (string) - Business industry
- `business_idea` (string) - Short business idea description

**Note:** ElevenLabs uses `{{variable}}` (double curly braces) for simple variable substitution. If a variable is not provided, it will remain as `{{variable}}` in the message.

## How It Works

1. **Set the template** in ElevenLabs dashboard (copy from above)
2. **Define the placeholders** in your agent configuration
3. **Code automatically passes variables** when initializing conversations:
   - `user_name` - From props
   - `business_name` - From business plan (business_name or company_planned_name)
   - `industry` - From business plan
   - `country_of_origin` - From business plan
   - `years_in_finland` - From business plan
   - `has_business_experience` - From business plan
   - `has_existing_roadmap` - Calculated from roadmap data
   - `roadmap_steps_count` - Number of roadmap steps
   - `missing_background_info` - Calculated from business plan data
   - `business_idea` - Short version from business plan (first 30 chars)

The variables are passed automatically when `initializeChat()` is called, so the first message will be personalized based on the user's current data!


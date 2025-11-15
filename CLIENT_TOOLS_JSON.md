# ElevenLabs Client Tools JSON Schemas

## 1. generateBusinessPlan Tool

```json
{
  "name": "generateBusinessPlan",
  "description": "Generate a business plan PDF document from the template.docx file using the user's business plan information. Ask the user if they want to generate the PDF when they are ready.",
  "expects_response": true,
  "response_timeout_secs": 30,
  "parameters": [],
  "dynamic_variables": {
    "dynamic_variable_placeholders": []
  }
}
```

## 2. markChecklistComplete Tool

```json
{
  "name": "markChecklistComplete",
  "description": "Mark a roadmap step as completed. Updates the step status and triggers a celebration animation with progress tracking.",
  "expects_response": true,
  "response_timeout_secs": 30,
  "parameters": [
    {
      "id": "step",
      "type": "object",
      "description": "The roadmap step to mark as complete",
      "required": true,
      "dynamic_variable": "",
      "constant_value": "",
      "properties": [
        {
          "id": "id",
          "type": "integer",
          "description": "Step ID (if available)",
          "required": false,
          "dynamic_variable": "",
          "constant_value": ""
        },
        {
          "id": "order",
          "type": "integer",
          "description": "Step order number",
          "required": false,
          "dynamic_variable": "",
          "constant_value": ""
        },
        {
          "id": "title",
          "type": "string",
          "description": "Step title (used as identifier if id/order not available)",
          "required": false,
          "dynamic_variable": "",
          "constant_value": ""
        }
      ]
    }
  ],
  "dynamic_variables": {
    "dynamic_variable_placeholders": []
  }
}
```

## 3. requestDocument Tool

```json
{
  "name": "requestDocument",
  "description": "Request a specific document or information from the user. Creates a request card that the user can mark as provided.",
  "expects_response": true,
  "response_timeout_secs": 30,
  "parameters": [
    {
      "id": "document",
      "type": "object",
      "description": "Document request information",
      "required": true,
      "dynamic_variable": "",
      "constant_value": "",
      "properties": [
        {
          "id": "type",
          "type": "string",
          "description": "Type of document (e.g., 'registration', 'permit', 'financial', 'general')",
          "required": false,
          "dynamic_variable": "",
          "constant_value": ""
        },
        {
          "id": "title",
          "type": "string",
          "description": "Title of the document or information needed",
          "required": true,
          "dynamic_variable": "",
          "constant_value": ""
        },
        {
          "id": "description",
          "type": "string",
          "description": "Detailed description of what is needed and why",
          "required": true,
          "dynamic_variable": "",
          "constant_value": ""
        },
        {
          "id": "required",
          "type": "boolean",
          "description": "Whether this document is required (default: true)",
          "required": false,
          "dynamic_variable": "",
          "constant_value": ""
        },
        {
          "id": "field",
          "type": "string",
          "description": "Related business plan field (if applicable)",
          "required": false,
          "dynamic_variable": "",
          "constant_value": ""
        }
      ]
    }
  ],
  "dynamic_variables": {
    "dynamic_variable_placeholders": []
  }
}
```

## 4. suggestResource Tool

```json
{
  "name": "suggestResource",
  "description": "Suggest a helpful resource, guide, or link to the user. Displays a rich resource card with icon, category, and preview text.",
  "expects_response": true,
  "response_timeout_secs": 30,
  "parameters": [
    {
      "id": "resource",
      "type": "object",
      "description": "Resource information",
      "required": true,
      "dynamic_variable": "",
      "constant_value": "",
      "properties": [
        {
          "id": "title",
          "type": "string",
          "description": "Resource title",
          "required": true,
          "dynamic_variable": "",
          "constant_value": ""
        },
        {
          "id": "description",
          "type": "string",
          "description": "Full description of the resource",
          "required": false,
          "dynamic_variable": "",
          "constant_value": ""
        },
        {
          "id": "url",
          "type": "string",
          "description": "URL to the resource",
          "required": true,
          "dynamic_variable": "",
          "constant_value": ""
        },
        {
          "id": "category",
          "type": "string",
          "description": "Resource category (e.g., 'Registration', 'Permits', 'Funding', 'Networking', 'Legal', 'Taxes')",
          "required": false,
          "dynamic_variable": "",
          "constant_value": ""
        },
        {
          "id": "icon",
          "type": "string",
          "description": "Emoji icon for the resource (e.g., '📋', '💰', '📚')",
          "required": false,
          "dynamic_variable": "",
          "constant_value": ""
        },
        {
          "id": "preview",
          "type": "string",
          "description": "Preview text to display on the card (first 100 characters)",
          "required": false,
          "dynamic_variable": "",
          "constant_value": ""
        }
      ]
    }
  ],
  "dynamic_variables": {
    "dynamic_variable_placeholders": []
  }
}
```

## 5. generateProgressSummary Tool

```json
{
  "name": "generateProgressSummary",
  "description": "Generate and display a comprehensive progress summary showing business plan completion, roadmap progress, achievements, and next steps.",
  "expects_response": true,
  "response_timeout_secs": 30,
  "parameters": [
    {
      "id": "summary",
      "type": "object",
      "description": "Optional custom summary data (can be empty - progress is calculated automatically)",
      "required": false,
      "dynamic_variable": "",
      "constant_value": "",
      "properties": [
        {
          "id": "custom_message",
          "type": "string",
          "description": "Optional custom message to include in the summary (ignored if empty)",
          "required": false,
          "dynamic_variable": "",
          "constant_value": ""
        }
      ]
    }
  ],
  "dynamic_variables": {
    "dynamic_variable_placeholders": {}
  }
}
```

## Summary of Changes

All tools now include:
- `"expects_response": true` - Indicates the tool expects a response from the client
- `"response_timeout_secs": 30` - 30 second timeout for the response
- `"dynamic_variables": { "dynamic_variable_placeholders": [] }` - Empty array for dynamic variable placeholders (required by ElevenLabs)

These should now pass validation in the ElevenLabs dashboard.


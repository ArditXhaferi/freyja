# Eppu the Bear - First Message Prompt

## ✅ Recommended Approach: Build Dynamically from Backend

**The first message is now built dynamically from backend data** - no need for dynamic variables in ElevenLabs!

The message is automatically generated based on:
- **User's name** - Passed from backend via Inertia props (`userName`)
- **Existing roadmap status** - Checked from `/api/roadmap` (if steps exist)
- **Missing background information** - Checked from `/api/business-plan` (if contextual fields are null/empty)

This happens in `useVoiceAgent.js` when `connect()` is called:
1. Fetches data from `/api/business-plan` and `/api/roadmap`
2. Builds personalized first message using `buildFirstMessage()` function
3. Includes the message in the context sent to the agent via `sendContextualUpdate()`
4. Agent receives instruction: "FIRST MESSAGE TO SAY: [personalized message]"

---

## 🎯 What to Set in ElevenLabs Dashboard

**⚠️ IMPORTANT: Set a simple first message in the ElevenLabs Agent "First Message" field to trigger the agent to speak:**

```
Hi! I'm Eppu the Bear, your AI startup coach! 🐻

Ready to build your startup roadmap? Let's get started!
```

**Why set a first message?**
- ElevenLabs agents need a "First Message" set to trigger them to speak automatically
- The personalized message is still built dynamically and sent via context
- The agent will receive: `"FIRST MESSAGE TO SAY: [personalized message]"` in the context
- The agent should use the personalized message from context, but the dashboard message ensures the agent speaks

**How it works:**
1. Dashboard "First Message" triggers the agent to start speaking
2. Context with personalized message is sent via `sendContextualUpdate()`
3. Agent receives explicit instruction: `"FIRST MESSAGE TO SAY: [personalized message]"`
4. Agent should use the personalized message from context (not the dashboard message)

**Note:** If the agent still doesn't speak, the dashboard message will serve as a fallback, but the personalized message from context should take priority.

---

## Alternative: Using ElevenLabs Dynamic Variables

If you prefer to use ElevenLabs dynamic variables instead, use the format below:

## Short Greeting (Recommended)

```
Hi! I'm Eppu the Bear, your AI startup coach! 🐻

{_if_user_name}Nice to meet you, {user_name}!{_if}

{_if_has_existing_roadmap}I see you already have a roadmap started - that's great! Let's continue building on that.{else}Ready to build your startup roadmap? Let's get started!{_if}

{_if_missing_background_info}First, let me ask a few quick questions to personalize your journey:{else}Let's talk about your business idea!{_if}
```

## Alternative: Even Shorter Version

```
Hey there! I'm Eppu the Bear 🐻 - your startup coach!

{_if_user_name}Hi {user_name}!{_if}

{_if_has_existing_roadmap}I see you've already started - awesome! Let's keep going.{else}Ready to build your startup roadmap? Let's do this!{_if}

{_if_missing_background_info}Quick question first: Are you an EU/EEA citizen, or will you need a residence permit?{else}What kind of business are you planning to start?{_if}
```

## Simplest Version (No Conditionals)

If you want to avoid dynamic variables entirely:

```
Hi! I'm Eppu the Bear, your AI startup coach! 🐻

Ready to build your startup roadmap? Let me ask a few quick questions first to personalize your journey.

Are you an EU/EEA citizen, or will you need a residence permit to work in Finland?
```

## Required Dynamic Variable Placeholders

For ElevenLabs validation, you need to define these dynamic variable placeholders in your agent configuration:

```json
{
  "dynamic_variables": {
    "dynamic_variable_placeholders": [
      {
        "id": "_if_user_name",
        "description": "Conditional check if user_name exists"
      },
      {
        "id": "user_name",
        "description": "User's name"
      },
      {
        "id": "_if",
        "description": "Closing tag for conditional blocks"
      },
      {
        "id": "_if_has_existing_roadmap",
        "description": "Conditional check if user has existing roadmap steps"
      },
      {
        "id": "else",
        "description": "Else clause for conditional blocks"
      },
      {
        "id": "_if_missing_background_info",
        "description": "Conditional check if contextual background fields are missing"
      }
    ]
  }
}
```

## Variables Available

- `user_name` - User's name (if available)
- `has_existing_roadmap` - Boolean: true if user has roadmap steps
- `missing_background_info` - Boolean: true if contextual fields are missing
- `business_plan_completion` - Percentage of business plan filled (0-100)
- `roadmap_steps_count` - Number of roadmap steps
- `completed_steps_count` - Number of completed roadmap steps

## How It Works (Dynamic Backend Approach)

The first message is built in `useVoiceAgent.js` when `connect()` is called:

1. **Fetches data from backend:**
   - `/api/business-plan` - Gets all business plan and contextual fields
   - `/api/roadmap` - Gets roadmap steps

2. **Builds message dynamically:**
   ```javascript
   const buildFirstMessage = (businessPlan, roadmap, userName) => {
       // Checks actual data and builds personalized message
   }
   ```

3. **Message logic:**
   - If `userName` exists → adds personalized greeting
   - If roadmap has steps → says "continue building"
   - If contextual fields missing → asks about background first
   - Otherwise → asks about business idea

4. **No ElevenLabs configuration needed** - the message is built in code!

## Usage Instructions (If Using Dynamic Variables)

If you want to use ElevenLabs dynamic variables instead:

1. **Copy your preferred prompt** from above
2. **Add the dynamic variable placeholders** to your ElevenLabs Agent configuration (see JSON above)
3. **Set the first message** in your ElevenLabs Agent settings
4. **Ensure your backend provides these variables** when initializing the conversation

## ElevenLabs Variable Syntax

ElevenLabs uses: `{variable_name}` (single curly braces, not double)

Conditional format:
- `{_if_variable_name}...content...{else}...alternative...{_if}`

Make sure to define all placeholders in the `dynamic_variable_placeholders` array in your agent configuration.


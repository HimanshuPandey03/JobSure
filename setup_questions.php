<?php
require 'db_config.php';

// =================================================================
// FULL DATABASE SETUP SCRIPT
// Populates 11 Categories x 25 Questions with 4-Point Scoring Logic
// =================================================================

$all_categories = [
    'Human Resources (HR)' => [
        // SECTION A: PERSONAL & MOTIVATION
        ['text' => 'What inspired you to choose HR as your career?', 'options' => ['A' => 'I enjoy helping people grow professionally', 'B' => 'HR gives me stability and long-term scope', 'C' => 'I like solving workplace issues', 'D' => 'I want to create a positive work culture'], 'points' => ['A'=>4, 'D'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'What do you enjoy most about interacting with people at work?', 'options' => ['A' => 'Understanding different personalities', 'B' => 'Helping people solve problems', 'C' => 'Building connections and trust', 'D' => 'Learning how teams function'], 'points' => ['C'=>4, 'B'=>3, 'A'=>2, 'D'=>1]],
        ['text' => 'How would you describe your communication style?', 'options' => ['A' => 'Clear and respectful', 'B' => 'Calm and patient', 'C' => 'Direct but polite', 'D' => 'Friendly yet professional'], 'points' => ['A'=>4, 'D'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'Which personal value aligns most with HR?', 'options' => ['A' => 'Integrity', 'B' => 'Empathy', 'C' => 'Confidentiality', 'D' => 'Fairness'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'How do you stay calm during difficult situations?', 'options' => ['A' => 'Listen first, respond later', 'B' => 'Break the issue into smaller parts', 'C' => 'Focus on facts, not emotions', 'D' => 'Take a moment, then act'], 'points' => ['C'=>4, 'A'=>3, 'B'=>2, 'D'=>1]],

        // SECTION B: HR KNOWLEDGE
        ['text' => 'Which area of HR interests you the most?', 'options' => ['A' => 'Recruitment', 'B' => 'Training & Development', 'C' => 'Payroll & Compliance', 'D' => 'Employee Relations'], 'points' => ['D'=>4, 'A'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'What is the main role of HR in a company?', 'options' => ['A' => 'Hiring good talent', 'B' => 'Supporting employee growth', 'C' => 'Maintaining policies', 'D' => 'Building a healthy work culture'], 'points' => ['D'=>4, 'A'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'What does “employee engagement” mean?', 'options' => ['A' => 'How happy employees feel at work', 'B' => 'How connected employees feel to the company', 'C' => 'How much employees trust their leaders', 'D' => 'How motivated employees are to give their best'], 'points' => ['D'=>4, 'B'=>3, 'C'=>2, 'A'=>1]],
        ['text' => 'How familiar are you with HR policies?', 'options' => ['A' => 'Very familiar', 'B' => 'Have basic understanding', 'C' => 'Know a few policies', 'D' => 'Still learning'], 'points' => ['A'=>4, 'B'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'What does confidentiality in HR mean to you?', 'options' => ['A' => 'Never sharing personal data', 'B' => 'Protecting sensitive information', 'C' => 'Not discussing employee issues casually', 'D' => 'All of the above'], 'points' => ['D'=>4, 'B'=>3, 'A'=>2, 'C'=>1]],

        // SECTION C: PRACTICAL SITUATION
        ['text' => 'Two employees are arguing. What do you do first?', 'options' => ['A' => 'Listen to both sides separately', 'B' => 'Calm them down immediately', 'C' => 'Understand the root cause', 'D' => 'Bring them together for resolution'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'A great candidate demands a high salary. What do you do?', 'options' => ['A' => 'Explain company budget', 'B' => 'Negotiate mutually', 'C' => 'Check flexibility with management', 'D' => 'Highlight non-salary benefits'], 'points' => ['D'=>4, 'B'=>3, 'C'=>2, 'A'=>1]],
        ['text' => 'What is your first step during onboarding?', 'options' => ['A' => 'Make them feel welcomed', 'B' => 'Explain job role clearly', 'C' => 'Introduce them to team', 'D' => 'Explain company culture'], 'points' => ['D'=>4, 'B'=>3, 'C'=>2, 'A'=>1]],
        ['text' => 'An employee is often late. Your step?', 'options' => ['A' => 'Talk privately', 'B' => 'Understand reason', 'C' => 'Give guidelines', 'D' => 'Track improvement'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'A manager says hiring is slow. Your response?', 'options' => ['A' => 'Share the current progress', 'B' => 'Explain challenges', 'C' => 'Offer a revised hiring plan', 'D' => 'Increase sourcing efforts'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],

        // SECTION D: COMMUNICATION
        ['text' => 'How do you take critical feedback?', 'options' => ['A' => 'As a chance to improve', 'B' => 'Calmly and professionally', 'C' => 'By asking for details', 'D' => 'By reflecting before reacting'], 'points' => ['A'=>4, 'D'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'Best way to solve problems through communication?', 'options' => ['A' => 'Listen actively', 'B' => 'Ask the right questions', 'C' => 'Stay neutral', 'D' => 'Provide clear solutions'], 'points' => ['A'=>4, 'B'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'How do you build trust with new people?', 'options' => ['A' => 'Keep promises', 'B' => 'Communicate honestly', 'C' => 'Respect boundaries', 'D' => 'Show consistency'], 'points' => ['D'=>4, 'B'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'Most important quality for HR?', 'options' => ['A' => 'Empathy', 'B' => 'Confidentiality', 'C' => 'Patience', 'D' => 'Neutral judgement'], 'points' => ['D'=>4, 'A'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'How would you motivate a low performer?', 'options' => ['A' => 'Understand the reason', 'B' => 'Set clear goals', 'C' => 'Give positive feedback', 'D' => 'Support with resources'], 'points' => ['A'=>4, 'B'=>3, 'D'=>2, 'C'=>1]],

        // SECTION E: FUTURE GOALS
        ['text' => 'Where do you see yourself in HR in 3 years?', 'options' => ['A' => 'HR Executive with strong skills', 'B' => 'HR Generalist handling key responsibilities', 'C' => 'Specialist in Recruitment/ER/Training', 'D' => 'Leading small HR initiatives'], 'points' => ['B'=>4, 'C'=>3, 'D'=>2, 'A'=>1]],
        ['text' => 'Which HR skill do you want to improve?', 'options' => ['A' => 'Communication', 'B' => 'Conflict handling', 'C' => 'Recruitment strategy', 'D' => 'Policy understanding'], 'points' => ['B'=>4, 'C'=>3, 'D'=>2, 'A'=>1]],
        ['text' => 'How do you stay updated with HR trends?', 'options' => ['A' => 'Reading HR blogs', 'B' => 'Attending webinars', 'C' => 'Learning from mentors', 'D' => 'Following HR communities'], 'points' => ['C'=>4, 'B'=>3, 'A'=>2, 'D'=>1]],
        ['text' => 'What do you expect from your HR team?', 'options' => ['A' => 'Support and guidance', 'B' => 'A positive work culture', 'C' => 'Clear communication', 'D' => 'Opportunities to grow'], 'points' => ['D'=>4, 'B'=>3, 'C'=>2, 'A'=>1]],
        ['text' => 'Why should a company hire you for HR?', 'options' => ['A' => 'I connect well with people', 'B' => 'I solve problems calmly', 'C' => 'I take responsibility seriously', 'D' => 'I bring positive energy to the team'], 'points' => ['B'=>4, 'C'=>3, 'A'=>2, 'D'=>1]]
    ],

    'Sales' => [
        // SECTION A
        ['text' => 'Why do you want to build a career in Sales?', 'options' => ['A' => 'I love interacting with people', 'B' => 'I enjoy convincing and influencing', 'C' => 'Sales offers high growth & income', 'D' => 'I like solving customer problems'], 'points' => ['B'=>4, 'D'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'Which sales trait fits your personality the most?', 'options' => ['A' => 'Confidence', 'B' => 'Communication', 'C' => 'Patience', 'D' => 'Persistence'], 'points' => ['D'=>4, 'A'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'What motivates you the most in sales?', 'options' => ['A' => 'Closing deals', 'B' => 'Building customer trust', 'C' => 'Achieving targets', 'D' => 'Helping customers find solutions'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'How comfortable are you talking to new people?', 'options' => ['A' => 'Very confident', 'B' => 'Comfortable after a few minutes', 'C' => 'Nervous but try', 'D' => 'Need more practice'], 'points' => ['A'=>4, 'B'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'What is your biggest strength as a sales candidate?', 'options' => ['A' => 'Listening', 'B' => 'Negotiation', 'C' => 'Problem solving', 'D' => 'Relationship building'], 'points' => ['B'=>4, 'D'=>3, 'A'=>2, 'C'=>1]],

        // SECTION B
        ['text' => 'What is most important in sales?', 'options' => ['A' => 'Understanding customer needs', 'B' => 'Strong product knowledge', 'C' => 'Effective communication', 'D' => 'Trust building'], 'points' => ['A'=>4, 'D'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'What type of customer is most challenging?', 'options' => ['A' => 'Silent customer', 'B' => 'Confused customer', 'C' => 'Aggressive customer', 'D' => 'Bargaining customer'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What helps build trust quickly?', 'options' => ['A' => 'Listening actively', 'B' => 'Speaking confidently', 'C' => 'Giving clear answers', 'D' => 'Being honest about limitations'], 'points' => ['D'=>4, 'A'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'How do you handle customer objections?', 'options' => ['A' => 'Stay calm & listen', 'B' => 'Ask clarifying questions', 'C' => 'Provide solutions logically', 'D' => 'Offer alternatives'], 'points' => ['B'=>4, 'C'=>3, 'A'=>2, 'D'=>1]],
        ['text' => 'What is key to a successful sales pitch?', 'options' => ['A' => 'Clear message', 'B' => 'Understanding pain points', 'C' => 'Showing value', 'D' => 'Being confident'], 'points' => ['C'=>4, 'B'=>3, 'A'=>2, 'D'=>1]],

        // SECTION C
        ['text' => 'If a customer says “price is too high”, what do you do first?', 'options' => ['A' => 'Ask what price they expected', 'B' => 'Show value & benefits', 'C' => 'Offer different packages', 'D' => 'Explain quality difference'], 'points' => ['B'=>4, 'D'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'If a customer shows interest but doesn’t decide, what’s your approach?', 'options' => ['A' => 'Follow up', 'B' => 'Give more details', 'C' => 'Understand their hesitation', 'D' => 'Create urgency ethically'], 'points' => ['D'=>4, 'C'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'What is most important in negotiation?', 'options' => ['A' => 'Patience', 'B' => 'Understanding customer’s need', 'C' => 'Value-based communication', 'D' => 'Finding win-win outcome'], 'points' => ['D'=>4, 'C'=>3, 'B'=>2, 'A'=>1]],
        ['text' => 'When should you ask for the sale?', 'options' => ['A' => 'After solving all doubts', 'B' => 'When customer is convinced', 'C' => 'After showing value', 'D' => 'At the right emotional moment'], 'points' => ['A'=>4, 'B'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'What helps close deals faster?', 'options' => ['A' => 'Product knowledge', 'B' => 'Understanding objections', 'C' => 'Confidence', 'D' => 'Relationship management'], 'points' => ['B'=>4, 'D'=>3, 'C'=>2, 'A'=>1]],

        // SECTION D
        ['text' => 'How do you react if you lose a potential customer?', 'options' => ['A' => 'Analyze what went wrong', 'B' => 'Follow up later', 'C' => 'Improve pitch for next time', 'D' => 'Stay positive'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What makes a great salesperson?', 'options' => ['A' => 'Listening', 'B' => 'Empathy', 'C' => 'Persistence', 'D' => 'Product knowledge'], 'points' => ['C'=>4, 'A'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'Your sales target is difficult. What will you do?', 'options' => ['A' => 'Improve daily activity', 'B' => 'Try new strategies', 'C' => 'Reach out to more customers', 'D' => 'Stay consistent and focused'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'How do you handle pressure or rejection?', 'options' => ['A' => 'Stay calm', 'B' => 'Focus on next opportunity', 'C' => 'Learn from mistakes', 'D' => 'Keep improving'], 'points' => ['B'=>4, 'C'=>3, 'D'=>2, 'A'=>1]],
        ['text' => 'Your customer is confused. What’s your approach?', 'options' => ['A' => 'Ask questions', 'B' => 'Understand their need', 'C' => 'Explain options simply', 'D' => 'Suggest best solution'], 'points' => ['D'=>4, 'A'=>3, 'B'=>2, 'C'=>1]],

        // SECTION E
        ['text' => 'Where do you see yourself in sales in 3 years?', 'options' => ['A' => 'Senior Sales Executive', 'B' => 'Team Leader', 'C' => 'Key Account Manager', 'D' => 'Sales Trainer'], 'points' => ['C'=>4, 'B'=>3, 'A'=>2, 'D'=>1]],
        ['text' => 'Which sales skill do you want to improve first?', 'options' => ['A' => 'Prospecting', 'B' => 'Closing', 'C' => 'Negotiation', 'D' => 'Communication'], 'points' => ['B'=>4, 'C'=>3, 'A'=>2, 'D'=>1]],
        ['text' => 'How do you stay updated in the sales field?', 'options' => ['A' => 'Training & workshops', 'B' => 'Observing top performers', 'C' => 'Learning from experience', 'D' => 'Watching sales videos'], 'points' => ['B'=>4, 'C'=>3, 'A'=>2, 'D'=>1]],
        ['text' => 'What do you expect from your sales team?', 'options' => ['A' => 'Support', 'B' => 'Team coordination', 'C' => 'Learning environment', 'D' => 'Motivating culture'], 'points' => ['D'=>4, 'C'=>3, 'B'=>2, 'A'=>1]],
        ['text' => 'Why should a company hire you for a sales role?', 'options' => ['A' => 'I understand people', 'B' => 'I can convince with clarity', 'C' => 'I handle pressure well', 'D' => 'I bring energy & dedication'], 'points' => ['D'=>4, 'B'=>3, 'C'=>2, 'A'=>1]]
    ],

    'Information Technology (IT)' => [
        // SECTION A
        ['text' => 'Why do you want to pursue a career in IT?', 'options' => ['A' => 'Technology excites me', 'B' => 'IT offers long-term growth', 'C' => 'I like solving problems', 'D' => 'I want a stable & respected career'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'Which IT role fits your personality the most?', 'options' => ['A' => 'Developer / Programmer', 'B' => 'Tester / QA', 'C' => 'Data Analyst', 'D' => 'IT Support / System Admin'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'What motivates you the most in technical work?', 'options' => ['A' => 'Building something new', 'B' => 'Finding and fixing issues', 'C' => 'Working with data', 'D' => 'Helping users solve problems'], 'points' => ['A'=>4, 'B'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'How do you prefer working?', 'options' => ['A' => 'Independent coding', 'B' => 'Team projects', 'C' => 'Structured tasks', 'D' => 'Fast-paced troubleshooting'], 'points' => ['A'=>4, 'B'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'What is your biggest strength?', 'options' => ['A' => 'Logic', 'B' => 'Patience', 'C' => 'Curiosity', 'D' => 'Discipline'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        
        // SECTION B
        ['text' => 'Which skill area interests you most?', 'options' => ['A' => 'Frontend development', 'B' => 'Backend development', 'C' => 'Database management', 'D' => 'Cloud / DevOps'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'Which programming language feels most comfortable to you?', 'options' => ['A' => 'Java', 'B' => 'Python', 'C' => 'JavaScript', 'D' => 'C / C++'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'What is the most important habit for an IT professional?', 'options' => ['A' => 'Keep learning', 'B' => 'Write clean code', 'C' => 'Debug efficiently', 'D' => 'Collaborate well'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'How strong are your problem-solving skills?', 'options' => ['A' => 'Very strong', 'B' => 'Good', 'C' => 'Average', 'D' => 'Improving'], 'points' => ['A'=>4, 'B'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'What is your preferred way to learn new technology?', 'options' => ['A' => 'Online courses', 'B' => 'YouTube tutorials', 'C' => 'Projects & practice', 'D' => 'Learning from mentors'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],

        // SECTION C
        ['text' => 'When facing a coding error, what’s your approach?', 'options' => ['A' => 'Re-read the logic', 'B' => 'Check resources online', 'C' => 'Try debugging tools', 'D' => 'Break problem into parts'], 'points' => ['D'=>4, 'C'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'If a project deadline is near, what do you focus on?', 'options' => ['A' => 'Completing core features', 'B' => 'Testing stability', 'C' => 'Fixing major bugs', 'D' => 'Team coordination'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'How do you handle unfamiliar tasks?', 'options' => ['A' => 'Research and learn', 'B' => 'Ask teammates', 'C' => 'Experiment practically', 'D' => 'Break it into steps'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What do you value most in IT teamwork?', 'options' => ['A' => 'Good communication', 'B' => 'Helping each other', 'C' => 'Clear roles', 'D' => 'Knowledge sharing'], 'points' => ['D'=>4, 'B'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'While coding, what is your first priority?', 'options' => ['A' => 'Logic clarity', 'B' => 'Code quality', 'C' => 'Performance', 'D' => 'User needs'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],

        // SECTION D
        ['text' => 'How do you react under pressure?', 'options' => ['A' => 'Stay calm & logical', 'B' => 'Prioritize tasks', 'C' => 'Ask for help if needed', 'D' => 'Focus on solutions'], 'points' => ['A'=>4, 'D'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'What type of tasks challenge you the most?', 'options' => ['A' => 'Debugging', 'B' => 'Writing optimized code', 'C' => 'Understanding complex logic', 'D' => 'Learning new tools fast'], 'points' => ['C'=>4, 'A'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'If your code doesn’t work after many attempts, what will you do?', 'options' => ['A' => 'Re-write from scratch', 'B' => 'Divide the code and test parts', 'C' => 'Search references', 'D' => 'Seek guidance'], 'points' => ['B'=>4, 'D'=>3, 'C'=>2, 'A'=>1]],
        ['text' => 'How do you approach continuous learning?', 'options' => ['A' => 'Daily small learning', 'B' => 'Weekly deep learning', 'C' => 'Learning during projects', 'D' => 'Following trends regularly'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What is more important in IT?', 'options' => ['A' => 'Skills', 'B' => 'Practical knowledge', 'C' => 'Attitude', 'D' => 'All of the above'], 'points' => ['B'=>4, 'A'=>3, 'C'=>2, 'D'=>1]],

        // SECTION E
        ['text' => 'Where do you see yourself in 3 years?', 'options' => ['A' => 'Skilled developer', 'B' => 'IT Analyst or Specialist', 'C' => 'Team contributor', 'D' => 'Leading small projects'], 'points' => ['A'=>4, 'D'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'What is your long-term IT goal?', 'options' => ['A' => 'Full-stack developer', 'B' => 'Cloud architect', 'C' => 'Data scientist', 'D' => 'Cybersecurity expert'], 'points' => ['A'=>4, 'B'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'What skill do you want to improve first?', 'options' => ['A' => 'Coding logic', 'B' => 'Communication', 'C' => 'Debugging', 'D' => 'Project management'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What makes you fit for an IT career?', 'options' => ['A' => 'Curious mind', 'B' => 'Logical thinking', 'C' => 'Patience', 'D' => 'Consistency'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'Why should an IT company hire you?', 'options' => ['A' => 'I learn fast', 'B' => 'I solve problems', 'C' => 'I work dedicatedly', 'D' => 'I grow with the team'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]]
    ],

    'Influencer' => [
        // SECTION A
        ['text' => 'Why do you want to become a social media influencer?', 'options' => ['A' => 'To inspire people', 'B' => 'To express creativity', 'C' => 'To build a personal brand', 'D' => 'To earn income from content'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'Which content category fits your personality most?', 'options' => ['A' => 'Fashion & Lifestyle', 'B' => 'Education & Tips', 'C' => 'Entertainment & Humor', 'D' => 'Motivation & Self-growth'], 'points' => ['B'=>4, 'D'=>3, 'C'=>2, 'A'=>1]],
        ['text' => 'What is your unique strength?', 'options' => ['A' => 'Creativity', 'B' => 'Confidence', 'C' => 'Communication', 'D' => 'Consistency'], 'points' => ['D'=>4, 'A'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'How comfortable are you in front of a camera?', 'options' => ['A' => 'Very confident', 'B' => 'A little nervous but improving', 'C' => 'Shy but willing to try', 'D' => 'Still learning how to express'], 'points' => ['A'=>4, 'B'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'What motivates you to keep posting consistently?', 'options' => ['A' => 'Passion for audience', 'B' => 'Desire to grow', 'C' => 'Love for creativity', 'D' => 'Personal discipline'], 'points' => ['D'=>4, 'A'=>3, 'B'=>2, 'C'=>1]],

        // SECTION B
        ['text' => 'What type of content do you enjoy creating the most?', 'options' => ['A' => 'Short reels', 'B' => 'Long informative videos', 'C' => 'Photoshoot-based posts', 'D' => 'Stories & daily updates'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'What is your content creation style?', 'options' => ['A' => 'Aesthetic & clean', 'B' => 'Fun & interactive', 'C' => 'Informative & structured', 'D' => 'Bold & expressive'], 'points' => ['C'=>4, 'D'=>3, 'B'=>2, 'A'=>1]],
        ['text' => 'What do you believe your audience values most?', 'options' => ['A' => 'Authenticity', 'B' => 'Creativity', 'C' => 'Value-based content', 'D' => 'Entertainment'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'What is your biggest challenge in content creation?', 'options' => ['A' => 'Consistency', 'B' => 'Ideas', 'C' => 'Editing', 'D' => 'Confidence'], 'points' => ['A'=>4, 'B'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'Which platform do you feel most connected with?', 'options' => ['A' => 'Instagram', 'B' => 'YouTube', 'C' => 'TikTok / Reels', 'D' => 'LinkedIn'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],

        // SECTION C
        ['text' => 'What is the first step in growing your social media account?', 'options' => ['A' => 'Define niche', 'B' => 'Post consistently', 'C' => 'Engage with audience', 'D' => 'Use trends wisely'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'What is the most powerful growth tool?', 'options' => ['A' => 'Reels/Short videos', 'B' => 'Collaborations', 'C' => 'Hashtags', 'D' => 'Story engagement'], 'points' => ['A'=>4, 'B'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'Which collaboration do you prefer?', 'options' => ['A' => 'Brands with similar audience', 'B' => 'Other influencers', 'C' => 'Startups looking for visibility', 'D' => 'Local businesses'], 'points' => ['A'=>4, 'B'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'How do you measure your growth?', 'options' => ['A' => 'Followers', 'B' => 'Engagement rate', 'C' => 'Reach', 'D' => 'Saves & shares'], 'points' => ['B'=>4, 'D'=>3, 'C'=>2, 'A'=>1]],
        ['text' => 'If a post goes viral, what do you do next?', 'options' => ['A' => 'Create similar content', 'B' => 'Engage with comments', 'C' => 'Plan a trend-based series', 'D' => 'Analyze why it performed well'], 'points' => ['D'=>4, 'A'=>3, 'C'=>2, 'B'=>1]],

        // SECTION D
        ['text' => 'How do you respond to hate comments?', 'options' => ['A' => 'Ignore', 'B' => 'Reply politely', 'C' => 'Block if needed', 'D' => 'Use it as motivation'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'What’s most important in building trust online?', 'options' => ['A' => 'Transparency', 'B' => 'Honesty', 'C' => 'Consistency', 'D' => 'Authentic personality'], 'points' => ['D'=>4, 'A'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'What type of brand deal will you reject?', 'options' => ['A' => 'Misaligned with values', 'B' => 'Poor product quality', 'C' => 'Misleading claims', 'D' => 'All of the above'], 'points' => ['D'=>4, 'C'=>3, 'B'=>2, 'A'=>1]],
        ['text' => 'How will you maintain authenticity?', 'options' => ['A' => 'Show real moments', 'B' => 'Share honest reviews', 'C' => 'Don’t fake lifestyle', 'D' => 'All of the above'], 'points' => ['D'=>4, 'A'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'What defines a good influencer?', 'options' => ['A' => 'Positive impact', 'B' => 'Loyal audience', 'C' => 'Strong content', 'D' => 'Clear message'], 'points' => ['A'=>4, 'B'=>3, 'D'=>2, 'C'=>1]],

        // SECTION E
        ['text' => 'Where do you see yourself as an influencer in 2 years?', 'options' => ['A' => 'Growing rapidly', 'B' => 'Earning consistently', 'C' => 'Strong brand collaborations', 'D' => 'Inspiring many people'], 'points' => ['D'=>4, 'C'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'What skill do you want to improve?', 'options' => ['A' => 'Editing', 'B' => 'Creativity', 'C' => 'Speaking skills', 'D' => 'Trend analysis'], 'points' => ['C'=>4, 'D'=>3, 'B'=>2, 'A'=>1]],
        ['text' => 'How do you learn new influencer strategies?', 'options' => ['A' => 'Watching tutorials', 'B' => 'Following top creators', 'C' => 'Experimenting', 'D' => 'Feedback from audience'], 'points' => ['C'=>4, 'D'=>3, 'B'=>2, 'A'=>1]],
        ['text' => 'What is your long-term goal as an influencer?', 'options' => ['A' => 'Build a personal brand', 'B' => 'Start my own business', 'C' => 'Become a full-time influencer', 'D' => 'Become a role model'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'Why should people follow you?', 'options' => ['A' => 'I add value', 'B' => 'I entertain', 'C' => 'I inspire', 'D' => 'I relate to their life'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]]
    ],

    'Content Creator' => [
        // SECTION A: PERSONAL BRAND & SELF-AWARENESS
        ['text' => 'Why do you want to become a Content Creator?', 'options' => ['A' => 'To inspire and add value', 'B' => 'To express my creativity', 'C' => 'To build a personal brand', 'D' => 'To gain recognition and growth'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'Which content type suits your natural personality?', 'options' => ['A' => 'Fashion & Lifestyle', 'B' => 'Entertainment & Humor', 'C' => 'Motivation & Education', 'D' => 'Daily Vlogs / Relatable content'], 'points' => ['C'=>4, 'D'=>3, 'B'=>2, 'A'=>1]],
        ['text' => 'What makes you different from other creators?', 'options' => ['A' => 'Unique personality', 'B' => 'My creativity', 'C' => 'My storytelling', 'D' => 'My consistency'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'How comfortable are you in front of a camera?', 'options' => ['A' => 'Very confident', 'B' => 'Confident with retakes', 'C' => 'Nervous but improving', 'D' => 'Not confident, but willing to learn'], 'points' => ['A'=>4, 'B'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'What’s your biggest strength as a potential creator?', 'options' => ['A' => 'Creativity', 'B' => 'Consistency', 'C' => 'Communication skills', 'D' => 'Problem-solving'], 'points' => ['B'=>4, 'A'=>3, 'C'=>2, 'D'=>1]],

        // SECTION B: CONTENT CREATION SKILLS
        ['text' => 'What type of content do you enjoy creating the most?', 'options' => ['A' => 'Reels / Shorts', 'B' => 'Long videos', 'C' => 'Photoshoot content', 'D' => 'Daily stories'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'What do you think your audience will love the most?', 'options' => ['A' => 'Authenticity', 'B' => 'Creativity', 'C' => 'Relatable moments', 'D' => 'Informative value'], 'points' => ['D'=>4, 'A'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'What’s your biggest challenge in content creation?', 'options' => ['A' => 'Finding new ideas', 'B' => 'Staying consistent', 'C' => 'Editing & technical work', 'D' => 'Being confident on camera'], 'points' => ['B'=>4, 'A'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'Which platform aligns most with your style?', 'options' => ['A' => 'Instagram', 'B' => 'YouTube', 'C' => 'TikTok / Reels', 'D' => 'LinkedIn (professional niche)'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'How would you describe your editing skill level?', 'options' => ['A' => 'Beginner', 'B' => 'Intermediate', 'C' => 'Good with apps', 'D' => 'Strong and creative'], 'points' => ['D'=>4, 'C'=>3, 'B'=>2, 'A'=>1]],

        // SECTION C: GROWTH STRATEGY & ONLINE PRESENCE
        ['text' => 'What matters most when building an audience?', 'options' => ['A' => 'Consistency', 'B' => 'Engagement', 'C' => 'Value-rich content', 'D' => 'Authentic presence'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What is the most powerful growth tool today?', 'options' => ['A' => 'Reels / Short videos', 'B' => 'Collaborations', 'C' => 'Trends', 'D' => 'Hashtags & SEO'], 'points' => ['A'=>4, 'D'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'Which type of collaboration do you prefer?', 'options' => ['A' => 'Brands relevant to your niche', 'B' => 'Other influencers', 'C' => 'Startups', 'D' => 'Local small businesses'], 'points' => ['A'=>4, 'B'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'How do you measure your growth?', 'options' => ['A' => 'Followers', 'B' => 'Saves & Shares', 'C' => 'Engagement rate', 'D' => 'Reactions & comments'], 'points' => ['C'=>4, 'B'=>3, 'D'=>2, 'A'=>1]],
        ['text' => 'What will you do if one of your videos goes viral?', 'options' => ['A' => 'Make similar content', 'B' => 'Engage heavily with the audience', 'C' => 'Analyze what worked', 'D' => 'Create a planned content series'], 'points' => ['C'=>4, 'D'=>3, 'B'=>2, 'A'=>1]],

        // SECTION D: ETHICS, VALUES & PUBLIC IMAGE
        ['text' => 'How will you deal with negativity or hate?', 'options' => ['A' => 'Ignore', 'B' => 'Reply politely', 'C' => 'Block toxic users', 'D' => 'Use it as motivation'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'What makes a creator “trustworthy”?', 'options' => ['A' => 'Authenticity', 'B' => 'Honesty', 'C' => 'Transparency', 'D' => 'Consistency'], 'points' => ['C'=>4, 'B'=>3, 'D'=>2, 'A'=>1]],
        ['text' => 'What kind of brand deal would you reject?', 'options' => ['A' => 'Misaligned with your values', 'B' => 'Misleading products', 'C' => 'Poor-quality brand', 'D' => 'All of the above'], 'points' => ['D'=>4, 'A'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'How will you maintain your authenticity?', 'options' => ['A' => 'Show real moments', 'B' => 'Share honest opinions', 'C' => 'Don’t fake lifestyle', 'D' => 'All of the above'], 'points' => ['D'=>4, 'A'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'What is your biggest responsibility as a creator?', 'options' => ['A' => 'Positive impact', 'B' => 'Honest content', 'C' => 'Value creation', 'D' => 'Ethical promotions'], 'points' => ['C'=>4, 'A'=>3, 'B'=>2, 'D'=>1]],

        // SECTION E: FUTURE VISION & MINDSET
        ['text' => 'Where do you see yourself in 2 years as a creator?', 'options' => ['A' => 'Posting consistently', 'B' => 'Working with brands', 'C' => 'Growing a loyal audience', 'D' => 'Becoming full-time influencer'], 'points' => ['D'=>4, 'C'=>3, 'B'=>2, 'A'=>1]],
        ['text' => 'Which skill do you want to improve?', 'options' => ['A' => 'Speaking skills', 'B' => 'Editing skills', 'C' => 'Creativity and ideas', 'D' => 'Analytics understanding'], 'points' => ['D'=>4, 'C'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'How do you learn new trends or strategies?', 'options' => ['A' => 'Watching top creators', 'B' => 'Online tutorials', 'C' => 'Experimenting', 'D' => 'Following growth coaches'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What is your long-term creator goal?', 'options' => ['A' => 'Build my personal brand', 'B' => 'Start my own business', 'C' => 'Become a recognized influencer', 'D' => 'Inspire millions'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'Why should people follow you?', 'options' => ['A' => 'I inspire', 'B' => 'I entertain', 'C' => 'I educate', 'D' => 'I relate'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]]
    ],

    'Marketing' => [
        // SECTION A
        ['text' => 'Why do you want to build a career in marketing?', 'options' => ['A' => 'I enjoy understanding customer behavior', 'B' => 'I like creative communication', 'C' => 'I want to help brands grow', 'D' => 'Marketing has unlimited career scope'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'Which marketing skill fits your personality most?', 'options' => ['A' => 'Creativity', 'B' => 'Strategy', 'C' => 'Communication', 'D' => 'Analysis'], 'points' => ['D'=>4, 'B'=>3, 'C'=>2, 'A'=>1]],
        ['text' => 'What motivates you the most in marketing work?', 'options' => ['A' => 'Solving customer needs', 'B' => 'Building engaging campaigns', 'C' => 'Understanding trends', 'D' => 'Helping brands stand out'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What type of marketing excites you?', 'options' => ['A' => 'Digital marketing', 'B' => 'Social media marketing', 'C' => 'Brand marketing', 'D' => 'Content marketing'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What is your biggest strength as a marketing candidate?', 'options' => ['A' => 'Creativity', 'B' => 'Observation', 'C' => 'Storytelling', 'D' => 'Analytical thinking'], 'points' => ['D'=>4, 'B'=>3, 'C'=>2, 'A'=>1]],

        // SECTION B
        ['text' => 'What is the most important element of marketing?', 'options' => ['A' => 'Understanding customer needs', 'B' => 'Creativity in campaign design', 'C' => 'Smart brand positioning', 'D' => 'Right communication'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What does “value proposition” mean to you?', 'options' => ['A' => 'Unique reason customers choose the brand', 'B' => 'Benefits offered', 'C' => 'Difference from competitors', 'D' => 'All of the above'], 'points' => ['D'=>4, 'A'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'Which marketing tool are you most confident in?', 'options' => ['A' => 'Social media', 'B' => 'SEO / SEM', 'C' => 'Content writing', 'D' => 'Basic analytics'], 'points' => ['D'=>4, 'B'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'What helps a brand stand out in a crowded market?', 'options' => ['A' => 'Clear message', 'B' => 'Strong visuals', 'C' => 'Consistency', 'D' => 'Customer trust'], 'points' => ['D'=>4, 'A'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'What is your preferred way to learn new marketing skills?', 'options' => ['A' => 'Online courses', 'B' => 'Observing top brands', 'C' => 'Practical projects', 'D' => 'Reading case studies'], 'points' => ['C'=>4, 'D'=>3, 'A'=>2, 'B'=>1]],

        // SECTION C
        ['text' => 'If a product isn’t selling, what do you check first?', 'options' => ['A' => 'Target audience', 'B' => 'Pricing', 'C' => 'Promotion strategy', 'D' => 'Product quality'], 'points' => ['A'=>4, 'D'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'If your campaign has low engagement, what is your approach?', 'options' => ['A' => 'Analyze audience insights', 'B' => 'Try new creatives', 'C' => 'Test different timings', 'D' => 'Change communication style'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'A brand needs more visibility. Your top approach?', 'options' => ['A' => 'Social media campaigns', 'B' => 'Influencer collaboration', 'C' => 'Google ads', 'D' => 'Content marketing'], 'points' => ['D'=>4, 'C'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'A competitor launches a better offer. Your reaction?', 'options' => ['A' => 'Analyze their strategy', 'B' => 'Improve your messaging', 'C' => 'Add more value', 'D' => 'Reposition the campaign'], 'points' => ['C'=>4, 'D'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'What makes a marketing campaign successful?', 'options' => ['A' => 'Creativity', 'B' => 'Right targeting', 'C' => 'Consistency', 'D' => 'Emotional connection'], 'points' => ['B'=>4, 'D'=>3, 'C'=>2, 'A'=>1]],

        // SECTION D
        ['text' => 'How do you communicate with customers effectively?', 'options' => ['A' => 'Understand their pain points', 'B' => 'Use simple language', 'C' => 'Listen actively', 'D' => 'Stay honest'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'What makes marketing communication powerful?', 'options' => ['A' => 'Clear message', 'B' => 'Strong storytelling', 'C' => 'Emotional impact', 'D' => 'Relatable examples'], 'points' => ['C'=>4, 'B'=>3, 'A'=>2, 'D'=>1]],
        ['text' => 'What’s the best way to build trust with customers?', 'options' => ['A' => 'Honest communication', 'B' => 'Transparency', 'C' => 'Customer engagement', 'D' => 'Providing value'], 'points' => ['D'=>4, 'B'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'What defines a strong brand personality?', 'options' => ['A' => 'Consistency', 'B' => 'Tone of voice', 'C' => 'Visual identity', 'D' => 'Brand values'], 'points' => ['D'=>4, 'A'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'What type of content works best today?', 'options' => ['A' => 'Short videos', 'B' => 'Informative posts', 'C' => 'Relatable stories', 'D' => 'Interactive content'], 'points' => ['C'=>4, 'D'=>3, 'A'=>2, 'B'=>1]],

        // SECTION E
        ['text' => 'Where do you see yourself in marketing in 3 years?', 'options' => ['A' => 'Creative strategist', 'B' => 'Digital marketing specialist', 'C' => 'Brand manager', 'D' => 'Content lead'], 'points' => ['C'=>4, 'A'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'Which marketing skill do you want to improve first?', 'options' => ['A' => 'Copywriting', 'B' => 'Analytics', 'C' => 'Creativity', 'D' => 'Campaign planning'], 'points' => ['B'=>4, 'D'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'What motivates you to stay updated with marketing trends?', 'options' => ['A' => 'Curiosity', 'B' => 'Creativity', 'C' => 'Passion for branding', 'D' => 'Growth mindset'], 'points' => ['D'=>4, 'A'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'What do you expect from your marketing team?', 'options' => ['A' => 'Clear communication', 'B' => 'Supportive collaboration', 'C' => 'Continuous learning', 'D' => 'Creative freedom'], 'points' => ['C'=>4, 'B'=>3, 'A'=>2, 'D'=>1]],
        ['text' => 'Why should a company hire you for marketing?', 'options' => ['A' => 'I understand customer behavior', 'B' => 'I think creatively', 'C' => 'I communicate effectively', 'D' => 'I bring energy + fresh ideas'], 'points' => ['A'=>4, 'D'=>3, 'B'=>2, 'C'=>1]]
    ],

    'Customer Support' => [
        // SECTION A
        ['text' => 'What inspired you to choose Customer Support as your career?', 'options' => ['A' => 'I enjoy helping people', 'B' => 'I like solving problems', 'C' => 'I want a communication-focused role', 'D' => 'I enjoy working in a service-oriented environment'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'What do you enjoy most about interacting with customers?', 'options' => ['A' => 'Understanding their issues', 'B' => 'Helping them feel valued', 'C' => 'Offering solutions', 'D' => 'Turning negative situations positive'], 'points' => ['D'=>4, 'C'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'How would you describe your communication style?', 'options' => ['A' => 'Clear and polite', 'B' => 'Calm and patient', 'C' => 'Direct but respectful', 'D' => 'Friendly yet professional'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'Which personal value aligns most with Customer Support?', 'options' => ['A' => 'Patience', 'B' => 'Empathy', 'C' => 'Honesty', 'D' => 'Accountability'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'How do you stay calm when customers are upset?', 'options' => ['A' => 'Listen without interrupting', 'B' => 'Focus on resolving the issue', 'C' => 'Control emotions and stay neutral', 'D' => 'Take a deep breath before responding'], 'points' => ['C'=>4, 'A'=>3, 'B'=>2, 'D'=>1]],

        // SECTION B
        ['text' => 'Which area of Customer Support interests you most?', 'options' => ['A' => 'Live Chat Support', 'B' => 'Email Support', 'C' => 'Phone Support', 'D' => 'Technical Support'], 'points' => ['D'=>4, 'A'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'What do you think is the main role of Customer Support?', 'options' => ['A' => 'Solve customer problems', 'B' => 'Represent the company positively', 'C' => 'Collect customer feedback', 'D' => 'Provide timely assistance'], 'points' => ['A'=>4, 'D'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'What does “customer satisfaction” mean to you?', 'options' => ['A' => 'Customer feels heard', 'B' => 'Customer gets a solution', 'C' => 'Customer builds trust in the company', 'D' => 'Customer leaves with a positive experience'], 'points' => ['D'=>4, 'C'=>3, 'B'=>2, 'A'=>1]],
        ['text' => 'How familiar are you with customer handling tools?', 'options' => ['A' => 'Very familiar', 'B' => 'Basic understanding', 'C' => 'Used only a few tools', 'D' => 'Still learning'], 'points' => ['A'=>4, 'B'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'What does confidentiality mean in customer support?', 'options' => ['A' => 'Protecting customer data', 'B' => 'Not sharing personal information', 'C' => 'Handling sensitive information carefully', 'D' => 'All of the above'], 'points' => ['D'=>4, 'A'=>3, 'C'=>2, 'B'=>1]],

        // SECTION C
        ['text' => 'A customer is angry and shouting. What do you do first?', 'options' => ['A' => 'Let them express their feelings', 'B' => 'Calm them down politely', 'C' => 'Apologize and assure help', 'D' => 'Understand the root cause'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'A customer demands a refund immediately. What is your approach?', 'options' => ['A' => 'Check refund policy', 'B' => 'Understand the full issue', 'C' => 'Offer alternatives if possible', 'D' => 'Escalate if required'], 'points' => ['B'=>4, 'A'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'First step when responding to a support ticket?', 'options' => ['A' => 'Greet the customer', 'B' => 'Understand the issue', 'C' => 'Ask clarifying questions', 'D' => 'Check previous history'], 'points' => ['B'=>4, 'D'=>3, 'C'=>2, 'A'=>1]],
        ['text' => 'A customer keeps asking repeated questions. What do you do?', 'options' => ['A' => 'Answer clearly again', 'B' => 'Break info into simple steps', 'C' => 'Share visuals or guides', 'D' => 'Confirm understanding'], 'points' => ['B'=>4, 'C'=>3, 'D'=>2, 'A'=>1]],
        ['text' => 'Customer complains about slow service. Your response?', 'options' => ['A' => 'Apologize sincerely', 'B' => 'Explain reason politely', 'C' => 'Reassure quick resolution', 'D' => 'Improve service speed'], 'points' => ['C'=>4, 'A'=>3, 'B'=>2, 'D'=>1]],

        // SECTION D
        ['text' => 'How do you take feedback from supervisors?', 'options' => ['A' => 'As a chance to improve', 'B' => 'Calmly and openly', 'C' => 'By asking for details', 'D' => 'By applying it immediately'], 'points' => ['D'=>4, 'A'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'Best way to solve a customer\'s problem?', 'options' => ['A' => 'Listen actively', 'B' => 'Ask the right questions', 'C' => 'Stay patient and clear', 'D' => 'Offer practical solutions'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'How do you build trust with customers?', 'options' => ['A' => 'Be consistent', 'B' => 'Respond honestly', 'C' => 'Stay polite', 'D' => 'Keep promises'], 'points' => ['D'=>4, 'B'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'Most important quality for Customer Support?', 'options' => ['A' => 'Patience', 'B' => 'Empathy', 'C' => 'Clear communication', 'D' => 'Responsibility'], 'points' => ['B'=>4, 'A'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'How would you handle a confused customer?', 'options' => ['A' => 'Ask simple questions', 'B' => 'Explain step-by-step', 'C' => 'Reassure them calmly', 'D' => 'Offer easier alternatives'], 'points' => ['B'=>4, 'D'=>3, 'A'=>2, 'C'=>1]],

        // SECTION E
        ['text' => 'Where do you see yourself in Customer Support in 3 years?', 'options' => ['A' => 'Senior Support Executive', 'B' => 'Team Leader', 'C' => 'Support Trainer', 'D' => 'Quality Analyst (QA)'], 'points' => ['B'=>4, 'D'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'Which CS skill do you want to improve?', 'options' => ['A' => 'Communication', 'B' => 'Technical troubleshooting', 'C' => 'Multi-tasking', 'D' => 'Problem-solving'], 'points' => ['D'=>4, 'B'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'How do you stay updated with customer support trends?', 'options' => ['A' => 'Watching industry videos', 'B' => 'Learning from teammates', 'C' => 'Reading blogs & articles', 'D' => 'Observing customer patterns'], 'points' => ['D'=>4, 'C'=>3, 'B'=>2, 'A'=>1]],
        ['text' => 'What do you expect from your support team?', 'options' => ['A' => 'Collaboration', 'B' => 'Clear communication', 'C' => 'Helpful guidance', 'D' => 'Positive environment'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'Why should a company hire you for Customer Support?', 'options' => ['A' => 'I stay calm under pressure', 'B' => 'I communicate clearly', 'C' => 'I enjoy helping others', 'D' => 'I handle challenging customers well'], 'points' => ['D'=>4, 'A'=>3, 'B'=>2, 'C'=>1]]
    ],

    'Finance & Accounts' => [
        // SECTION A
        ['text' => 'Why do you want a career in Finance & Accounts?', 'options' => ['A' => 'I enjoy working with numbers', 'B' => 'I like analyzing financial data', 'C' => 'Finance offers stability & respect', 'D' => 'I want to help businesses make smart decisions'], 'points' => ['D'=>4, 'B'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'Which skill best describes your personality?', 'options' => ['A' => 'Accuracy', 'B' => 'Analysis', 'C' => 'Patience', 'D' => 'Discipline'], 'points' => ['A'=>4, 'B'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'What motivates you most in finance work?', 'options' => ['A' => 'Problem-solving', 'B' => 'Maintaining accuracy', 'C' => 'Financial planning', 'D' => 'Understanding business performance'], 'points' => ['D'=>4, 'A'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'What is your working style?', 'options' => ['A' => 'Detail-oriented', 'B' => 'Logical & structured', 'C' => 'Organized under deadlines', 'D' => 'Methodical & consistent'], 'points' => ['A'=>4, 'B'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'Which financial area interests you most?', 'options' => ['A' => 'Accounting', 'B' => 'Taxation', 'C' => 'Financial analysis', 'D' => 'Budgeting & planning'], 'points' => ['C'=>4, 'D'=>3, 'A'=>2, 'B'=>1]],

        // SECTION B
        ['text' => 'What is most important in accounting?', 'options' => ['A' => 'Accuracy', 'B' => 'Transparency', 'C' => 'Double-entry knowledge', 'D' => 'Documentation'], 'points' => ['A'=>4, 'B'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'What does a balance sheet show?', 'options' => ['A' => 'Assets, liabilities & equity', 'B' => 'Income & expenses', 'C' => 'Cash inflow/outflow', 'D' => 'Profit earned'], 'points' => ['A'=>4, 'D'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'What makes financial statements reliable?', 'options' => ['A' => 'Proper audit', 'B' => 'Following accounting standards', 'C' => 'Accurate data entry', 'D' => 'Clear notes & disclosures'], 'points' => ['B'=>4, 'A'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'What is your approach when numbers don’t match?', 'options' => ['A' => 'Recheck entries', 'B' => 'Trace every step', 'C' => 'Verify ledgers', 'D' => 'Find root-cause calmly'], 'points' => ['D'=>4, 'B'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'Which accounting tool are you most confident with?', 'options' => ['A' => 'Excel', 'B' => 'Tally', 'C' => 'ERP systems', 'D' => 'Google Sheets'], 'points' => ['C'=>4, 'A'=>3, 'B'=>2, 'D'=>1]],

        // SECTION C
        ['text' => 'What is the first step in financial analysis?', 'options' => ['A' => 'Understanding data', 'B' => 'Comparing with previous periods', 'C' => 'Checking accuracy', 'D' => 'Identifying key ratios'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'If expenses suddenly increase, what will you check first?', 'options' => ['A' => 'Expense category', 'B' => 'Vendor invoices', 'C' => 'Error in entry', 'D' => 'Budget deviation'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'A company’s profit decreases — your first action?', 'options' => ['A' => 'Review cost structure', 'B' => 'Analyze sales performance', 'C' => 'Check outstanding payments', 'D' => 'Compare with last period'], 'points' => ['A'=>4, 'B'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'What shows a company’s financial health best?', 'options' => ['A' => 'Profit margin', 'B' => 'Cash flow', 'C' => 'Debt ratio', 'D' => 'Working capital'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'What is the most important habit for finance professionals?', 'options' => ['A' => 'Accuracy', 'B' => 'Confidentiality', 'C' => 'Documentation', 'D' => 'Continuous learning'], 'points' => ['A'=>4, 'B'=>3, 'D'=>2, 'C'=>1]],

        // SECTION D
        ['text' => 'If you find a small financial error, what do you do?', 'options' => ['A' => 'Fix it immediately', 'B' => 'Check full report for more errors', 'C' => 'Notify supervisor if required', 'D' => 'Document changes'], 'points' => ['B'=>4, 'A'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'What builds trust in the finance team?', 'options' => ['A' => 'Transparency', 'B' => 'Accountability', 'C' => 'Accuracy', 'D' => 'Confidentiality'], 'points' => ['C'=>4, 'B'=>3, 'A'=>2, 'D'=>1]],
        ['text' => 'What is your reaction under deadlines?', 'options' => ['A' => 'Stay calm', 'B' => 'Organize tasks', 'C' => 'Prioritize critical reports', 'D' => 'Work with focus'], 'points' => ['C'=>4, 'B'=>3, 'D'=>2, 'A'=>1]],
        ['text' => 'If your manager disagrees with your analysis, what do you do?', 'options' => ['A' => 'Explain with data', 'B' => 'Ask questions', 'C' => 'Recheck assumptions', 'D' => 'Find middle-ground'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'What is the biggest responsibility of finance?', 'options' => ['A' => 'Maintaining accuracy', 'B' => 'Protecting company assets', 'C' => 'Supporting business decisions', 'D' => 'Following compliance'], 'points' => ['C'=>4, 'B'=>3, 'A'=>2, 'D'=>1]],

        // SECTION E
        ['text' => 'Where do you see yourself in finance in 3 years?', 'options' => ['A' => 'Senior Accountant', 'B' => 'Financial Analyst', 'C' => 'Tax Consultant', 'D' => 'Finance Manager'], 'points' => ['B'=>4, 'D'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'Which skill do you want to improve first?', 'options' => ['A' => 'Advanced Excel', 'B' => 'Taxation', 'C' => 'Financial reporting', 'D' => 'Budgeting'], 'points' => ['C'=>4, 'D'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'How do you stay updated in finance & accounting?', 'options' => ['A' => 'Following finance news', 'B' => 'Online courses', 'C' => 'Watching analysis videos', 'D' => 'Learning during work'], 'points' => ['D'=>4, 'A'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'What do you expect from your finance team?', 'options' => ['A' => 'Clarity and organization', 'B' => 'Ethical work culture', 'C' => 'Knowledge sharing', 'D' => 'Team support'], 'points' => ['B'=>4, 'A'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'Why should a company hire you for Finance & Accounts?', 'options' => ['A' => 'I maintain accuracy', 'B' => 'I handle numbers confidently', 'C' => 'I learn quickly', 'D' => 'I work with discipline'], 'points' => ['A'=>4, 'D'=>3, 'B'=>2, 'C'=>1]]
    ],

    'Administration / Management' => [
        // SECTION A
        ['text' => 'What inspired you to choose Administration/Management as your career?', 'options' => ['A' => 'I enjoy organizing and coordinating tasks', 'B' => 'I like handling responsibilities', 'C' => 'I want to support smooth operations', 'D' => 'I prefer managing people and processes'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What do you enjoy most about administrative work?', 'options' => ['A' => 'Keeping things structured', 'B' => 'Solving day-to-day issues', 'C' => 'Interacting with teams', 'D' => 'Ensuring everything runs smoothly'], 'points' => ['D'=>4, 'A'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'How would you describe your working style?', 'options' => ['A' => 'Organized and systematic', 'B' => 'Calm and controlled', 'C' => 'Proactive and responsible', 'D' => 'Flexible and adaptive'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'Which personal value aligns most with Administration/Management?', 'options' => ['A' => 'Discipline', 'B' => 'Accountability', 'C' => 'Time management', 'D' => 'Reliability'], 'points' => ['B'=>4, 'D'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'How do you handle unexpected situations?', 'options' => ['A' => 'Stay calm and analyze', 'B' => 'Prioritize tasks quickly', 'C' => 'Communicate with the right people', 'D' => 'Find a practical solution'], 'points' => ['D'=>4, 'B'=>3, 'A'=>2, 'C'=>1]],

        // SECTION B
        ['text' => 'Which administrative area interests you the most?', 'options' => ['A' => 'Office management', 'B' => 'Operations support', 'C' => 'Team coordination', 'D' => 'Documentation & records'], 'points' => ['B'=>4, 'A'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'What is the main role of an administrator/manager?', 'options' => ['A' => 'Ensure smooth workflow', 'B' => 'Support teams and departments', 'C' => 'Handle processes and policies', 'D' => 'Solve operational issues'], 'points' => ['A'=>4, 'D'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'What does “operational efficiency” mean to you?', 'options' => ['A' => 'Work completed on time', 'B' => 'Minimum errors in tasks', 'C' => 'Smooth coordination', 'D' => 'Optimal use of resources'], 'points' => ['D'=>4, 'C'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'How familiar are you with office tools?', 'options' => ['A' => 'Very familiar', 'B' => 'Basic understanding', 'C' => 'Used some tools', 'D' => 'Still learning'], 'points' => ['A'=>4, 'B'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'What does confidentiality mean in admin?', 'options' => ['A' => 'Protecting staff information', 'B' => 'Keeping sensitive data secure', 'C' => 'Not discussing internal matters', 'D' => 'All of the above'], 'points' => ['D'=>4, 'B'=>3, 'A'=>2, 'C'=>1]],

        // SECTION C
        ['text' => 'A staff member complains about missing information. What do you do?', 'options' => ['A' => 'Check the documentation', 'B' => 'Clarify details with departments', 'C' => 'Provide accurate info ASAP', 'D' => 'Prevent the issue from repeating'], 'points' => ['D'=>4, 'C'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'A task is urgent but your schedule is full. Your approach?', 'options' => ['A' => 'Re-prioritize tasks', 'B' => 'Ask for support', 'C' => 'Inform manager immediately', 'D' => 'Complete high-impact tasks first'], 'points' => ['A'=>4, 'D'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'A new employee joins. What is your first step?', 'options' => ['A' => 'Guide them through onboarding', 'B' => 'Explain basic rules', 'C' => 'Provide resources and tools', 'D' => 'Introduce them to the team'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'A team is facing delays. What do you do?', 'options' => ['A' => 'Understand the cause', 'B' => 'Reorganize tasks', 'C' => 'Offer support or resources', 'D' => 'Communicate solutions'], 'points' => ['A'=>4, 'D'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'A senior manager disagrees with your plan. What do you do?', 'options' => ['A' => 'Explain reasoning calmly', 'B' => 'Listen to their perspective', 'C' => 'Adjust plan accordingly', 'D' => 'Find a balanced solution'], 'points' => ['D'=>4, 'A'=>3, 'B'=>2, 'C'=>1]],

        // SECTION D
        ['text' => 'How do you take feedback from supervisors?', 'options' => ['A' => 'As a learning opportunity', 'B' => 'Calmly and openly', 'C' => 'Ask for clarity', 'D' => 'Apply improvements quickly'], 'points' => ['A'=>4, 'D'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'Best way to handle team coordination?', 'options' => ['A' => 'Clear communication', 'B' => 'Sharing responsibilities', 'C' => 'Regular follow-ups', 'D' => 'Planning tasks wisely'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'How do you build trust with staff?', 'options' => ['A' => 'Be consistent', 'B' => 'Communicate clearly', 'C' => 'Stay dependable', 'D' => 'Maintain fairness'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'Most important quality for Management/Admin?', 'options' => ['A' => 'Time management', 'B' => 'Problem-solving', 'C' => 'Decision-making', 'D' => 'Leadership attitude'], 'points' => ['B'=>4, 'D'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'How would you handle a conflict between two team members?', 'options' => ['A' => 'Listen to both sides', 'B' => 'Understand the root issue', 'C' => 'Stay neutral and fair', 'D' => 'Offer a practical solution'], 'points' => ['B'=>4, 'D'=>3, 'C'=>2, 'A'=>1]],

        // SECTION E
        ['text' => 'Where do you see yourself in 3 years?', 'options' => ['A' => 'Admin Executive', 'B' => 'Operations Coordinator', 'C' => 'Office Manager', 'D' => 'Team Leader/Supervisor'], 'points' => ['B'=>4, 'D'=>3, 'C'=>2, 'A'=>1]],
        ['text' => 'Which admin skill do you want to improve?', 'options' => ['A' => 'Time management', 'B' => 'Decision-making', 'C' => 'Task delegation', 'D' => 'Communication'], 'points' => ['B'=>4, 'C'=>3, 'D'=>2, 'A'=>1]],
        ['text' => 'How do you stay updated in administration/management?', 'options' => ['A' => 'Learning from seniors', 'B' => 'Watching leadership videos', 'C' => 'Reading management blogs', 'D' => 'Understanding workplace patterns'], 'points' => ['D'=>4, 'C'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'What do you expect from your admin/management team?', 'options' => ['A' => 'Cooperation', 'B' => 'Clear communication', 'C' => 'Good planning', 'D' => 'Supportive environment'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'Why should a company hire you for Administration/Management?', 'options' => ['A' => 'I stay organized', 'B' => 'I handle pressure well', 'C' => 'I support teams effectively', 'D' => 'I think logically and responsibly'], 'points' => ['C'=>4, 'D'=>3, 'A'=>2, 'B'=>1]]
    ],

    // ADDED: UI/UX Designer (Based on the same logic pattern)
    'UI/UX Designer' => [
         // A
        ['text' => 'When someone says “Make the design more user-friendly,” what do you THINK they actually mean?', 'options' => ['A'=>'Make it cleaner', 'B'=>'Make it simpler', 'C'=>'Make it prettier', 'D'=>'Make it understandable for their grandma'], 'points' => ['D'=>4, 'B'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'What frustrates you the MOST in UI/UX?', 'options' => ['A'=>'Clients wanting aesthetic over usability', 'B'=>'Developers not following design', 'C'=>'Users behaving unpredictably', 'D'=>'Stakeholders giving vague feedback'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What part of UX research drains you the most?', 'options' => ['A'=>'User interviews', 'B'=>'Data analysis', 'C'=>'Usability testing', 'D'=>'Creating personas'], 'points' => ['B'=>4, 'A'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'What’s your biggest UI insecurity?', 'options' => ['A'=>'My layouts look basic', 'B'=>'My color palettes are safe', 'C'=>'My typography choices feel boring', 'D'=>'My visual style lacks identity'], 'points' => ['A'=>4, 'D'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'When you see a beautiful interface online, what do you FEEL first?', 'options' => ['A'=>'Inspired', 'B'=>'Jealous', 'C'=>'Curious', 'D'=>'Pressured'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],
         // B
        ['text' => 'What’s the hardest UX problem for YOU?', 'options' => ['A'=>'Understanding real user pain', 'B'=>'Creating simple flows', 'C'=>'Getting clean feedback', 'D'=>'Convincing clients why UX matters'], 'points' => ['A'=>4, 'B'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'What makes UI/UX mentally exhausting for you?', 'options' => ['A'=>'Too many revisions', 'B'=>'Too many opinions', 'C'=>'Too many research steps', 'D'=>'Too many design iterations'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'What is your design instinct naturally drawn to?', 'options' => ['A'=>'Minimalism', 'B'=>'Colorful visuals', 'C'=>'Functional layouts', 'D'=>'Trendy aesthetics'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'When a stakeholder says “This looks empty,” you think—', 'options' => ['A'=>'“It’s negative space.”', 'B'=>'“They don’t understand design.”', 'C'=>'“Should I add something?”', 'D'=>'“Let me explain the UX logic.”'], 'points' => ['D'=>4, 'A'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'What is your REAL weakness in UI/UX?', 'options' => ['A'=>'Research depth', 'B'=>'UX writing', 'C'=>'Visual creativity', 'D'=>'Design systems'], 'points' => ['A'=>4, 'D'=>3, 'B'=>2, 'C'=>1]],
         // C
        ['text' => 'When testing with real users, what scares you MOST?', 'options' => ['A'=>'They won’t understand the flow', 'B'=>'They’ll criticize the UI', 'C'=>'They’ll find flaws I missed', 'D'=>'They’ll get confused'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'Your natural UX thinking style is—', 'options' => ['A'=>'Analytical', 'B'=>'Empathetic', 'C'=>'Creative', 'D'=>'Strategic'], 'points' => ['B'=>4, 'D'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'Which part of UI/UX gives you the MOST pressure?', 'options' => ['A'=>'Wireframes', 'B'=>'Final visuals', 'C'=>'Prototyping', 'D'=>'Presentations'], 'points' => ['C'=>4, 'D'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'What do you OVERTHINK the most while designing?', 'options' => ['A'=>'Spacing', 'B'=>'Colors', 'C'=>'Buttons', 'D'=>'Icons'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'When your design gets rejected, what hits you hardest?', 'options' => ['A'=>'Effort wasted', 'B'=>'Creativity doubted', 'C'=>'Logic ignored', 'D'=>'Confidence shaken'], 'points' => ['C'=>4, 'B'=>3, 'A'=>2, 'D'=>1]],
         // D
        ['text' => 'What kind of UX task is the HARDEST for you?', 'options' => ['A'=>'Building user journeys', 'B'=>'Creating wireframes', 'C'=>'Conducting interviews', 'D'=>'Crafting information architecture'], 'points' => ['D'=>4, 'A'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'What makes you FEEL like a real UI/UX designer?', 'options' => ['A'=>'Creating smooth flows', 'B'=>'Designing clean screens', 'C'=>'Fixing pain points', 'D'=>'Seeing users smile'], 'points' => ['C'=>4, 'D'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'What annoys you the MOST about non-designers?', 'options' => ['A'=>'“Can you make it pop?”', 'B'=>'“Why so much spacing?”', 'C'=>'“Use more colors!”', 'D'=>'“UI and UX are same right?”'], 'points' => ['D'=>4, 'A'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'Which part of UI/UX do you secretly AVOID?', 'options' => ['A'=>'Heuristic evaluation', 'B'=>'UX research', 'C'=>'UX writing', 'D'=>'Design systems'], 'points' => ['B'=>4, 'D'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'What scares you MOST about real users?', 'options' => ['A'=>'They behave unexpectedly', 'B'=>'They don’t read text', 'C'=>'They pick random paths', 'D'=>'They give brutal feedback'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]],
         // E
        ['text' => 'What UI element do you mess up MOST often?', 'options' => ['A'=>'Buttons', 'B'=>'Spacing', 'C'=>'Typography', 'D'=>'Colours'], 'points' => ['C'=>4, 'B'=>3, 'D'=>2, 'A'=>1]],
        ['text' => 'When someone rushes your design, what happens internally?', 'options' => ['A'=>'Creativity dies', 'B'=>'Stress rises', 'C'=>'Details suffer', 'D'=>'Quality drops'], 'points' => ['C'=>4, 'D'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'What is your biggest UX blind spot?', 'options' => ['A'=>'Over-complicating flows', 'B'=>'Under-researching', 'C'=>'Over-designing', 'D'=>'Ignoring micro-details'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'Your dream UI/UX goal is—', 'options' => ['A'=>'Work at a big tech company', 'B'=>'Build beautiful interfaces', 'C'=>'Solve real user problems', 'D'=>'Start your own design studio'], 'points' => ['C'=>4, 'D'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'What gives you REAL satisfaction as a designer?', 'options' => ['A'=>'Minimalist layout', 'B'=>'Clean typography', 'C'=>'Smooth experience', 'D'=>'User happiness'], 'points' => ['D'=>4, 'C'=>3, 'A'=>2, 'B'=>1]]
    ],
    
    'Graphic Design' => [
        // Section A
        ['text' => 'When starting a new design, what overwhelms you the MOST?', 'options' => ['A'=>'Finding the right concept', 'B'=>'Picking the color palette', 'C'=>'Choosing fonts', 'D'=>'Making everything look balanced'], 'points' => ['A'=>4, 'D'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'What do you secretly struggle with as a designer?', 'options' => ['A'=>'Self-doubt', 'B'=>'Consistency', 'C'=>'Creative block', 'D'=>'Overthinking layouts'], 'points' => ['C'=>4, 'D'=>3, 'B'=>2, 'A'=>1]],
        ['text' => 'Which type of client stresses you the MOST?', 'options' => ['A'=>'“Make it POP” client', 'B'=>'“Do magic” client', 'C'=>'“I’ll know it when I see it” client', 'D'=>'“My cousin can also design” client'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'When someone says “It looks simple,” what do you FEEL?', 'options' => ['A'=>'Proud', 'B'=>'Offended', 'C'=>'Misunderstood', 'D'=>'Annoyed'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What part of the design process drains you the MOST?', 'options' => ['A'=>'Brainstorming concepts', 'B'=>'Visual execution', 'C'=>'Feedback revisions', 'D'=>'Final polishing'], 'points' => ['C'=>4, 'A'=>3, 'B'=>2, 'D'=>1]],
        // Section B
        ['text' => 'What’s your biggest DESIGN insecurity?', 'options' => ['A'=>'My layouts look repetitive', 'B'=>'My color sense isn’t strong', 'C'=>'My ideas aren’t innovative', 'D'=>'My design doesn’t stand out'], 'points' => ['C'=>4, 'D'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'When a client rejects your design after 8 hours of work, your REAL reaction is—', 'options' => ['A'=>'Rewrite the brief', 'B'=>'Rework the concept', 'C'=>'Lose confidence', 'D'=>'Rage silently'], 'points' => ['A'=>4, 'B'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'What’s your hidden design WEAKNESS?', 'options' => ['A'=>'Typography', 'B'=>'Color selection', 'C'=>'Consistency', 'D'=>'Creative direction'], 'points' => ['D'=>4, 'C'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'What fuels your creativity the MOST?', 'options' => ['A'=>'Music', 'B'=>'Pinterest', 'C'=>'Nature', 'D'=>'Pressure'], 'points' => ['C'=>4, 'B'=>3, 'A'=>2, 'D'=>1]],
        ['text' => 'When a design looks “almost good,” you…', 'options' => ['A'=>'Keep refining', 'B'=>'Leave it as it is', 'C'=>'Restart', 'D'=>'Ask someone’s feedback'], 'points' => ['A'=>4, 'D'=>3, 'C'=>2, 'B'=>1]],
        // Section C
        ['text' => 'What part of designing gives you silent anxiety?', 'options' => ['A'=>'Blank canvas', 'B'=>'Deadline pressure', 'C'=>'Client expectations', 'D'=>'Feedback sessions'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What type of project SCARES you?', 'options' => ['A'=>'Branding', 'B'=>'UI design', 'C'=>'Social media', 'D'=>'Big posters'], 'points' => ['A'=>4, 'B'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'Your design identity is closest to—', 'options' => ['A'=>'Minimalist', 'B'=>'Bold & experimental', 'C'=>'Trend-driven', 'D'=>'Clean and aesthetic'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'What frustrates you the MOST in design software?', 'options' => ['A'=>'Too many tools', 'B'=>'Slow system', 'C'=>'Export issues', 'D'=>'Fonts not loading'], 'points' => ['B'=>4, 'C'=>3, 'A'=>2, 'D'=>1]],
        ['text' => 'When you compare your work to top designers, you feel—', 'options' => ['A'=>'Inspired', 'B'=>'Pressured', 'C'=>'Inferior', 'D'=>'Motivated'], 'points' => ['D'=>4, 'A'=>3, 'B'=>2, 'C'=>1]],
        // Section D
        ['text' => 'What makes you doubt your design skills?', 'options' => ['A'=>'Too many revisions', 'B'=>'Low engagement', 'C'=>'Self-comparison', 'D'=>'Creative block'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What’s the REAL reason you enjoy designing?', 'options' => ['A'=>'It expresses me', 'B'=>'It feels aesthetic', 'C'=>'It makes me feel skilled', 'D'=>'It gives me peace'], 'points' => ['A'=>4, 'C'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'What design element do you overthink the MOST?', 'options' => ['A'=>'Color palette', 'B'=>'Typography', 'C'=>'Spacing', 'D'=>'Composition'], 'points' => ['D'=>4, 'B'=>3, 'C'=>2, 'A'=>1]],
        ['text' => 'Which phrase annoys you the MOST?', 'options' => ['A'=>'“Make it modern”', 'B'=>'“Make it attractive”', 'C'=>'“Make it premium”', 'D'=>'“Make it unique”'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        ['text' => 'Which design flaws do you judge in others\' work?', 'options' => ['A'=>'Bad font selection', 'B'=>'Poor alignment', 'C'=>'Clashing colors', 'D'=>'Over-crowded layout'], 'points' => ['B'=>4, 'A'=>3, 'D'=>2, 'C'=>1]],
        // Section E
        ['text' => 'What blocks your creativity instantly?', 'options' => ['A'=>'Constant interruptions', 'B'=>'Strict brief', 'C'=>'Too much freedom', 'D'=>'Lack of clarity'], 'points' => ['D'=>4, 'B'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'Your mind works BEST when—', 'options' => ['A'=>'You’re calm', 'B'=>'You’re stressed', 'C'=>'You’re inspired', 'D'=>'You’re under pressure'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What’s the hardest part of being a designer?', 'options' => ['A'=>'Revising same thing repeatedly', 'B'=>'Dealing with clueless clients', 'C'=>'Staying original', 'D'=>'Staying updated'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What’s your design superpower?', 'options' => ['A'=>'Creativity', 'B'=>'Aesthetics', 'C'=>'Precision', 'D'=>'Versatility'], 'points' => ['D'=>4, 'A'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'What’s your long-term design dream?', 'options' => ['A'=>'Build a portfolio people admire', 'B'=>'Work with big brands', 'C'=>'Create iconic designs', 'D'=>'Start your own studio'], 'points' => ['C'=>4, 'D'=>3, 'B'=>2, 'A'=>1]]
    ],

    // DIGITAL MARKETING (Added to ensure coverage)
    'Digital Marketing' => [
        // Section A
        ['text' => 'Why do you want to pursue Digital Marketing?', 'options' => ['A'=>'I love online platforms', 'B'=>'I want a creative + technical career', 'C'=>'I enjoy understanding user behavior', 'D'=>'Digital marketing has big future scope'], 'points' => ['C'=>4, 'B'=>3, 'D'=>2, 'A'=>1]],
        ['text' => 'Which digital marketing area interests you the most?', 'options' => ['A'=>'Social Media Marketing', 'B'=>'SEO / Google Ranking', 'C'=>'Paid Ads (Google/Facebook)', 'D'=>'Content Marketing'], 'points' => ['B'=>4, 'C'=>3, 'D'=>2, 'A'=>1]],
        ['text' => 'What motivates you most in digital marketing?', 'options' => ['A'=>'Seeing results in real time', 'B'=>'Creating engaging content', 'C'=>'Understanding analytics', 'D'=>'Helping brands grow'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What is your strongest personal trait?', 'options' => ['A'=>'Creativity', 'B'=>'Logical thinking', 'C'=>'Communication', 'D'=>'Consistency'], 'points' => ['B'=>4, 'D'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'How do you prefer working?', 'options' => ['A'=>'Creating content', 'B'=>'Analyzing data', 'C'=>'Planning strategies', 'D'=>'Managing social pages'], 'points' => ['C'=>4, 'B'=>3, 'A'=>2, 'D'=>1]],
        // Section B
        ['text' => 'What is most important in SEO?', 'options' => ['A'=>'Keywords', 'B'=>'Content quality', 'C'=>'Backlinks', 'D'=>'User experience'], 'points' => ['D'=>4, 'B'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'What type of content performs the best online?', 'options' => ['A'=>'Short videos', 'B'=>'Informational blogs', 'C'=>'Infographics', 'D'=>'Relatable stories'], 'points' => ['A'=>4, 'D'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'What is the purpose of hashtags?', 'options' => ['A'=>'Increase visibility', 'B'=>'Reach targeted audience', 'C'=>'Categorize content', 'D'=>'All of the above'], 'points' => ['B'=>4, 'A'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'What makes a social media page grow fast?', 'options' => ['A'=>'Consistency', 'B'=>'Creative content', 'C'=>'Engagement with audience', 'D'=>'Using trends wisely'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'What is the most important part of content marketing?', 'options' => ['A'=>'Providing value', 'B'=>'Maintaining consistency', 'C'=>'Strong storytelling', 'D'=>'Solving customer pain points'], 'points' => ['D'=>4, 'A'=>3, 'C'=>2, 'B'=>1]],
        // Section C
        ['text' => 'What matters most in paid ads?', 'options' => ['A'=>'Targeting', 'B'=>'Budget', 'C'=>'Creative', 'D'=>'Copywriting'], 'points' => ['A'=>4, 'C'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'How do you measure campaign success?', 'options' => ['A'=>'Click-through rate', 'B'=>'Conversions', 'C'=>'Engagement', 'D'=>'ROI'], 'points' => ['D'=>4, 'B'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'If your ad is getting clicks but no conversions, what do you check?', 'options' => ['A'=>'Landing page', 'B'=>'Target audience', 'C'=>'Ad creative', 'D'=>'Offer quality'], 'points' => ['A'=>4, 'D'=>3, 'B'=>2, 'C'=>1]],
        ['text' => 'A client wants faster results. Your approach?', 'options' => ['A'=>'Run paid ads', 'B'=>'Improve website', 'C'=>'Create a campaign strategy', 'D'=>'Use influencer marketing'], 'points' => ['C'=>4, 'A'=>3, 'B'=>2, 'D'=>1]],
        ['text' => 'What is the most powerful digital marketing skill today?', 'options' => ['A'=>'Video editing', 'B'=>'Data analytics', 'C'=>'Copywriting', 'D'=>'Trend understanding'], 'points' => ['B'=>4, 'D'=>3, 'C'=>2, 'A'=>1]],
        // Section D
        ['text' => 'Your post has low reach. What do you do?', 'options' => ['A'=>'Change hashtags', 'B'=>'Try trending audio', 'C'=>'Post at a better time', 'D'=>'Improve content quality'], 'points' => ['D'=>4, 'B'=>3, 'C'=>2, 'A'=>1]],
        ['text' => 'What defines effective communication in digital marketing?', 'options' => ['A'=>'Clear message', 'B'=>'Emotional connection', 'C'=>'Persuasive tone', 'D'=>'Simple language'], 'points' => ['B'=>4, 'A'=>3, 'C'=>2, 'D'=>1]],
        ['text' => 'If a client disagrees with your strategy, what do you do?', 'options' => ['A'=>'Explain with data', 'B'=>'Offer alternatives', 'C'=>'Understand their viewpoint', 'D'=>'Test both approaches'], 'points' => ['A'=>4, 'D'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'What builds trust online?', 'options' => ['A'=>'Consistency', 'B'=>'Transparency', 'C'=>'Value-rich content', 'D'=>'All of the above'], 'points' => ['D'=>4, 'B'=>3, 'A'=>2, 'C'=>1]],
        ['text' => 'What makes a brand memorable?', 'options' => ['A'=>'Unique voice', 'B'=>'Strong visuals', 'C'=>'Storytelling', 'D'=>'Customer experience'], 'points' => ['D'=>4, 'C'=>3, 'A'=>2, 'B'=>1]],
        // Section E
        ['text' => 'Where do you see yourself in digital marketing in 3 years?', 'options' => ['A'=>'Social media manager', 'B'=>'SEO specialist', 'C'=>'Paid ads expert', 'D'=>'Full Digital Marketing Manager'], 'points' => ['D'=>4, 'C'=>3, 'B'=>2, 'A'=>1]],
        ['text' => 'Which skill do you want to improve first?', 'options' => ['A'=>'SEO', 'B'=>'Content writing', 'C'=>'Video creation', 'D'=>'Analytics'], 'points' => ['D'=>4, 'A'=>3, 'C'=>2, 'B'=>1]],
        ['text' => 'How do you keep yourself updated?', 'options' => ['A'=>'Following marketing blogs', 'B'=>'Watching tutorials', 'C'=>'Observing top brands', 'D'=>'Testing strategies'], 'points' => ['D'=>4, 'C'=>3, 'A'=>2, 'B'=>1]],
        ['text' => 'What do you expect from your digital marketing team?', 'options' => ['A'=>'Creativity', 'B'=>'Good communication', 'C'=>'Learning culture', 'D'=>'Supportive teamwork'], 'points' => ['C'=>4, 'A'=>3, 'D'=>2, 'B'=>1]],
        ['text' => 'Why should a company choose you for digital marketing?', 'options' => ['A'=>'I think creatively', 'B'=>'I understand customers', 'C'=>'I learn fast', 'D'=>'I deliver results with consistency'], 'points' => ['D'=>4, 'B'=>3, 'C'=>2, 'A'=>1]]
    ]
];

echo "<h3>Populating Database...</h3>";

// Clear Tables
$conn->query("SET FOREIGN_KEY_CHECKS=0");
$conn->query("TRUNCATE TABLE assessment_options");
$conn->query("TRUNCATE TABLE assessment_questions");
$conn->query("SET FOREIGN_KEY_CHECKS=1");

foreach ($all_categories as $cat_name => $questions) {
    $q_count = 0;
    foreach ($questions as $q_data) {
        $q_count++;
        $text = $conn->real_escape_string($q_data['text']);
        
        // Logic to assign sections based on Question Number
        // 1-5: A (Motivation), 6-10: B (Knowledge), 11-15: C (Practical), 16-20: D (Behavior), 21-25: E (Goals)
        $section = 'A';
        if ($q_count > 5) $section = 'B';
        if ($q_count > 10) $section = 'C';
        if ($q_count > 15) $section = 'D';
        if ($q_count > 20) $section = 'E';

        $sql_q = "INSERT INTO assessment_questions (category, section, question_text) VALUES ('$cat_name', '$section', '$text')";
        if ($conn->query($sql_q)) {
            $q_id = $conn->insert_id;
            
            foreach ($q_data['options'] as $label => $opt_text) {
                $opt_text = $conn->real_escape_string($opt_text);
                
                // Fetch points for this specific option
                // Default to 0 if not found (safety check)
                $p = isset($q_data['points'][$label]) ? $q_data['points'][$label] : 1;
                
                $conn->query("INSERT INTO assessment_options (question_id, option_text, option_label, points) VALUES ($q_id, '$opt_text', '$label', $p)");
            }
        }
    }
    echo "Inserted $q_count questions for <b>$cat_name</b>.<br>";
}
echo "<h3>Success! Database is ready. You can delete this file now.</h3>";
?>
import sys, json
with open(r'C:/Users/ADMIN/.gemini/antigravity-ide/brain/8c530f9d-f6ff-4b20-91b3-25f15fc820b6/.system_generated/logs/transcript_full.jsonl', 'r', encoding='utf-8') as f:
    for l in f:
        data = json.loads(l)
        if data.get('type') == 'VIEW_FILE' and 'opd/dashboard.blade.php' in data.get('content', '') and 'Showing lines 150' in data.get('content', ''):
            sys.stdout.buffer.write(data['content'].encode('utf-8'))
            sys.stdout.buffer.write(b'\n================\n')

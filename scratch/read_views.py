import sys
with open(r'd:\KodingProject\Project-KP-Beasiswa-Kab-Blitar\scratch\all_views.txt', 'r', encoding='utf-16') as f:
    text = f.read()
    sys.stdout.buffer.write(text.encode('utf-8'))

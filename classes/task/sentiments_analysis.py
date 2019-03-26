from textblob import TextBlob
import os
import sys
import codecs
from reportlab.pdfgen import canvas
from reportlab.lib.pagesizes import letter, A4

if len(sys.argv) != 2:
    print("Usage: python sentiments_analysis.py <directory>")
    exit(0)

directory = str(sys.argv[1])

if (not os.path.isdir(directory)) or (not os.path.exists(directory)):
    print("Directory %s is not valid.", directory)
    exit(0)


def print_report(report_name, sentiments_list):
    save_name = os.path.join(directory, report_name + ".pdf")
    rep = canvas.Canvas(save_name, pagesize=letter)
    width, height = letter  # keep for later
    y = height - 100
    for sentiment in sentiments_list:
        # Parse file name to get student username
        rep.drawString(100, y, "Student: ")
        for name, value in sentiment.__dict__.items():
            y = y - 15
            rep.drawString(125, y, str(name))
            y = y - 15
            rep.drawString(125, y, str(value))
        y = y - 100
    rep.save()


sentiments = list()

for filename in os.listdir(directory):
    if filename.endswith(".txt"):
        print(filename)
        filename = os.path.join(directory, filename)

        line = ""
        with codecs.open(filename, "r",encoding='utf-8', errors='ignore') as fdata:
            line += fdata.read()

            line = TextBlob(line)
            sentiments.append(line.sentiment_assessments)

print_report("output", sentiments)
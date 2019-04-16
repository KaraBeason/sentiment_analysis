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


def is_new_page(report, y, value, height):
    if y - 100 < 0:
        report.showPage()
        y = height - 100
        return y
    else:
        y = y - value
        return y


def print_report(report_name, sentiments_list, overall):
    save_name = os.path.join(directory, report_name + ".pdf")
    rep = canvas.Canvas(save_name, pagesize=letter)
    width, height = letter 
    y = height - 100
    # # Overall Sentiment Analysis First
    rep.drawString(100, y, "Overall Sentiment: ")
    y = is_new_page(rep, y, 15, height)
    for label, val in overall.__dict__.items():
        y = is_new_page(rep, y, 15, height)
        rep.drawString(125, y, str(label))
        y = is_new_page(rep, y, 15, height)
        if isinstance(val, float):
            rep.drawString(125, y, str(val))
    rep.showPage()
    # Sentiment Analysis by Student
    for user, sentiment in sentiments_list.iteritems():
        y = height - 100
        rep.drawString(100, y, "Student Username: ")
        rep.drawString(225, y, user)
        for name, value in sentiment.__dict__.items():
            y = is_new_page(rep, y, 15, height)
            rep.drawString(125, y, str(name))
            y = is_new_page(rep, y, 15, height)

            if isinstance(value, float):
                rep.drawString(125, y, str(value))
            else:
                for word in value:
                    y = is_new_page(rep, y, 15, height)
                    rep.drawString(125, y, str(word))
        rep.showPage()
    rep.save()


sentiments = dict()
overall = ""
for filename in os.listdir(directory):
    i = 1
    if filename.endswith(".txt"):
        username = filename.split('_')[0]
        filename = os.path.join(directory, filename)
        line = ""
        with codecs.open(filename, "r",encoding='utf-8', errors='ignore') as fdata:
            line += fdata.read()
            overall += line
            line = TextBlob(line)
            sentiments[username] = line.sentiment_assessments
        i += 1
overall = TextBlob(overall)
overall_sentiment = overall.sentiment_assessments
print_report("output", sentiments, overall)
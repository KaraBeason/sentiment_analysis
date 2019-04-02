from textblob import TextBlob
import os
import sys
import codecs
from reportlab.pdfgen import canvas
from reportlab.platypus import PageBreak
from reportlab.lib.pagesizes import letter, A4

if len(sys.argv) != 2:
    print("Usage: python sentiments_analysis.py <directory>")
    exit(0)

directory = str(sys.argv[1])

if (not os.path.isdir(directory)) or (not os.path.exists(directory)):
    print("Directory %s is not valid.", directory)
    exit(0)


def is_new_page(report, y, value, height):
    if y - value < 0:
        report.showPage()
        y = height - 100
        return y
    else:
        y = y - value
        return y


def print_report(report_name, sentiments_list):
    save_name = os.path.join(os.path.expanduser("~"), "Desktop/", report_name + ".pdf")
    rep = canvas.Canvas(save_name, pagesize=letter)
    width, height = letter  # keep for later
    y = height - 100
    for user, sentiment in sentiments_list.iteritems():
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

    rep.save()


sentiments = dict()
for filename in os.listdir(directory):
    if filename.endswith(".txt"):
        print(filename)
        username = filename.split('_')[0]
        print username
        filename = os.path.join(directory, filename)
        # blob = file_obj.read()
        # file_obj = open(filename, "r+", encoding="utf-8")
        line = ""
        with codecs.open(filename, "r",encoding='utf-8', errors='ignore') as fdata:
            line += fdata.read()
            # print(line)

            line = TextBlob(line)
            sentiments[username] = line.sentiment_assessments

print_report("test", sentiments)

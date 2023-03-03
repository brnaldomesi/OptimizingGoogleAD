from __future__ import division

import math


class Confidence(object):
    """
    Returns the statistical significance of two sets of numbers (e.g. clicks and impressions)
    Add the losing ad as the control when comparing winning and losing ads
    """

    def __init__(self, control, treatment):
        # ,control_number_visitors, control_number_clicks, treatment_number_visitors,treatment_number_clicks
        # self.control_number_visitors = control_number_visitors
        # self.control_number_clicks = control_number_clicks
        # self.treatment_number_visitors = treatment_number_visitors
        # self.treatment_number_clicks = treatment_number_clicks
        self.control = control  # array e.g. [1000,10]
        self.treatment = treatment  # array e.g. [1000,10]

    # cr: calculation of the Ctr
    # zscore: calculation of the z-score
    # cumnormdist: calculation of the cumulative normal distribution
    # ssize: Given a Ctr, calculate a recommended sample size
    #        E.g: 0.25 worst, 0.15, 0.05 best at a 95% confidence.

    def calculate(self):

        control_number_visitors = self.control[0]
        control_number_clicks = self.control[1]

        treatment_number_visitors = self.treatment[0]
        treatment_number_clicks = self.treatment[1]

        c = [control_number_visitors, control_number_clicks]
        tA = [treatment_number_visitors, treatment_number_clicks]

        # cr: calculation of the Ctr
        # zscore: calculation of the z-score
        # cumnormdist: calculation of the cumulative normal distribution
        # ssize: Given a Ctr, calculate a recommended sample size
        #        E.g: 0.25 worst, 0.15, 0.05 best at a 95% confidence.

        def cr(t):
            return t[1] / t[0]

        def getZScore(c, t):
            z = cr(t) - cr(c)
            s = (cr(t) * (1 - cr(t))) / t[0] + (cr(c) * (1 - cr(c))) / c[0];
            s = math.sqrt(s)
            return z / s

        def cumnormdist(x):
            b1 = 0.319381530
            b2 = -0.356563782
            b3 = 1.781477937
            b4 = -1.821255978
            b5 = 1.330274429
            p = 0.2316419
            c = 0.39894228

            if x >= 0.0:
                t = 1.0 / (1.0 + p * x)
                return (1.0 - c * math.exp(-x * x / 2.0) * t * (t * (t * (t * (t * b5 + b4) + b3) + b2) + b1));

            else:
                t = 1.0 / (1.0 - p * x)
                return (c * math.exp(-x * x / 2.0) * t * (t * (t * (t * (t * b5 + b4) + b3) + b2) + b1));

        def ssize(conv):
            a = 3.84145882689
            res = []
            bs = [0.0625, 0.0225, 0.0025]
            for b in bs:
                res = (int)((1 - conv) * a / (b * conv))

            return res

        # control_number_visitors = self.control_number_visitors
        # control_number_clicks = self.control_number_clicks
        # treatment_number_visitors = self.treatment_number_visitors
        # treatment_number_clicks = self.treatment_number_clicks

        if min(control_number_visitors, control_number_clicks, treatment_number_visitors, treatment_number_clicks) == 0:
            return 0
        if control_number_clicks >= control_number_visitors:
            return 0
        if treatment_number_clicks > treatment_number_visitors:
            return 0
        # print "min: %s" %(min(control_number_visitors, control_number_clicks, treatment_number_visitors, treatment_number_clicks),)
        c = [control_number_visitors, control_number_clicks]
        tA = [treatment_number_visitors, treatment_number_clicks]
        # Calculate Ctrs.
        c_conversion_rate = (control_number_clicks / control_number_visitors) * 100
        tA_conversion_rate = (treatment_number_clicks / treatment_number_visitors) * 100
        c_conversion_rate = str(c_conversion_rate) + '%'
        tA_conversion_rate = str(tA_conversion_rate) + '%'

        # The z-score is ... [explain]
        zscore = getZScore(c, tA)

        # Calculate the 'cumulative normal distribution' (confidence ratio)
        confidence = cumnormdist(zscore)

        # If the 'confidence interval is >95%', the test is statistically significant.
        confidence_as_percentage = confidence * 100

        # Pad the strings for output
        # cV = str_pad(control_number_visitors, 16, ' ', STR_PAD_BOTH);
        # cC = str_pad(control_number_clicks, 11, ' ', STR_PAD_BOTH);

        # tV = str_pad(treatment_number_visitors, 16, ' ', STR_PAD_BOTH);
        # tC = str_pad(treatment_number_clicks, 11, ' ', STR_PAD_BOTH);

        # cr_c = str_pad(sprintf('%0.2f', c_conversion_rate), 15, ' ', STR_PAD_BOTH);
        # cr_t = str_pad(tA_conversion_rate, 15, ' ', STR_PAD_BOTH);

        # zs = str_pad(zscore, 15, ' ', STR_PAD_BOTH);

        cratio = str(confidence * 100) + '%'

        # print "Split and AB Testing Confidence Calculator<br><br>";
        # print "------------------------------------------<br><br>";

        """
        print "-------------------------------------------------------------------------------------------<br>";
        print "Control-------   Impressions: cV | Clicks: cC | Ctr: cr_c   <br>";
        print "Treatment---- Impressions: tV | Clicks: tC | Ctr: cr_t | Confidence: cratio        <br>";
        print "-------------------------------------------------------------------------------------------<br>";

        print "confidence: %s"%(confidence)
        print "cratio: %s"%(cratio)    
        print "c_conversion_rate: %s"%(c_conversion_rate)
        print "tA_conversion_rate: %s"%(tA_conversion_rate) 
        """
        return confidence_as_percentage

# control = [1000, 10]
# treatment = [500, 10]
# calc = Confidence(control, treatment)

# # print calc.calculate()

# Example #2:
# 270.000	198.000	4337.000	2506.000
# calculate(4337, 270, 2506, 198)

# print zscore(c, tA).' - '.cumnormdist(zscore(c, tA))."<br>";
# print zscore(c, tB).' - '.cumnormdist(zscore(c, tB))."<br>";
# print zscore(c, tC).' - '.cumnormdist(zscore(c, tC))."<br>";
# print '1.645 - '.cumnormdist(1.645)."<br>";

---
layout: post
title: "Everybody's First Linear Program - Cheap Eating"
date: 2018-07-05
math: true
---
This should be the only post I make relating to my own coursework.  It's easy and a common problem, but instructive and fun.

>
You have a list of food items you're willing to eat together with their nutritional information.  You have some nutritional requirements for yourself, and want to minimize the cost of your daily food intake subject to those requirements.
>

This can be formulated as a _linear program_: the daily amount of each food item you will eat is a variable, so that the cost and the total of each nutritional item are all linear functions of these variables.

 food item            | fat | Cals | fiber | iron | sodium | protein | carbs | COST |
----------------------|---|---|---|---|---|---|---|---|
 NutriGrain (1 bar)                 | 5 | 120 | 10 | 10 | 5 | 2 | 8 | 25 |
 raisin Granola (1 bar)          |  3 | 90   | 4    | 2   | 3 | 1 | 6 | 25 |
 bread (1 slice)                        |  1 | 70   | 4    | 4   | 4  | 3 | 4 | 10 |
 fruit/nut cereal (3/4 c)        | 6 | 210 | 20 | 50 | 6 | 4 | 14| 42 |
 peanut butter (2T)             | 18| 190| 12 | 4  | 10| 7 | 4 | 20 |
 lentils (1/4 c dry)                  | 0  | 70   | 36  | 15| 0 | 8  | 6 | 9.5 |
 split peas (1/4 c dry)            | 0 | 110 | 44 | 15 | 1 | 11| 9 | 13.5 |
 Nutella (2T)                            | 11 | 200| 6  | 4   | 1  | 3  | 7 | 34 |
 pumpkin puree (1/2 c)        | 0  |50    | 12 | 4   | 1 | 2  | 4 | 30 |
 egg (1)                                      | 7 | 70    | 0   | 4   | 2  | 6 |  0 | 13 |
 brown rice (1/4 c dry)         | 2 | 150  | 4   | 2   | 0  | 3 | 11| 17.5 |
 Goldfish crackers (30g)     | 8 | 140  | 7   | 6   | 10| 4 | 6  | 20 |
 applesauce (111g)              | 0 | 50     | 4   | 0   | 0   | 0 | 4  | 36 |
 boxed mac'n'cheese (1/3 box)           | 5 | 260   | 4   | 10 | 24|10|16| 33 |
 butter (1T)                              |17| 100 | 0   | 0   | 0   | 0  | 0 | 7.5 |
 heavy cream (1T)                  |9  | 60    | 0   | 0   | 0   | 0  | 0  | 9 |
 cheese (28g)                           |8 | 70     | 0   | 0   | 8  | 6   | 0  | 25 |
 Snickers (34g)                        |12| 160 | 4   | 0   | 4  | 3   | 7  | 27.5 |
 salt (1/4 t)                               |0   | 0       | 0  | 0   | 25| 0   | 0  | 0.2 |
 Ramen (1/2 package)          | 11| 190 | 4  | 8  | 32 | 5   | 9  | 12.5 |
 
All nutritional entries except for Calories and protein are measured in Percent Daily Values, as recommended on nutritional information panels; Calories are measured in Calories, and protein is measured in grams.  Cost is measured in cents per serving.  The recommended amount of protein for this purpose comes from [WebMD]({{'http://www.webmd.com/food-recipes/protein'}}).  The constraints were chosen as follows (the instructor of the course requested the higher sodium requirement).

$$
\begin{align*}
 100 &\leq \text{fat} \leq 200 \\
 2000 &\leq \text{Calories} \leq 2500 \\
 100 &\leq \text{fiber} \\
 100 &\leq \text{iron} \\
 150 &\leq \text{sodium} \leq 300 \\
 56 &\leq \text{protein}  \\
 100 &\leq \text{carbs} \leq 300
\end{align*}
$$

I ran the LP through SageMath, a program built on top of Python.
I ran it before including all 20 food items.  Including those up to the mac'n'cheese gave a solution that involved just cereal, peanut butter, and mac'n'cheese.  Thinking perhaps that these were chosen to get enough fat, I added butter, cream, cheese, and Snickers.  After these additions the solution was unchanged.  Then I thought perhaps the sodium was the culprit; I would have checked for slackness, but that didn't seem easy in Sage, so I just added table salt to the menu.  This gave rise to a solution involving lentils, eggs, mac'n'cheese, and salt.  (Interesting that peanut butter has dropped out!)  Finally I decided that the final menu item should be the old college staple, Ramen.


```python
sage: p = MixedIntegerLinearProgram(maximization=False)
sage: w = p.new_variable()
sage: n = [[ 5,120,10,10, 5, 2, 8],
...        [ 3, 90, 4, 2, 3, 1, 6],
...        [ 1, 70, 4, 4, 4, 2, 4],
...        [ 6,210,20,50, 6, 4,14],
...        [18,190,12, 4,10, 7, 4],
...        [ 0, 70,36,15, 0, 8, 6],
...        [ 0,110,44,15, 1,11, 9],
...        [11,200, 6, 4, 1, 3, 7],
...        [ 0, 50,12, 4, 1, 2, 4],
...        [ 7, 70, 0, 4, 2, 6, 0],
...        [ 2,150, 4, 2, 0, 3,11],
...        [ 8,140, 7, 6,10, 4, 6],
...        [ 0, 50, 4, 0, 0, 0, 4],
...        [ 5,260, 4,10,24,10,16],
...        [17,100, 0, 0, 0, 0, 0],
...        [ 9, 60, 0, 0, 0, 0, 0],
...        [ 8, 70, 0, 0, 8, 6, 0],
...        [12,160, 4, 0, 4, 3, 7],
...        [ 0,  0, 0, 0,25, 0, 0],
...        [11,190, 4, 8,32, 5, 9]]
...
sage: c = [25, 25, 10, 42, 20, 9.5, 13.5, 34, 30, 13, 17.5, 20, 36, 33, 7.5, 9, 25, 27.5, 0.2, 12.5]
sage: k=19 #=number of food items-1
sage: # total fat, as percent daily value
sage: p.add_constraint( sum([n[i][0]*w[i] for i in [0..k]]) >= 100 )
sage: p.add_constraint( sum([n[i][0]*w[i] for i in [0..k]]) <= 200 )
sage: # Calories
sage: p.add_constraint( sum([n[i][1]*w[i] for i in [0..k]]) >= 2000 )
sage: p.add_constraint( sum([n[i][1]*w[i] for i in [0..k]]) <= 2500 )
sage: # fiber, as PDV
sage: p.add_constraint( sum([n[i][2]*w[i] for i in [0..k]]) >= 100 )
sage: # iron, PDV
sage: p.add_constraint( sum([n[i][3]*w[i] for i in [0..k]]) >= 100 )
sage: # salt, PDV, desired 150% per instructions
sage: p.add_constraint( sum([n[i][4]*w[i] for i in [0..k]]) >= 150 )
sage: p.add_constraint( sum([n[i][4]*w[i] for i in [0..k]]) <= 300 )
sage: # protein, grams
sage: p.add_constraint( sum([n[i][5]*w[i] for i in [0..k]]) >= 56  )
sage: # carbs, PDV
sage: p.add_constraint( sum([n[i][6]*w[i] for i in [0..k]]) >= 100 )
sage: p.add_constraint( sum([n[i][6]*w[i] for i in [0..k]]) <= 300 )
sage: p.set_objective( sum([c[i]*w[i] for i in [0..k]]) )
sage: p.show()
Minimization:
  25.0 x_0 +25.0 x_1 +10.0 x_2 +42.0 x_3 +20.0 x_4 +9.5 x_5 +13.5 x_6 +34.0 x_7 +30.0 x_8 +13.0 x_9 +17.5 x_10 +20.0 x_11 +36.0 x_12 +33.0 x_13 +7.5 x_14 +9.0 x_15 +25.0 x_16 +27.5 x_17 +0.2 x_18 +12.5 x_19
Constraints:
  -5.0 x_0 -3.0 x_1 -x_2 -6.0 x_3 -18.0 x_4 -11.0 x_7 -7.0 x_9 -2.0 x_10 -8.0 x_11 -5.0 x_13 -17.0 x_14 -9.0 x_15 -8.0 x_16 -12.0 x_17 -11.0 x_19 <= -100.0
  5.0 x_0 +3.0 x_1 +x_2 +6.0 x_3 +18.0 x_4 +11.0 x_7 +7.0 x_9 +2.0 x_10 +8.0 x_11 +5.0 x_13 +17.0 x_14 +9.0 x_15 +8.0 x_16 +12.0 x_17 +11.0 x_19 <= 200.0
  -120.0 x_0 -90.0 x_1 -70.0 x_2 -210.0 x_3 -190.0 x_4 -70.0 x_5 -110.0 x_6 -200.0 x_7 -50.0 x_8 -70.0 x_9 -150.0 x_10 -140.0 x_11 -50.0 x_12 -260.0 x_13 -100.0 x_14 -60.0 x_15 -70.0 x_16 -160.0 x_17 -190.0 x_19 <= -2000.0
  120.0 x_0 +90.0 x_1 +70.0 x_2 +210.0 x_3 +190.0 x_4 +70.0 x_5 +110.0 x_6 +200.0 x_7 +50.0 x_8 +70.0 x_9 +150.0 x_10 +140.0 x_11 +50.0 x_12 +260.0 x_13 +100.0 x_14 +60.0 x_15 +70.0 x_16 +160.0 x_17 +190.0 x_19 <= 2500.0
  -10.0 x_0 -4.0 x_1 -4.0 x_2 -20.0 x_3 -12.0 x_4 -36.0 x_5 -44.0 x_6 -6.0 x_7 -12.0 x_8 -4.0 x_10 -7.0 x_11 -4.0 x_12 -4.0 x_13 -4.0 x_17 -4.0 x_19 <= -100.0
  -10.0 x_0 -2.0 x_1 -4.0 x_2 -50.0 x_3 -4.0 x_4 -15.0 x_5 -15.0 x_6 -4.0 x_7 -4.0 x_8 -4.0 x_9 -2.0 x_10 -6.0 x_11 -10.0 x_13 -8.0 x_19 <= -100.0
  -5.0 x_0 -3.0 x_1 -4.0 x_2 -6.0 x_3 -10.0 x_4 -x_6 -x_7 -x_8 -2.0 x_9 -10.0 x_11 -24.0 x_13 -8.0 x_16 -4.0 x_17 -25.0 x_18 -32.0 x_19 <= -150.0
  5.0 x_0 +3.0 x_1 +4.0 x_2 +6.0 x_3 +10.0 x_4 +x_6 +x_7 +x_8 +2.0 x_9 +10.0 x_11 +24.0 x_13 +8.0 x_16 +4.0 x_17 +25.0 x_18 +32.0 x_19 <= 300.0
  -2.0 x_0 -x_1 -2.0 x_2 -4.0 x_3 -7.0 x_4 -8.0 x_5 -11.0 x_6 -3.0 x_7 -2.0 x_8 -6.0 x_9 -3.0 x_10 -4.0 x_11 -10.0 x_13 -6.0 x_16 -3.0 x_17 -5.0 x_19 <= -56.0
  -8.0 x_0 -6.0 x_1 -4.0 x_2 -14.0 x_3 -4.0 x_4 -6.0 x_5 -9.0 x_6 -7.0 x_7 -4.0 x_8 -11.0 x_10 -6.0 x_11 -4.0 x_12 -16.0 x_13 -7.0 x_17 -9.0 x_19 <= -100.0
  8.0 x_0 +6.0 x_1 +4.0 x_2 +14.0 x_3 +4.0 x_4 +6.0 x_5 +9.0 x_6 +7.0 x_7 +4.0 x_8 +11.0 x_10 +6.0 x_11 +4.0 x_12 +16.0 x_13 +7.0 x_17 +9.0 x_19 <= 300.0
Variables:
  x_0 is a continuous variable (min=0.0, max=+oo)
  x_1 is a continuous variable (min=0.0, max=+oo)
  x_2 is a continuous variable (min=0.0, max=+oo)
  x_3 is a continuous variable (min=0.0, max=+oo)
  x_4 is a continuous variable (min=0.0, max=+oo)
  x_5 is a continuous variable (min=0.0, max=+oo)
  x_6 is a continuous variable (min=0.0, max=+oo)
  x_7 is a continuous variable (min=0.0, max=+oo)
  x_8 is a continuous variable (min=0.0, max=+oo)
  x_9 is a continuous variable (min=0.0, max=+oo)
  x_10 is a continuous variable (min=0.0, max=+oo)
  x_11 is a continuous variable (min=0.0, max=+oo)
  x_12 is a continuous variable (min=0.0, max=+oo)
  x_13 is a continuous variable (min=0.0, max=+oo)
  x_14 is a continuous variable (min=0.0, max=+oo)
  x_15 is a continuous variable (min=0.0, max=+oo)
  x_16 is a continuous variable (min=0.0, max=+oo)
  x_17 is a continuous variable (min=0.0, max=+oo)
  x_18 is a continuous variable (min=0.0, max=+oo)
  x_19 is a continuous variable (min=0.0, max=+oo)
sage: p.solve()
143.06214933103149
sage: for i, v in p.get_values(w).iteritems():
...       print 'w_%s = %s' % (i, v)
w_0 = 0.0
w_1 = 0.0
w_2 = 0.0
w_3 = 0.0
w_4 = 0.0
w_5 = 0.0
w_6 = 1.68321104877
w_7 = 0.0
w_8 = 0.0
w_9 = 0.0
w_10 = 0.0863185153215
w_11 = 0.0
w_12 = 0.0
w_13 = 0.0
w_14 = 0.306430729391
w_15 = 0.0
w_16 = 0.0
w_17 = 0.0
w_18 = 0.0
w_19 = 9.32239965473
```


The result?  I can eat for a measly $1.43 per day, by eating a bit under half a cup (measured dry) of lentils, a very small bit of egg, about 1/9 of a box of mac'n'cheese, and between 4 and 5 packages of Ramen noodles.

Yum.







Finally, linear programs come with their _dual_ program, which in this situation gives values to each nutrient.  Here's that code.
```python
sage: #Now, on to the DUAL PROBLEM:
sage: d = MixedIntegerLinearProgram()
sage: x = d.new_variable()
sage: for j in [0..19]:
...       d.add_constraint( sum( [ n[j][i]*x[i] for i in [0..6] ] ) <= n[j][0]*x[7] + n[j][1]*x[8] + n[j][4]*x[11] + n[j][6]*x[13] + c[j] )
...
sage: d.set_objective( 100*x[0] - 200*x[7] + 2000*x[1] - 2500*x[8] + 100*x[2]
...         + 100*x[3] + 150*x[4] - 300*x[11] + 56*x[5] + 100*x[6] - 300*x[13] )
...
sage: d.solve()
143.06214933103152
sage: for i, v in d.get_values(x).iteritems():
...       print 'x_%s = %s' % (i, v)
x_0 = 0.0
x_1 = 0.075
x_2 = 0.0
x_3 = 0.0266508416055
x_4 = 0.0
x_5 = 0.0
x_6 = 0.563336210617
x_7 = 0.0
x_8 = 0.0
x_11 = 0.219788519637
x_13 = 0.0
```
Again you see the optimal $1.43.  Here only Calories, iron, and carbohydrates should have positive cost, while sodium should have negative cost.  (The negative is allowed here since I have put an upper bound on the daily intake of sodium.)

Finally, I decided to see what happened after I removed the upper bounds (since these were not really requested in the problem).  In this version of the problem, I can save three cents, and should eat 2 teaspoons of peanut butter, 1/4 cup lentils, and nearly 10 packages of Ramen.
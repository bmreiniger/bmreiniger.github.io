---
layout: post
title: "Integer Programs and Nauty - Poset Dimension"
date: 2018-07-07
math: true
---
(Warning: the math involved in this problem is more advanced and niche than I'm discussing elsewhere.  I still want this post to focus on the programmatical aspect rather than the math, so I try to black-box most of the math here.)

>
How many subsets of \\([n]\\) can we include in a poset with dimension 2?
>

It's "obvious" that the answer is \\(1, 2, 4\\) for \\(n=0,1,2\\), and it's not hard to see that the answer is 7 when \\(n=3\\).  The dimension of a poset is not a particularly easy parameter to get one's mind around, so past this some computer experiments seemed prudent.

I tried the most naive thing first.  SageMath has some builtin utilities for poset dimension, so it's easy to write a script to run through all the families of \\(k\\) subsets of \\([n]\\) and compute the dimension, stopping if/when a dimension-2 family is produced.

That's awful:

1. The dimension is an invariant of the partial order on the sets we choose; in particular, renaming the elements of \\([n]\\) doesn't change things, so we're checking almost a factor of \\(n!\\) too many families.
2. As we increase \\(k\\) (remember, we're looking for the largest \\(k\\)), we're re-checking smaller posets (because dimension is monotone under containment).

Enter the graph package `nauty`.  We can encode our family of sets by a bipartite graph: one partite set represents the \\(n\\) elements of the underlying set, and the other partite set has \\(k\\) vertices representing our sets, a set vertex being adjacent to each element it contains.  Under this correspondence, no two vertices in the second partite set can have the same neighborhood.  By only caring about isomorphism classes (respecting which partite set is which, but not which individual vertices are which), we strip out the \\(n!\\) symmetry mentioned earlier.  And `nauty` is set up to do exactly that:
```bash
genbg -szl n k filename.txt
```
produces an output file containing an encoding of all the desired bipartite graphs.  (The option -s specifies the encoding, the -z enforces different neighborhoods in the second partite set, and the -l produces a canonical labeling.)  Now we pull this back into SageMath, reinterpret the bigraphs as posets, and ask whether these have dimension 2.

That fixes problem 1 above, but not problem 2.

We can move even more work to nauty.  The program builds up the desired graphs from scratch, stripping out automorphisms as it goes.  It allows us to define a pruning function: when a graph meets the criteria in the pruning function, neither that graph nor any of its descendents get output.  Since dimension is monotone, and poset containment corresponds to subgraphs in our bigraph model, pruning when we find a dimension-3 poset works to produce only the posets of dimension at most 2.



### Integer programs?
In SageMath, there is a class for posets, and it includes a function to return the dimension of the poset.  So in the early tests, I likely just tested for `P.dimension()<=2`.  (Computing the dimension can be phrased as a hypergraph coloring question, which in turn can be phrased as an integer program, and that is how it is implemented in SageMath.)

However, computing poset dimension is NP-hard, while testing specifically for dimension at most 2 is in P.  In fact, dimension at most 2 is equivalent to the comparability graph's complement also being a comparability graph, and _that_ test has poly-time algorithms.  Testing a graph for being a comparability graph has been implemented in SageMath, but only with two slow algorithms: an integer program, and a greedy partitioning algorithm.  (The documentation implies that someone intend(s/ed) to code something more efficient.)

When I moved to pruning inside nauty, I needed to be able to code a test for dimension at most 2 in C.  Since I was already familiar with Gurobi, I just implemented the integer program.  If it had seemed important, I would have tried to implement the greedy partitioning algorithm, or maybe even found a more efficient algorithm and implemented it in both C for this purpose and python for inclusion in the SageMath library. But...

### Conclusion
Testing small cases is only really helpful in the pure mathematics context for guessing the right answer.  Here's what we know from the experiments described above:

| \\(n\\) | 0 | 1 | 2 | 3 | 4 | 5 |
|---------|---|---|---|---|---|---|
|max size of poset with dimension at most 2| 1 | 2 | 4 | 7 | 12| 20|

And yes, those are recognizable: they are one less than the Fibonacci numbers!  And it turns out there's a good reason for that: there is a nice Fibonacci way to construct these posets of dimension 2, which I found by examining some of the solutions the program produced.

**But** that fails eventually.  The Fibonacci numbers grow exponentially, but with base the golden ratio (about 1.618).  Taking all the subsets of size \\(n/2\\) produces a poset of dimension 2, and when \\(n\\) is large enough, there are many more of these than the Fibonacci number.

So, I think having even a much longer collection of values computed is unlikely to help much in finding the correct answer.  We know now that the correct answer is \\(\Theta(\binom{n}{n/2})\\), and pinning down the constant is probably not easy just from example data.
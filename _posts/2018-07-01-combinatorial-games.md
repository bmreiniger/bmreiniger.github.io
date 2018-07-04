---
layout: post
title: "Combinatorial Games"
date: 2018-07-01
---
To kick things off, I thought I'd post some of my earliest research-oriented scripts.

In the summers of my degree, we had a large research group in combinatorics, with tons of problems presented and then worked on in small groups. A number of them were __combinatorial games__: games of perfect information in which 2 players take alternating turns.

Several games arose as variations of one introduced to us as "Chaos", based on a discrete chip-firing/sandpile process. (We were, or at least I was, astonished to learn the extent to which sandpile models appear in different branches of mathematics.)  A graph is given as the game board, and players alternate turns adding a chip to a vertex.  Between players' turns, the chip-firing process occurs: if a vertex has at least as many chips as its degree, then it sends one chip to each of its neighbors, and this repeats.  It is possible, and indeed necessary when enough chips have been played, that the chip-firing goes on forever, in which case our game ends.

I want to focus on two of the variants we studied, and we'll just stick to complete graphs for the game boards.  The two variants I'll discuss here are impartial, meaning that the two players have the same play options throughout; for complete graphs, this means we can forget the graphs and just consider the chip counts at each vertex.  (For non-complete boards, we did make similar scripts, using the graph utility `nauty`.)  Hence our game state is just a non-increasing list of integers, and the chip-firing (which we were calling "toppling") can be achieved by the following code common to both variants.

```python
def topple(G):
   ret = ""
   for i in range(len(G)):
      if G[i]>=len(G)-1:
         G[i] -= len(G)-1
         for j in range(len(G)):
            if j != i:
               G[j] += 1
   if G[0]>=len(G)-1:
      ret = "End Game"
   G.sort(reverse=True)    #could make more efficient with targeted sort...(?)
   return ret
```

## Last play wins
The first variant is won by the player who sets off the infinite chip-firing sequence.  Using the following script, we found that the first player wins on any complete graph with up to ten vertices.

```python
import sys

def winner(G, player, E):
   if G in E:
      return E[G]
   else:
      #check value of subgames:
      subgames = populate_subgames(G)
      if len(subgames)==0:
         E[G] = player
         return E[G]
      E[G]=1-player #set initial winner as NOT this player
      for option in subgames:
         if winner(option, 1-player, E)==player:
            E[G] = player
            break
      return E[G]

def populate_subgames(G):  #returns empty if there is a winning move
   subgames=set()
   H=list(G)
   for i in range(len(G)):
      if i==0 or H[i]<H[i-1]:
         #create gameboard with new token there
         newH = H[:]
         newH[i] += 1
         if topple(newH)=="End Game":
            subgames=set()
            break
         else:
            newG=tuple(newH)
            subgames.add( newG )
   return subgames

def main():
   n = int(sys.argv[1])
   G = tuple([0]*n)

   E={}
   print winner(G, 0, E)

if __name__ == '__main__':
   main()
```

There's no real reason to expect a change after ten vertices.  To try to prove that, it seemed helpful to consider the full game tree, to see what moves the first player should make.  For that we need some small tweaks throughout to save the subgames from each position, and an addition to the main function to describe the game tree in graphviz code and automatically run that to produce a graphic:

```python
import subprocess    #to run graphviz code to produce graphical game tree

def main():
   n = int(sys.argv[1])
   G = tuple([0]*n)

   E={}  #holds values
   S={}  #holds subgames
   whowins = winner(G,0,E,S)
   
   gvcode = "digraph G{"
   for graph in E:
      gvcode += '\"'+str(graph)+"\" "
      if E[graph]==1:
         gvcode += "[shape=box] "
      for child in S[graph]:
         if E[graph]==0:
            gvcode += '\"'+str(graph)+"\"->\""+str(child)+"\" "
         if E[graph]==0 and E[child]==1:
            gvcode += "[color=red] "
   gvcode += "}"
   filename="K"+str(n).zfill(2)
   f=open(filename+".gvz",'w')
   f.write(gvcode)
   f.close()

   subprocess.call(["dot","-Tpng",filename+".gvz","-o"+filename+".png"])
```

And here are the first few game trees.  (Most of the tree anyway.  Since we "knew" that player 1 should win, we cut off the subtrees rooted at a second-player-win node.  Those nodes are boxed, and the arrows going into them made red to draw attention to the first player's losing moves.)

![K3 A/B game tree]({{'/assets/K03.png'}})

![K4 A/B game tree]({{'/assets/K04.png'}})

![K5 A/B game tree]({{'/assets/K05.png'}})

[K6 A/B game tree]({{'/assets/K06.png'}}){:target="_blank"}

We did actually manage to prove that the first player wins when the number of vertices is odd.


## Max-min variant
In this variant, the two players have opposing goals: one tries to maximize the number of chips played (which is also the length of the game) while the other tries to minimize it.  Thus optimal play gives a parameter of the graph, the _game chip number_.

We cannot cut off branches when the maximizing player could end the game, so `populate_subgames` gets changed a bit (in particular, the ending state is recorded as an empty tuple).  The most relevant change is in the winner function, which now returns a score instead:

```python
def bBetter(option1, option2, player):
   return True if ((option1>option2 and player==0) or 
                     (option1<option2 and player==1)) else False 

def score(G, player, E, S):
   if G in E:
      return E[G]
   else:
      #check value of subgames:
      S[G] = populate_subgames(G)
      if ( () in S[G] ) and (player==1 or S[G]==set([()]) ): 
         #if Min can end the game or the game must end
         E[G] = sum(G)+1
         S[G] = set([()])  #if Min's turn, kill any child nodes
         return E[G]
      bestoption=(0 if player==0 else len(G)**2 ) #set initial best option as bad for player
      for option in S[G]:
         if option!=():
            newoption=score(option[:], 1-player, E, S)
            if bBetter( newoption, bestoption, player ):
               bestoption=newoption
      E[G] = bestoption
      return E[G]
```

Here are a couple of the game trees; the number in front of a position is the score when play starts from that node.

![K4 game chip number tree]({{'/assets/gcn-K04.png'}})

![K5 game chip number tree]({{'/assets/gcn-K05.png'}})

I imagine we had more data than this at some point, but here's what I have about the game chip number of the complete graph on n vertices:

| n              | 2 | 3 | 4 | 5 | 6 | 7 | 8 |
|---------------:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|
|game chip number| 1 | 4 | 8 | 12| 18| 26| 34|

Oh, and there are "easy" upper and lower bounds that we could include:

| n              | 2 | 3 | 4 | 5 | 6 | 7 | 8 |
|---------------:|:-:|:-:|:-:|:-:|:-:|:-:|:-:|
|lower bound     | 1 | 3 | 6 | 10| 15| 21| 28|
|game chip number| 1 | 4 | 8 | 12| 18| 26| 34|
|upper bound     | 1 | 4 | 9 | 16| 25| 36| 49|

The game chip number hovers (for these small n) about 1/3 of the way from the lower bound to the upper bound, but I don't see any pattern in the particular numbers; do you?

### References
[REGS page]({{'https://faculty.math.illinois.edu/~west/regs/gamechip.html'}})

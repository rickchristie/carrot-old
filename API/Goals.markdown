GOALS
-----

We must not stray from our objectives:

- A framework where you don't use globals and statics.
- A framework where the user can code in their Plain Old PHP Object.
- A framework that clearly separates the framework part (part of
  the code that imposes architectural decisions) and the library
  part (part of the code that can be reused in other architectural
  decisions).
- A framework that does not couple the user's code to the
  framework. This means if I use the framework and decided to
  switch, I don't have to take any baggage from the framework
  part of the code.
- A framework that has clean, easy to use API.
- A framework that makes it easier to write testable code.
- A framework that doesn't encourage bad practices.
- A framework that is deeply configurable, but simple to use if
  you don't want to customize it.
- A framework that has wonderful documentation.
- A framework where composition is favored over inheritance.


HOW TO REACH THEM
-----------------

# Does not use globals and statics

Use good OO-design principles. Produce good OO-design.

# User can code in Plain Old PHP Object

The framework knows the user code, but the user code is oblivious
to the framework. Clever use of dependency injection will allow
this to happen.

# Clear separation between framework and library parts

Use this mindset from the start. Classes that are libraries must
not be aware of the framework. Classes that are part of the
framework code, however, is aware of the library classes.

# Does not couple user's code to the framework

Achieving clear separation between framework and library parts.
Also, from the start, user's classes should not be made aware
of the framework part of the code. The framework part of the
code, however, is aware and calls the user code.

# Clean, easy to use API

Plan APIs from the start in a formal document. The document can
later be used to help write documentation.

# Makes it easier to write testable code.

Achieve:

- Does not couple user's code to the framework
- Does not use globals and statics
- User can code in Plain Old PHP Object

# Does not encourage bad practices

Write the documentation carefully. Suggest nothing of the sort
and do not use globals and statics.

# Deeply configurable, simple to use with no customization

//---------------------------------------------------------------
Use this mindset from the start in designing the API and OO
design. The default objects should cover 80% of the use cases.
For edge cases, the user should be able to replace our
implementations with their own or extend our implementations.

Have a layer of indirection between the framework code and
library code by using interfaces exclusively in framework code.

An example is, the user can create their own implementation of
RequestInterface, which can replace 

# Has wonderful documentation

In each documentation page, focus on:

- What the user wanted to do.
- What the user can do.

Tell the relationship of the library with the framework part of
the code.

Have recipes section where you suggest how to use them with the
framework.




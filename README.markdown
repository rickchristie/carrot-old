Carrot: Simple, Experimental PHP Framework (Unstable)
=====================================================

Carrot is yet another PHP framework. It was created as a learning
project, hence it is experimental in nature. Carrot mandates the
usage of provider classes to manage dependency injection through
its dependency injection container (DIC). Carrot's core classes
includes the front controller, exception handler, DIC, and a
default router. Carrot is still unstable, with many changes (and
features) planned.

Detailed documentation is in progress, meanwhile, please download
the source and play with it if you wanted to know more about
Carrot.

Requirements
------------

- PHP 5.3+ (Carrot uses namespaces and anonymous functions).
- Apache2 web server (Carrot is not tested in other web servers).

Framework design goals
----------------------

- Create a framework without using the keyword global or static.
  Fully recognize the dangerous nature of global state and avoid
  it at all costs.
- Relying on dependency injection container to manage the
  dependencies of user classes, thus eliminating the need for a
  global objects and registry.
- Build and make use of decoupled classes, avoid inheritance
  whenever possible. This would allow the user's routine class
  to be a plain old PHP object managed by the dependency injection
  container.
- Make the core as small and focused as possible. Carrot's main
  goal is to be the front controller, routing the request, wiring
  the dependencies of the user's classes, and setting up a
  comfortable development environment for the user.
- Continue the development by adding libraries, which are
  essentially just decoupled classes with proper namespace. Each
  library must not know that it is being used inside a framework,
  people should be able to just copy and paste the library files
  to use it.

License
-------

Copyright (c) 2011 Ricky Christie

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
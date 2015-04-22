task void foo_task() {
  foreach (i = 1 ... 1000) {
    int y = 0;
    for (int x = 0; x < i * 2; x++) {
      y++;
    }
  }
}

export void foo() {
  launch foo_task();
  int a = programIndex;
  int b = 1;
  if (a <= 0) {
    b = 0;
  } else {
    b = 2;
  }
}
